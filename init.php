<?php

require_once('config.php');
require_once('pages/class_thscore.php');

//Clean up sessions table
db_query('DELETE FROM aac_sessions WHERE timestamp < UNIX_TIMESTAMP()-900');

//Check login
$user_id = 0;
if (isset($_COOKIE['usr']) && isset($_COOKIE['hash'])){
    //compare to db
    $session_query = db_query('SELECT userid FROM aac_sessions
                                    WHERE timestamp > UNIX_TIMESTAMP()-900
                                        AND userid = "'.db_escape($_COOKIE['usr']).'"
                                        AND hash = "'.db_escape($_COOKIE['hash']).'"
                                        AND ip = "'.db_escape($_SERVER['REMOTE_ADDR']).'"', true);
    if (mysql_num_rows($session_query) === 0){
        $logged_in = false;
    }else{
        $session_result = db_query_result($session_query);
        $user_id = $session_result[0];
        db_query('UPDATE aac_sessions
                        SET timestamp = UNIX_TIMESTAMP(), lastpage = "'.db_escape($_SERVER['REQUEST_URI']).'"
                        WHERE userid = "'.db_escape($_COOKIE['usr']).'"
                            AND hash = "'.db_escape($_COOKIE['hash']).'"
                            AND ip = "'.db_escape($_SERVER['REMOTE_ADDR']).'"');
        $logged_in = true;
    }
}else{
    $logged_in = false;
}
require_once('pages/module_login.php');
require_once('pages/class_user.php');
require_once('pages/class_timezone.php');
require_once('pages/class_activity.php');

?>
