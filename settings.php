<?php
defined('MOODLE_INTERNAL') || die;

include_once('settingslib.php');

if ($ADMIN->fulltree) {
	
    if ($PAGE->pagetype=="admin-setting-blocksettingsmb_web_client"){
        
        
        
        $jsmodule = array(
            'name'     => 'swc_config',
            'fullpath' => '/blocks/smb_web_client/js/swc_config.js',
            'requires' => array('base', 'node', 'array', 'datatable-datasource', 'datasource-function','datasource-jsonschema', 'panel'),
            'strings' => array(
                array('add', 'moodle'),
                array('edit', 'moodle'),
                array('update', 'moodle'),
                array('jsintfilter', 'block_smb_web_client')
            )
        );

        $PAGE->requires->js_init_call('M.swc_config.init', array(array('blockwww'=>$CFG->wwwroot.'/blocks/smb_web_client/')), true, $jsmodule);        

        
    }
    
    
$settings->add(new admin_raw_html(
           'hd_sponsor', '<div style="background-color:#ffa; padding:1em 1em 1em 1em; border-radius:6px; border:4px solid #aa4;"><img style="margin-bottom:1em" src="http://www.overnetdata.com/wp-content/themes/overnet/images/overnet-data-logo.png" alt="sponsorlogo" /><p>'.get_string('sponsor', 'block_smb_web_client').'</p></div>', '',''
        ));    
    
$settings->add(new admin_setting_configtext(
            'smb_web_client/customtitle',
            get_string('labelcustomtitle', 'block_smb_web_client'),
            get_string('desccustomtitle', 'block_smb_web_client'),
            ''
        ));    
    
$settings->add(new admin_setting_heading(
           'hd_generalsettings', get_string('generalsettings', 'block_smb_web_client'), ''
        ));    
$options = array('linux'=>get_string('linux', 'block_smb_web_client'),'windows'=>get_string('windows', 'block_smb_web_client'));
$settings->add(new admin_setting_configselect(
            'smb_web_client/webos',
            get_string('labelwebos', 'block_smb_web_client'),
            get_string('descwebos', 'block_smb_web_client'),
            'linux',
			$options
        ));	
$settings->add(new admin_setting_configtext(
            'smb_web_client/smbclient',
            get_string('labelsmbclient', 'block_smb_web_client'),
            get_string('descsmbclient', 'block_smb_web_client'),
            '/usr/bin/smbclient'
        ));


$settings->add(new admin_setting_configtextarea(
           'smb_web_client/shares', get_string('shares', 'block_smb_web_client'), get_string('shareformats', 'block_smb_web_client'),''
        
        ));


// gt mod 2013101500
$settings->add(new admin_setting_heading(
           'hd_homedirshare', get_string('homedirshare', 'block_smb_web_client'),''
        )); 


$settings->add(new admin_setting_configtext(
            'smb_web_client/homedirsharesrch',
            get_string('labelhomedirsharesrch', 'block_smb_web_client'),
            get_string('deschomedirsharesrch', 'block_smb_web_client'),
            ''
        ));

$settings->add(new admin_setting_configtext(
            'smb_web_client/homedirsharerep',
            get_string('labelhomedirsharerep', 'block_smb_web_client'),
            get_string('deschomedirsharerep', 'block_smb_web_client'),
            ''
        ));

$settings->add(new admin_setting_heading(
           'hd_advancedsettings', get_string('advancedsettings', 'block_smb_web_client'),''
        )); 

$options = array(''=>get_string('noav', 'block_smb_web_client'),'ClamAV'=>get_string('clamav', 'block_smb_web_client'));
$settings->add(new admin_setting_configselect(
            'smb_web_client/av',
            get_string('labelav', 'block_smb_web_client'),
            get_string('descav', 'block_smb_web_client'),
            0,
			$options
        ));

$settings->add(new admin_setting_configtext(
            'smb_web_client/userprefix',
            get_string('labeluserprefix', 'block_smb_web_client'),
            get_string('descuserprefix', 'block_smb_web_client'),
            ''
        ));

$settings->add(new admin_setting_configtext(
            'smb_web_client/homef',
            get_string('labelhomef', 'block_smb_web_client'),
            get_string('deschomef', 'block_smb_web_client'),
            ''
        ));			
$settings->add(new admin_setting_configcheckbox(
            'smb_web_client/homet',
            get_string('labelhomet', 'block_smb_web_client'),
            get_string('deschomet', 'block_smb_web_client'),
            0
        ));
$settings->add(new admin_setting_configtext(
            'smb_web_client/hometloc',
            get_string('labelhometloc', 'block_smb_web_client'),
            get_string('deschometloc', 'block_smb_web_client'),
            ''
        ));				 
$settings->add(new admin_setting_configcheckbox(
            'smb_web_client/allsharessite',
            get_string('labelallsharessite', 'block_smb_web_client'),
            get_string('descallsharessite', 'block_smb_web_client'),
            1
        ));
$settings->add(new admin_setting_configcheckbox(
            'smb_web_client/mod_rewrite',
            get_string('labelmodrewrite', 'block_smb_web_client'),
            get_string('descmodrewrite', 'block_smb_web_client'),
            0
        ));

$settings->add(new admin_setting_configcheckbox(
            'smb_web_client/anonymous',
            get_string('labelanonymous', 'block_smb_web_client'),
            get_string('descanonymous', 'block_smb_web_client'),
            0
        ));		
$settings->add(new admin_setting_configcheckbox(
            'smb_web_client/dotfiles',
            get_string('labeldotfiles', 'block_smb_web_client'),
            get_string('descdotfiles', 'block_smb_web_client'),
            1
        ));
$settings->add(new admin_setting_configcheckbox(
            'smb_web_client/adminshares',
            get_string('labeladminshares', 'block_smb_web_client'),
            get_string('descadminshares', 'block_smb_web_client'),
            1
        ));		
$settings->add(new admin_setting_configcheckbox(
            'smb_web_client/forcedl',
            get_string('labelforcedl', 'block_smb_web_client'),
            get_string('descforcedl', 'block_smb_web_client'),
            1
        ));	
$settings->add(new admin_setting_configtext(
            'smb_web_client/zipmax',
            get_string('labelzipmax', 'block_smb_web_client'),
            get_string('desczipmax', 'block_smb_web_client'),
            0
        ));
$options = array('SMB_AUTH_ARG'=>get_string('arg', 'block_smb_web_client'),'SMB_AUTH_ENV'=>get_string('env', 'block_smb_web_client'),'SMB_AUTH_ARG_WIN'=>get_string('winarg', 'block_smb_web_client'));
$settings->add(new admin_setting_configselect(
            'smb_web_client/auth',
            get_string('labelauth', 'block_smb_web_client'),
            get_string('descauth', 'block_smb_web_client'),
            'SMB_AUTH_ARG',
			$options
        ));		
$settings->add(new admin_setting_configcheckbox(
            'smb_web_client/prn',
            get_string('labelprn', 'block_smb_web_client'),
            get_string('descprn', 'block_smb_web_client'),
            1
        ));							
$options = array(-1=>get_string('nomsg', 'block_smb_web_client'),0=>get_string('logact', 'block_smb_web_client'),1=>get_string('smbcall', 'block_smb_web_client'),2=>get_string('logfull', 'block_smb_web_client'),3=>get_string('logbtrace','block_smb_web_client'));
$settings->add(new admin_setting_configselect(
            'smb_web_client/log',
            get_string('labellog', 'block_smb_web_client'),
            get_string('desclog', 'block_smb_web_client'),
            0,
			$options
        ));	
$settings->add(new admin_setting_configcheckbox(
            'smb_web_client/office',
            get_string('labeloffice', 'block_smb_web_client'),
            get_string('descoffice', 'block_smb_web_client'),
            1
        ));	
$settings->add(new admin_setting_configcheckbox(
            'smb_web_client/ssl',
            get_string('labelssl', 'block_smb_web_client'),
            get_string('descssl', 'block_smb_web_client'),
            1
        ));	
}
?>