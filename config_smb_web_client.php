<?php

###################################################################
# THERE SHOULD BE NO NEED TO MAKE CHANGES WITHIN THIS FILE        #
# ALL NECESSARY SETTINGS HAVE BEEN MOVED TO                       #
# SITE ADMINISTRATION > PLUGINS > BLOCKS > NETWORK DRIVE ACCESS   #
###################################################################

global $DB;
$smb_cfg=new stdClass;

# Log facility 

$smb_cfg->cfgFacility = LOG_DAEMON;

###################################################################
# User authentication (BasicAuth or FormAuth)
#
$smb_cfg->cfgUserAuth = 'FormAuth';

###################################################################
# Change PHP session name ('' to use default session name)SMBWebClientID
#
$smb_cfg->cfgSessionName = 'SMBWebClientID';

###################################################################
# Path at web server to store downloaded files. This script will
# check when it need to update the cached file. This path must be
# writable to the user that runs your web server.
# If you set this value to '' cache will be disabled.
# Note: this feature is a security risk.

$smb_cfg->cfgCachePath = '';

###################################################################
# This script try to set language from web browser. If browser
# language is not supported you can set a default language.

$smb_cfg->cfgDefaultLanguage = 'en';

###################################################################
# Default charset (as suggested by Norbert Malecki)

$smb_cfg->cfgDefaultCharset = 'ISO-8859-1';

###################################################################
# Default browse server for your network. A browse server is where
# you run smbclient -L subcommand to read available domains and/or
# workgroups. Set to 'localhost' if you are running SAMBA server
# in your web server. Maybe you will need cfgDefaultUser and
# cfgDefaultPassword if no anonymous browsing is allowed.

$smb_cfg->cfgDefaultServer = 'localhost';

###################################################################
# Format to upload compressed folders: tar, tgz or zip 
#
# $smb_cfg->cfgArchiver = 'tgz';

$smb_cfg->cfgArchiver = 'zip';

# inline files (included using base64_encode PHP function)

$smb_cfg->cfgInlineFiles = false;

#########################################################################
## YOU SHOULDN'T NEED TO CHANGE ANYTHING IN THIS FILE BEYOND THIS POINT##
#########################################################################

$smb_cfg->cfgWebserverType=get_config('smb_web_client', 'webos');

###################################################################
# String to prefix username - this is useful if you have your
# shares on a different server to your domain controller and your
# usernames need to be passed as domain/username

$upfx=get_config('smb_web_client', 'userprefix');
if (!empty($upfx)){
    $smb_cfg->cfgUserPrefix=$upfx;
}

###################################################################
# Arrays of shares ( other than home directory ) and the courses 
# they can be shown in.  This is to allow administrators to put 
# access to certain shares in certain courses only

$smb_cfg->cfgWinShares=array();
$shares=get_config('smb_web_client', 'shares');

