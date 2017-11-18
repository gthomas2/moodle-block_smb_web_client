<?php

// include relevant files
require_once (dirname(__FILE__).'/lib_db.php');
require_once (dirname(__FILE__).'/lib.php');

/**
 *==============================================================
 * (c) Moodle 3.3+ Block - Guy Thomas 2017
 * (c) Moodle 2.4+ Block - Guy Thomas Citricity Ltd 2013
 * (c) Moodle 2.3+ Block - Guy Thomas Citricity Ltd 2012
 * (c) Moodle 2.2 Block - Marc Coyles Ossett Academy 2012
 * Orig Author: Guy Thomas
 * (c) Moodle Block - Guy Thomas Ossett School 2007, 2008, 2009, 2010
 * (c) Original Smb Web Client - Victor M. Varela 2005
 * Title: Block Smb Web Client 2.3
 * Description:
 * This block enables users to login in to their windows shares (corresponding to their
 * ldap home dir field)
 * This block is basically a wrapper for smbwebclient developed by Victor M. Varela
 * see http://smbwebclient.sourceforge.net/
 * NOTE:  * * * Since version 2008121900 there are some custom improvements to the core
 * smbwebclient class - e.g. file extension icons by css
 *==============================================================
 */

global $smb_cfg, $CFG, $USER, $site, $share;
//@include('config_smb_web_client.php'); // config for this block only

class block_smb_web_client extends block_base {

    var $blockwww;
    var $blockdir;
    var $gcfg; // global config

    function init() {
        global $CFG, $smb_cfg;
        $this->gcfg=get_config('block_smb_web_client');

        $title=get_config('smb_web_client', 'customtitle');
        if (empty($title)){
            $title=get_string('pluginname', 'block_smb_web_client');
        }



        // Set title and version
        $this->title = $title == "[[blockmenutitle]]" ? "Windows Share Web Client" : $title;

	// set block dir
	$this->blockdir=$CFG->dirroot.'/blocks/smb_web_client';
        // set block www
        $this->blockwww=$CFG->wwwroot.'/blocks/smb_web_client';
    }

    function get_content() {
        global $CFG, $USER, $COURSE, $DB, $smb_cfg;
        include('config_smb_web_client.php'); // config for this block only
        // return content if allready set
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = '';

        //No config warning
        if (!isset($smb_cfg)){
            // GT mod 2008/12/19 - report different error if file exists but config variable not set
            if (file_exists($this->blockdir.'/config_smb_web_client.php')){
                $this->content->text=notify('Configuration for this block has errors!','notifyproblem','center',true);
            } else {
                $this->content->text=notify('Configuration for this block has not been completed!','notifyproblem','center',true);
            }
            return;
        }

        // Jon Witts Mod 2009060900: if ssl has not been explicitly set, then check Moodle config for loginhttps and set accordingly
        if (!isset($smb_cfg->cfgssl) && (isset($CFG->loginhttps) && $CFG->loginhttps)){
                $smb_cfg->cfgssl=true;
        }

        //EMC mod to only show for logged in users!
        if (!isloggedin() || isguestuser()) {
            return false;
        }
        //END EMC mod

        $sharelinks='';

        // GT Mod - 2008091500
        if ($USER->auth!='manual'){
            // get home directory string from language file
            $homedirstr=get_string('homedir', 'block_smb_web_client');
            $shareurl=$this->blockwww.'/smbwebclient_moodle.php?sesskey='.$USER->sesskey.'&amp;share=__home__';

            // modify shareurl to use ssl if necessary
            if (isset($smb_cfg->cfgssl) && $smb_cfg->cfgssl){
                    $shareurl=str_ireplace('http://', 'https://', $shareurl);
            }

            // add home directory link to block content
            $sharelinks = '<li style="list-style:none;" class="share share-home"><a id="block_smb_web_client_home" href="'.$shareurl.'" target="_blank" onclick="window.open(\''.$shareurl.'\',\''.$this->nice_popup_title($homedirstr).'\',\'width=640,height=480, scrollbars=1, resizable=1\'); return false;">'.$homedirstr.'</a></li>';
        }
        // END GT Mod

        // GT MOD 2013012500 new share restriction filering code
        if (isset($smb_cfg->cfgWinShares) && !empty($smb_cfg->cfgWinShares)){
            foreach ($smb_cfg->cfgWinShares as $share_arr) {
                if (swc::can_show_share($share_arr)){
                    $sharelinks.=$this->sharelink($share_arr);
                } else {
                    // an admin user shouldn't be able to open this share but lets be kind and let them know it exists
                    if (has_capability('block/smb_web_client:addinstance', get_system_context())){
                        // @todo lang string for title
                        $sharelinks.='<li class="share share-no-access" title="non-accessible share, you can administrate this block but the config forbids you from seeing this share- see block config" style="list-style:none;">'.$share_arr['title'].'</li>';
                    }
                }
            }
        }



        // GT MOD 2013060600 - add links to shares correctly!!!!
        if (!empty($sharelinks)){
            $sharelinks='<ul class="sharelinks">'.$sharelinks.'</ul>';
        }

        $this->content->text=$sharelinks;

        // end new code block for shares - JWI
        return $this->content;
    }


    /**
     * get share link string
     * @global stdClass $CFG
     * @global stdClass $USER
     * @global stdClass $smb_cfg
     * @param array $share_arr
     * @return string
     */
    private function sharelink($share_arr){
        global $CFG, $USER, $smb_cfg;
        $sharekey=$share_arr['id'];
        $shareurl=$this->blockwww.'/smbwebclient_moodle.php?sesskey='.$USER->sesskey.'&amp;share='.$sharekey;

        // GT Mod 2009031700 force https protocol if necessary
        if (isset($smb_cfg->cfgssl) && $smb_cfg->cfgssl){
                $shareurl=str_ireplace('http://', 'https://', $shareurl);
        }

        return('<li style="list-style:none;" class="share"><a id="block_smb_web_client_'.$sharekey.'" href="'.$shareurl.'" onclick="window.open(\''.$shareurl.'\',\''.$this->nice_popup_title($share_arr['title']).'\',\'width=640,height=480, scrollbars=1, resizable=1\'); return false;">'.$share_arr['title'].'</a></li>');
    }

    /**
    * Converts a string to a ie popup title friendly string
    * @param required $str - the title you want to make friendly to ie
    */
    private function nice_popup_title($str){
    	$bannedChars=array(" ", "*", "{", "}", "(", ")", "<", ">", "[", "]", "=", "+", "\"", "\\", "/", ",",".",":",";");

    	foreach ($bannedChars as $banned){
    		$str=str_replace($banned,"_", $str);
    	}

    	return ($str);

    }

    function has_config(){
        return true;
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
            $info=$ap_ldap->ldap_get_entries($ldapconnection, $sr);
        } else {
            return (false);
        }
        return ($info);
    }

}

?>
