<?php

global $CFG;

include_once($CFG->dirroot.'/lib/moodlelib.php');
include_once($CFG->dirroot.'/lib/datalib.php'); // needed for logging
@include_once($CFG->dirroot.'/auth/ldapcapture/aes_encryption.php');

class smbwebclient_moodle extends smbwebclient {

    var $cfgBaseUrl; // set base url to block
    var $cfgWinShareToSmb; // share format translator - converts from windows share to smbclient share
    var $cfgWinShares=array(); // non home directory shares that this user can access
    var $cfgAllSharesSite = true;  // if set to true then display all shares on site page providing user has access to any course specific shares
    var $cfgUserPrefix = ''; // default prefix for all usernames - useful for set-ups where domain controller is on another server to storage (you need to prefix with domain/)
    var $cfgForceDownloads = true; // if true then force all network files to download
    var $cfgMaxFolderZipSizeMB = 200; // maximum zip size in MBs for downloading folders as zips - default if not set is 200MB
    var $cfgProtocol = ''; // Protocol to be used with -m arg
    var $criticalError;

    function __construct(){
        global $smb_cfg, $CFG, $SESSION;

        // Jon Witts Mod 2009060900: if ssl has not been explicitly set, then check Moodle config for loginhttps and set accordingly
        if (!isset($smb_cfg->cfgssl) && (isset($CFG->loginhttps) && $CFG->loginhttps)){
                $smb_cfg->cfgssl=true;
        }

        // GT Mod 2009080300 - convert deprecated config setting forceDownloads to cfgForceDownloads
        if (isset($smb_cfg->forceDownloads) && !isset($smb_cfg->cfgForceDownloads)){
                $smb_cfg->cfgForceDownloads=$smb_cfg->forceDownloads;
        }

        $this->criticalError=false;

        // set config
        $this->_setstatic_configvars();
        $this->_setdynamic_configvars();

        // GT Mod 2009080300 - make sure max folder zip size is numeric
        if (!is_float($this->cfgMaxFolderZipSizeMB) && !is_int($this->cfgMaxFolderZipSizeMB)){
                $this->cfgMaxFolderZipSizeMB=floatval($this->cfgMaxFolderZipSizeMB);
        }

        // call parent constructor
        parent::__construct();

        // set username and login if the user has an encrypted version of their password available (i.e. ldapcapture authentication plugin is installed).
        if (isset($SESSION->epassword) && $SESSION->epassword!=''){
            if (class_exists('LDC_AesCtr')){
                $this->setauth();
            }
        }
    }

    /**
     * override smbwebclient debugging function with function that logs to Moodle logs
     * (non-PHPdoc)
     * @see blocks/smb_web_client/smbwebclient#Debug($message, $level)
     */
	 /*
    function Debug ($message, $level=0)
    {
        if ($level <= $this->debug) {
            foreach (preg_split('/\n/', $message) as $line) { // Fixed deprecated split function by Jon Witts 2010090600
                if (trim($line)!=''){
                    add_to_log(1, 'smb_webclient', 'view', '', str_replace("'", "\'", $line)); // fix 2012-12-14 escape log line
                }
            }
        }
    }
    */

    function setauth () {
        global $USER, $SESSION;

        // get ldap config
        $cfg_ldap = get_config('auth/ldap');

        // create key to decrypt password
        $key=md5($cfg_ldap->mcryptkey);

        // decrypt password
        $dpassword=LDC_AesCtr::decrypt($SESSION->epassword, $key, 256);

        // set user properties for this class
        $this->user=$USER->username;
        $this->pw=$dpassword;

        $mode=$this->type;

        $_SESSION['swcCachedAuth'][$mode][$this->$mode]['User'] = $this->user;
        $_SESSION['swcCachedAuth'][$mode][$this->$mode]['Password'] = $this->pw;

    }


    /**
    * Override GetAuth- don't bother authenticating if encrypted password available
    */
    function GetAuth ($command){

        global $USER, $SESSION;

        // Only try this if authentication has not been requested
        if (@$_GET['auth']!='1'){
            if (isset($SESSION->epassword) && $SESSION->epassword!='' && class_exists('LDC_AesCtr')){
                $this->setauth();
                return (true);
            }
        }

        return (parent::GetAuth($command));
    }

    /**
    * Set static config variables
    */
    private function _setstatic_configvars(){
        global $smb_cfg, $CFG, $USER;
        // set static config variables (from config file var $smb_cfg)
        $confvars=get_object_vars($smb_cfg);
        foreach ($confvars as $key=>$var){
            $this->$key=$var;
        }
    }

