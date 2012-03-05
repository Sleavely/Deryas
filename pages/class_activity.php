<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

class activity{

    public function add($activity, $user, $target = null, $activity_by_name = true){
        $user = intval($user);
        if ($user > 0){
            if ($activity_by_name === true){
                $name_query = db_query('SELECT id FROM 3h_activities WHERE name = "'.db_escape($activity).'"');
                if (mysql_num_rows($name_query) > 0){
                    while($n = mysql_fetch_array($name_query)){
                        $activity = $n["id"];
                    }
                }else{
                    return false;
                }
            }else{
                $activity = intval($activity);
            }
            db_query('INSERT INTO 3h_activitylog (activity, user, timestamp'.($target != null ? ', target' : '').') VALUES ('.$activity.', '.$user.', UNIX_TIMESTAMP()'.($target != null ? ', '.intval($target) : '').')');
            return true;
        }else{
            return false;
        }
    }
}
$activity = new activity();
?>
