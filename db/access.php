<?php
$capabilities = array(
    'block/smb_web_client:addinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(            
            'manager' => CAP_ALLOW
        )
    )
);
?>