    /**
    * Set dynamic config variables
    */
    private function _setdynamic_configvars(){
        global $smb_cfg, $CFG, $USER;

        // set dynamic config variables
        $this->cfgBaseUrl = $CFG->wwwroot.'/blocks/smb_web_client';

        // GT Mod 2009031700 force https protocol if necessary
        if (isset($this->cfgssl) && $this->cfgssl){
            $this->cfgBaseUrl=str_ireplace('http://', 'https://', $this->cfgBaseUrl);
        }

        $shareid=required_param('share',PARAM_ALPHANUMEXT);


        #####################################
        # new code block for shares JWI
        #####################################
        if ( $shareid == '__home__' ) {

            // Configure home directory field
            if (isset($smb_cfg->cfgHomeDirField) && $smb_cfg->cfgHomeDirField!=''){
                $homedirfld=$smb_cfg->cfgHomeDirField;
            } else {
                $homedirfld='homeDirectory';
            }

            // get user ldap object
            $ldapObj=$this->_getldap_entry($USER->username);

            $homedirfld = isset($ldapObj[0][$homedirfld]) ?: strtolower($homedirfld);

            if (!isset($ldapObj[0][$homedirfld][0])){
                $this->criticalError='No home directory specified in ldap for this user';
            }

            // set smbroot to user home dir
            $this->cfgSambaRoot=$ldapObj[0][$homedirfld][0];

            // Convert all escaped backslashes to forward slashes
            $this->cfgSambaRoot=str_replace('\\', '/', $this->cfgSambaRoot);

            // GT MOD - 2013101500 - Allow to do a search and replace on sambaRoot
            if (!empty($smb_cfg->cfgHomeDirSrch) && !empty($smb_cfg->cfgHomeDirRep)){
                // we also need to convert back slashes to forward slashes in search and replace strings
                $smb_cfg->cfgHomeDirSrch=str_ireplace('\\', '/', $smb_cfg->cfgHomeDirSrch);
                $smb_cfg->cfgHomeDirRep=str_ireplace('\\', '/', $smb_cfg->cfgHomeDirRep);
                // do the search and replace on the home directory
                $this->cfgSambaRoot=str_ireplace($smb_cfg->cfgHomeDirSrch, $smb_cfg->cfgHomeDirRep, $this->cfgSambaRoot);
            }

            // MC MOD - 2012020700 - Allow for traversal into a subfolder within specified HomeDir field retrieved from AD if
            // $smb_cfg->cfgHomeTraverse=true and $smb_cfg->cfgHomeDirSubFolder='\foldername' set within config_smb_web_client.php
            if ($smb_cfg->cfgHomeTraverse){
                $this->cfgSambaRoot=$this->cfgSambaRoot.$smb_cfg->cfgHomeDirSubFolder;
            }

        } else {

            // set smbroot to share
            if (isset($smb_cfg->cfgWinShares[$shareid])){
                $this->cfgSambaRoot=$smb_cfg->cfgWinShares[$shareid]['share'];
            }

            // Convert all escaped backslashes to forward slashes
            $this->cfgSambaRoot=str_replace('\\', '/', $this->cfgSambaRoot);
        }


        #####################################
        # end new code block for shares JWI
        #####################################




    }

    /**
    * Get ldap entry by user name
    * @param required $username
    */
    private function _getldap_entry($username){
        $ap_ldap=new auth_plugin_ldap();
        $ldapconnection=$ap_ldap->ldap_connect();
        $user_dn = $ap_ldap->ldap_find_userdn($ldapconnection, $username);
        $sr = ldap_read($ldapconnection, $user_dn, 'objectclass=*', array());
        if ($sr)  {
            //$info=$ap_ldap->ldap_get_entries($ldapconnection, $sr);
			$info = ldap_get_entries_moodle($ldapconnection, $sr);
        } else {
            return (false);
        }
        return ($info);
    }

    /**
    * PARENT CLASS OVERRIDE - HTML page
    */
    function Page ($title='', $content=''){
        global $CFG, $OUTPUT;
    	if (@$_SESSION['swcErrorMessage'] <> '') {
    		$content .= "\n<script language=\"Javascript\">alert(\"{$_SESSION['swcErrorMessage']}\")</script>\n";
    		$_SESSION['swcErrorMessage'] = '';
    	}
        // GT Mod 2011081000 set folder image instead of pix path
        $folderimg=$OUTPUT->image_url('f/folder');
        // GT Mod 2009031700 force https protocol on pix path if necessary
        if (isset($this->cfgssl) && $this->cfgssl){
            $folderimg=str_ireplace('http://', 'https://', $folderimg);
        }

    	return $this->Template('style/page.thtml', array(
            '{folderimg}' =>$folderimg,
            '{title}' => $title,
            '{charset}' => $this->cfgDefaultCharset,
            '{content}' => $content,
            '{style}' => $this->GetUrl('style/'),
            '{baseurl}'=> $this->cfgBaseUrl,
            '{favicon}' => $this->GetUrl('style/favicon.ico')
    	));
    }


}
?>
