<?php

class swc{
    /**
     * display share based on filters
     * @param array $share_arr
     * @return boolean
     */
    public static function can_show_share($share_arr, $userid=null){
        global $DB, $COURSE, $USER, $smb_cfg;
        
        if ($userid==null){
            $userid=$USER->id;
        }
        
        if (empty($share_arr['filters'])){
            // filters are empty, ok to display
            return true;
        }
        
        $filtercourses=!empty($share_arr['filters']['courses']);
        $filtercohorts=!empty($share_arr['filters']['cohorts']);
        $filterroles=!empty($share_arr['filters']['roles']);
        
        if (!$filtercourses && !$filtercohorts && !$filterroles){
            // filters are empty, ok to display
            return (true);
        }
        
        // evaluate course filter
        if ($filtercourses){
            $courses=$share_arr['filters']['courses'];
            // Check user can access course or course is currently open
            if (!empty($smb_cfg->cfgAllSharesSite)){
                foreach ($courses as $courseid){
                    if (is_int($courseid)){                        
                        // Get course
                        $course=get_record('course', 'id', $courseid);
                        $ci=false;
                        if ($course){
                            // Get context instance
                            $ci=context_course::instance($courseid);
                            // Check capabilities
                            if ($ci && ($COURSE->id==$courseid || has_capability('moodle/course:view', $ci))){
                                // OK to display
                                return (true);
                            }                            
                        }
                    }
                }    
            } else {
                if (in_array($COURSE->id, $courses)){
                    // OK to display
                    return (true);
                }
            }
        }
        
        // evaluate system level cohort filter
        if ($filtercohorts){
            foreach ($share_arr['filters']['cohorts'] as $cohort){
                if (swc_db::user_in_cohort($cohort)){
                    // OK to display
                    return (true);
                }
            }
        }
        
        // evaluate system level role filter
        if ($filterroles){            
            foreach ($share_arr['filters']['roles'] as $role){
                
               
                // if admin then check if current user is an admin
                if ($role=='administrator' || $role=='manager'){// bug fix 2013060602
                    $role='manager'; // convert legacy role
                    $admins=get_admins();
                    foreach ($admins as $admin){
                        //echo ('<p>checking '.$admin->id.' against '.$userid.'</p>');                        
                        if ($admin->id==$userid){
                            return (true);
                        }
                    }
                }
                if (swc_db::has_sys_role($role)){
                    // OK to display
                    return (true);
                }
            }
        }
        
        return (false); // do not display
    }
    
}
?>
