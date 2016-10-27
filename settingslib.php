<?php
class admin_raw_html extends admin_setting {
    
    /**
     * not a setting, just text
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $heading heading
     * @param string $information text in box
     */
    public function __construct($name, $heading, $information) {
        $this->nosave = true;
        parent::__construct($name, $heading, $information, '');
    }    
    
    public function output_html($data, $query='') {
        global $OUTPUT;
        $return = '';
        if ($this->visiblename != '') {
            $return .= $this->visiblename;
        }
        if ($this->description != '') {
            $return .= $this->description;
        }
        return $return;
    }
    
    public function write_setting($data) {
        return 'read'.time();
    }
    
    public function get_setting(){
        return 'no setting';
    }
    
    /**
     * Always returns true
     * @return bool Always returns true
     */
    public function get_defaultsetting() {
        return true;
    }    
    
}
?>
