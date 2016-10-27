// JavaScript Document
/* 
Placeholder - at somepoint rewrite block to get rid of onclick='' events within <a> tags, and replace with some YUI JS along lines of...

OLD CODE: 
<a id="block_smb_web_client_home" href="'.$shareurl.'" target="_blank" onclick="window.open(\''.$shareurl.'\',\''.$this->nice_popup_title($homedirstr).'\',\'width=640,height=480, scrollbars=1, resizable=1\'); return false;"><img src="'.$this->blockwww.'/pix/folder_home.png" alt="Home Folder"/> '.$homedirstr.'</a>

NEW CODE:
<a id="block_smb_web_client_home" href="'.$shareurl.'" target="_blank" onclick="window.open(\''.$shareurl.'\',\''.$this->nice_popup_title($homedirstr).'\',\'width=640,height=480, scrollbars=1, resizable=1\'); return false;"><img src="'.$this->blockwww.'/pix/folder_home.png" alt="Home Folder"/> '.$homedirstr.'</a>


YAHOO.util.Event.onDOMReady(function() {   
    document.getElementById("block_smb_web_client_home").onclick =  function (){dosummat();return false;};
  });
  
function dosummat {
	window.open(\''.$shareurl.'\',\''.$this->nice_popup_title($homedirstr).'\',\'width=640,height=480, scrollbars=1, resizable=1\'); 
	return false;
}

*/
