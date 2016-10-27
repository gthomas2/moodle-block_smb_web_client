<?php
$string['smb_web_client:addinstance']='Add SMB web client instance';
$string['homedir'] = 'Home Directory';
$string['pluginname'] = 'Windows Share access';

$string['sponsor']='Development of this release (2013101400) is sponsored by Overnet Data Ltd <a href="http://www.overnetdata.com">www.overnetdata.com</a>';

// Log on error
$string['logonerror']='Log on error - click here to re-enter credentials.';

/* Admin Settings */
$string['generalsettings'] = 'General Settings';
$string['advancedsettings'] = 'Advanced Settings';
$string['manageservers'] = 'Windows Share access > Manage Servers';
$string['manageshares'] = 'Windows Share access > Manage Shared Folders';
$string['homedirshare']='Home Directory Share';
$string['false'] = 'false';
$string['true'] = 'true';
$string['labeluserprefix']='Username Prefix';
$string['descuserprefix']='If your file server is separate from your domain server its likely that all usernames will require the domain prefix to login and access shares - e.g. CITRICITY/guy intead of just guy - in this case you would need a prefix of CITRICITY/';
$string['labelmodrewrite'] = 'Enable \'mod_rewrite\' functionality?';
$string['descmodrewrite'] = 'If you have Apache mod_rewrite installed you can enable this setting and put the following within a .htaccess file within this block\'s root:
<pre>
&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine on
    RewriteCond    %{REQUEST_FILENAME}  -d
    RewriteRule ^(.*/[^\./]*[^/])$ $1/#   RewriteRule ^(.*)$ smbwebclient.php?path=$1 [QSA,L]
&lt;/IfModule&gt;</pre>