$id=0;
if (!empty($shares)){
    $sharearr=explode("\n", $shares);
    foreach ($sharearr as $sharerow){
        // parse sharerow
        $tmparr=explode('|', $sharerow);

        if (count($tmparr)>1){
            $title = trim($tmparr[0]);
            $shareitem = trim($tmparr[1]);
        } else {
            $tmparr = explode('\\', $sharerow);
            $title = trim(end($tmparr));
            $shareitem = trim($sharerow);
        }

        $tokens=array('courses'=>':CS','cohorts'=>':CH','roles'=>':R');
        $filters=array('courses'=>array(), 'cohorts'=>array(), 'roles'=>array());

        $tokenstr=false;
        foreach ($tokens as $key=>$token){
            if (!$tokenstr){
                //echo ('<p>checking for filter token '.$token.' in '.$shareitem.'</p>');
                if (strpos($shareitem, $token)===false){
                    continue;
                } else {
                    //echo ('<p>token '.$token.' exists</p>');
                }
                $tmparr2=explode($token, $shareitem);
            } else {
                if (strpos($tokenstr, $token)===false){
                    continue;
                }
                $tmparr2=explode($token, $tokenstr);
            }

            //echo ('<p>found key '.$key.' and token '.$token.'</p>');

            if (count($tmparr2)>1){
                if (!$tokenstr){
                    $shareitem=$tmparr2[0];
                }
                $tokenstr=$tmparr2[1];
                if (strpos($tokenstr,'[')===false || strpos($tokenstr,']')===false || strpos($tokenstr,']') < strpos($tokenstr,'[')){
                    // can't do anything with this string as it does not have []
                    continue;
                }

                /*
                var_dump(strpos($tokenstr,'[')); 
                var_dump(strpos($tokenstr,']'));
                 */

                $tokenstr=substr($tokenstr, strpos($tokenstr,'[')+1, strpos($tokenstr,']')-1);

                $tmparr3=explode(',',str_replace('[','',str_replace(']','',$tokenstr)));
                foreach ($tmparr3 as &$ta3v){
                    $ta3v=trim($ta3v);
                }
                $filters[$key]=$tmparr3;
                // reset tokenstr
                $tokenstr=false;
            }
        }

        //var_dump($filters);

        // remove trailing comma from shareitem
        $shareitem=trim($shareitem);
        if (substr($shareitem,strlen($shareitem)-1)==','){
            $shareitem=substr($shareitem,0,strlen($shareitem)-1);
        }

        $shrobj=array('id'=>$id, 'share'=>trim($shareitem), 'title'=>$title, 'filters'=>$filters);
        $smb_cfg->cfgWinShares[$id]=$shrobj;
        $id++;
    }
}

$smb_cfg->cfgHomeDirSrch = get_config('smb_web_client', 'homedirsharesrch');
$smb_cfg->cfgHomeDirRep = get_config('smb_web_client', 'homedirsharerep');


// if set to true then display all shares on site page providing user has access to any course specific shares
$smb_cfg->cfgAllSharesSite = get_config('smb_web_client', 'allsharessite');

###################################################################
# Anonymoys login is disallowed by default.
# If you have public shares in your network (ie you want absolutely everyone (the whole world) to access it) then turn on this flag
# i.e. $smb_cfg->cfgAnonymous = true;

$smb_cfg->cfgAnonymous = get_config('smb_web_client', 'anonymous');

###################################################################
# GT MOD 2008-06-19
# Force all files to download instead of trying to open in browser, etc.
# NOTE: This variable used to be $smb_cfg->forceDownloads

$smb_cfg->cfgForceDownloads = get_config('smb_web_client', 'forcedl');

###################################################################
# GT MOD 2009-08-03
# Maximum zip size in MBs for downloading folders as zips

$smb_cfg->cfgMaxFolderZipSizeMB=get_config('smb_web_client', 'zipmax');

###################################################################
# Path to smbclient program.
# i.e. $smb_cfg->cfgSmbClient = '/usr/bin/smbclient';   (for Linux)
# i.e. $smb_cfg->cfgSmbClient = '/usr/local/bin/smbclient'; (for FreeBSD)
# 
# Ensure that this is correct...! Don't rely on it being correct.
# Establish the correct path for your SMBClient binary and stick it below.

$smb_cfg->cfgSmbClient = get_config('smb_web_client', 'smbclient');



###################################################################
# Authentication method with smbclient
# 'SMB_AUTH_ENV' USER environment variable (more secure)
# 'SMB_AUTH_ARG' smbclient -U param
//$smb_cfg->cfgAuthMode = 'SMB_AUTH_ARG';
$smb_cfg->cfgAuthMode = get_config('smb_web_client', 'auth');

