<?php
class swc_db {

    /**
     * escape column val
     * @param string $val
     * @return string
     */
    public static function escape_val($val){
        $escval='\''.str_replace('\'',"\'",$val).'\'';
        return ($escval);
    }    

    /**
     * is a user in the specific cohort (by cohort id or name), if so return
     * true
     * @global type $CFG
     * @global type $DB
     * @param int|string $cohort (pass cohort in as integer if you want to do it
     * @param null|int $userid
     * by cohort id)
     * @return boolean
     */
    public static function user_in_cohort($cohort, $userid=null){
        global $CFG, $DB, $USER;
        
        if ($userid===null){
            $userid=$USER->id; // get current users id
        }
        
        // if cohort is a string but obviously a number then recast
        if (preg_match('/^[0-9]$/',$cohort)){
            $cohort=intval($cohort);
        }
        
        $sql = 'SELECT cm.userid FROM '.$CFG->prefix.'cohort c LEFT JOIN '.$CFG->prefix.'cohort_members cm ON cm.cohortid=c.id WHERE c.contextid='.SYSCONTEXTID;
        if (is_int($cohort)){
            $sql.=' AND c.id='.$cohort.' AND cm.userid='.intval($userid);
        } else {
            $sql.=' AND c.idnumber='.self::escape_val($cohort).' AND cm.userid='.intval($userid);
        }
        $row=$DB->get_record_sql($sql);
        return (!empty($row));
    }
    
    /**
     * return true if user has sys level role (by shortname of sys role)
     * @param string $shortname
     * @param null|int $userid
     * @return bool
     */
    public static function has_sys_role($shortname, $userid=null){
        global $DB, $CFG, $USER;
        $userid=$userid==null ? $USER->id : $userid;
        $sql='SELECT DISTINCT r.shortname  FROM '.$CFG->prefix.'role_assignments ra LEFT JOIN '.$CFG->prefix.'role r ON r.id=ra.roleid WHERE ra.contextid='.SYSCONTEXTID.' AND ra.userid='.$userid.' AND r.shortname='.self::escape_val($shortname);
        $row=$DB->get_record_sql($sql);
        return (!empty($row));
    }    
    
}
?>