Then you will be able to access using "pretty" URLs (ie: http://server/windows-network/DOMAIN/SERVER/SHARE/PATH)';
$string['labeldotfiles'] = 'Hide hidden files';
$string['descdotfiles'] = 'Sets whether users can view files preceeded with a . (Unix file hiding)';
$string['labeladminshares'] = 'Hide Admin Shares';
$string['descadminshares'] = 'Sets whether system-wide shares (eg: windows shares suffixed with $) are shown';
$string['labelsmbclient'] = 'SMBClient Location';
$string['descsmbclient'] = 'Set the path to your smbclient binary.<br />
ie: /usr/bin/smbclient (for Linux), /usr/local/bin/smbclient (for FreeBSD), C:\cygwin\bin\smbclient (for Windows)';
$string['labelallsharessite'] = 'Display All Shares on Site Page';
$string['descallsharessite'] = 'If set, all shares are displayed throughout the site, otherwise shares are only displayed when the user is within the courses to which the share is allocated.';
$string['labelanonymous'] = 'Allow anonymous login';
$string['descanonymous'] = 'If you have public shares in your network (ie: you want absolutely everyone - *the whole world* - to access it) then tick this *[NB: Not recommended if Moodle Guest access is enabled]*';
$string['labelforcedl'] = 'Force downloads';
$string['descforcedl'] = 'Force all files to be downloaded instead of trying to open within browser';
$string['linux'] = 'Linux';
$string['windows'] = 'Windows';
$string['labelwebos'] = 'Webserver OS';
$string['descwebos'] = 'Webserver Operating System - either Linux or Windows. (If BSD or other *NIX OS, use \'Linux\')';
$string['labelzipmax'] = 'Max Zip Size (Mb)';
$string['desczipmax'] = 'When downloading a folder as a Zip file, impose a size maximum in Mb as set above. (0 = No limit)';
$string['labelauth'] = 'Authentication Method';
$string['descauth'] = 'Sets whether SMBClient uses the USER OS environment variable (more secure) or passes the username via smbclient -U parameter';
$string['arg'] = 'Argument';
$string['env'] = 'Environment';
$string['winarg'] = 'Argument (Win Only)';
$string['labelprn'] = 'Hide Printer Shares';
$string['descprn'] = 'If ticked, printer shares are hidden, otherwise they are shown';
$string['nomsg'] = 'No logging';
$string['logact'] = 'Basic Actions';
$string['smbcall'] = 'SMBClient Calls';
$string['logfull'] = 'Log ALL Output';
$string['logbtrace']= 'Log ALL Output and PHP backtrace';
$string['labellog'] = 'Logging Level';
$string['desclog'] = 'Sets logging level for Debug purposes (logs written to OS-level default log daemon - with Linux this is normally /var/log/syslog)';
$string['noav'] = 'No AntiVirus checking';
$string['clamav'] = 'Use ClamAV';
$string['labelav'] = 'AntiVirus Checking';
$string['descav'] = 'Use ClamAV to check uploads for viruses. NB: <a href="http://www.clamav.net/lang/en/download/packages/packages-linux/" target="_blank">ClamAV</a> must be installed to use this feature';
$string['labelhomef'] = 'ActiveDirectory field for HomeDirectory';
$string['deschomef'] = 'Leave blank to use the default AD homeDirectory field, otherwise, specify your custom AD field name above.';
$string['labelhomet'] = 'Traverse homeDirectory';
$string['deschomet'] = 'If your user\'s documents are stored within a SUBFOLDER within the AD homeDirectory path, enable this and specify the subfolder below.';
$string['labelhometloc'] = 'Subfolder for Traversal';
$string['deschometloc'] = 'Specify the subfolder of the homeDirectory path to use *(only in effect if \'Traverse homeDirectory\' is enabled)*';
$string['labeloffice'] = 'Force right-click > Save';
$string['descoffice'] = 'Affects Microsoft Office Document formats only - prevents users from opening within browser, thus opening from \'temporary internet files\' local folder, and forces them ro RightClick and choose \'Save Target As\' (the user is prompted to do this)';
$string['labelssl'] = 'Force SSL';
$string['descssl'] = 'Forces the Windows Share access block to communicate via SSL (recommended)';
$string['labelcustomtitle']='Block Custom Title';
$string['desccustomtitle']='Gives the block a custom title as opposed to the default of "Windows Shares" ';
$string['headerconfig'] = 'Windows Share access, aka SMBWebClient for Moodle 2.3+';
$string['descconfig'] = 'Whilst the majority of common settings can be established here, further fine tuning of less-common settings can be made within the <i>config_smb_web_client.php</i> file within this block\'s installation folder.';
$string['manserver'] = 'Set Server to SMB Mapping';
$string['manserver2'] = 'Step 1: Configure your Windows Shares for HomeDirectories';
$string['manshares'] = 'Add Shares';
$string['manshares2'] = 'Step 2: Configure any Additional Shares';
$string['winshare'] = 'Windows server (<i>\\\\SERVERNAME</i>)';
$string['smbshare'] = 'Samba Equivalent (<i>domain/servername</i>)';
$string['labelhomedirsharesrch']='Search home dir string';
$string['deschomedirsharesrch']='Text to search for in home directory string';
$string['labelhomedirsharerep']='Replace home dir string';
$string['deschomedirsharerep']='Text to replace in home directory string';
$string['add'] = 'Add Server > SMB mapping';
$string['del'] = 'Delete';
$string['noaccess'] = 'Access Denied';
$string['back'] = 'Return to Block Settings';
$string['sharename'] = 'Share Path (<i>domain/server/share</i>) ';
$string['sharetitle'] = 'Display Name ';
$string['courses'] = 'Choose Associated Courses (multiple selections allowed - hold CTRL) ';
$string['serverexp'] = 'You need an entry here for every server which contains your user\'s homeDirectories.';
$string['shares'] = 'Shares';
$string['jsintfilter']='You can filter shares so they only apply to people with specific roles (R), or be in specific courses (CS), or be in specific cohorts (CH) - e.g. R[manager,site_learner],CS[1],CH[parents]';
$string['shareformats'] = '<p>List each share on a separate line in standard smb format</p><p>e.g.</p><pre>
               \\\admin-server\staffshared
               \\\192.168.40.2\www
</pre>
<p>To limit shares by system roles, append :R[rolename(s)] to the share (separte multiple roles with commas), e.g.</p>
<pre>
               \\\admin-server\staffshared:R[manager,site_learner]
</pre>
<p>To limit shares by access to courses, append :CS[courseid(s)] to the share, e.g.</p>
<pre>
               \\\admin-server\staffshared:CS[100,5]
</pre>            
<p>To limit shares by system cohort membership, append :CH[cohortidnumber(s)] to the share, e.g.</p>
<pre>
               \\\admin-server\studentshared:CH[students,staff]
</pre>                        
            ';
?>