###################################################################
# If you have Apache mod_rewrite installed you can put this
# .htaccess file in same path of smbwebclient.php:
#
#  <IfModule mod_rewrite.c>
#   RewriteEngine on
#   RewriteCond    %{REQUEST_FILENAME}  -d
#   RewriteRule ^(.*/[^\./]*[^/])$ $1/
#   RewriteRule ^(.*)$ smbwebclient.php?path=$1 [QSA,L]
#  </IfModule>
#
# Then you will be able to access to use "pretty" URLs
# i.e: http://server/windows-network/DOMAIN/SERVER/SHARE/PATH
#
# To do this, all you have to set is cfgBaseUrl (*GT Mod- BaseUrl is set automatically by class_smbwebclient_moodle.php) and set
# cfgModRewrite = true
# (i.e. http://server/windows-network/)
#
# Note - Change this if you want to use mod_rewrite

$smb_cfg->cfgModRewrite = get_config('smb_web_client', 'mod_rewrite');

###################################################################
# Do not show dot files (like .cshrc)
#

$smb_cfg->cfgHideDotFiles = get_config('smb_web_client', 'dotfiles');

###################################################################
# Do not show system shared resources (like admin$ or C$)
#

$smb_cfg->cfgHideSystemShares = get_config('smb_web_client', 'adminshares');

###################################################################
# Do not show printer resources
#

$smb_cfg->cfgHidePrinterShares = get_config('smb_web_client', 'prn');

###################################################################
# Log level
# -1 = no messages
#  0 = log actions performed
#  1 = smbclient calls
# >1 = smbclient output
#

$smb_cfg->cfgLogLevel = get_config('smb_web_client', 'log');

###################################################################
# Virus scanner to upload files -- suggested by Bill R <wjries@hotmail.com>
# Only ClamAV is available in this revision, set to false to
# disable virus scanning.
#
# $smb_cfg->cfgAntivirus = 'ClamAV';

$smb_cfg->cfgAntivirus = get_config('smb_web_client', 'av');

###################################################################
# If above set to 'ClamAV', use clamscan or clamdscan? 
# clamdscan is quicker by a significant margin and if exists, should
# be used in preference over clamscan. Both should be located in
# /usr/bin/
# $smb_cfg->clamdaemon = true; //clamdscan
# $smb_cfg->clamdaemon = false; //clamscan

###################################################################
# Enable specific field to be used to retrieve home directory
# Default = homeDirectory (standard for AD)

$smb_cfg->cfgHomeDirField = get_config('smb_web_client', 'homef');

###################################################################
# If you need to traverse into a SUBFOLDER within the folder 
# home directory specified in AD to get to their documents, 
# set the below to true, and specify the folder name 
# in $smb_cfg->cfgHomeDirSubFolder

$smb_cfg->cfgHomeTraverse = get_config('smb_web_client', 'homet');

$smb_cfg->cfgHomeDirSubFolder = get_config('smb_web_client', 'hometloc');

###################################################################
# IE users have a problem when selecting 'open' for office files
# It basically will open the file in 'temporary internet files'
# and the user will possibly then save the file to this folder.
# If you set the following config variable to true then IE users
# will be forced to right hand click and save target for office
# documents.

$smb_cfg->cfgIEProtectMsOffice=get_config('smb_web_client', 'office');

###################################################################
# Forces https protocol if set to true

$smb_cfg->cfgssl=get_config('smb_web_client', 'ssl');

###################################################################
# Skips -N command line parameter (don't prompt for password)
# UBUNTU IBEX 8.10 - You must set this paremeter to true if you are
# Using UBUNTU 8.10.
# This is due to smbclient bugs with Ubuntu 8.10
# Also note- there are other problems with Ubuntu 8.10 that affect
# smbclient, the biggest being that out of the box it won't work
# with shares using domain names, it will only work with IPs
# To fix this see the following url:
# http://ubuntuforums.org/archive/index.php/t-909020.html
# OR - use Ubuntu 8.4 instead (works OK out of the box)

if (get_config('smb_web_client', 'webos')=='windows'){
    $smb_cfg->cfgSkipNoPwdParam = false;
} else {
    $smb_cfg->cfgSkipNoPwdParam = true;
}
?>
