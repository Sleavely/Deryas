<?php
//make sure other files are loaded through either index.php or ajax.php
$loadedProperly = true;

require_once('config.php');
require_once('pages/class_thscore.php');
require_once('pages/class_activity.php');

//Check login
$user_id = 0;
function checkLogin(){
    $user_id = 0;
    if (isset($_REQUEST['usr']) && isset($_REQUEST['hash'])){
        //compare to db
        $session_query = db_query('SELECT userid FROM aac_sessions
                                        WHERE timestamp > UNIX_TIMESTAMP()-900
                                            AND userid = "'.db_escape($_REQUEST['usr']).'"
                                            AND hash = "'.db_escape($_REQUEST['hash']).'"
                                            AND ip = "'.db_escape($_SERVER['REMOTE_ADDR']).'"');
        if (mysql_num_rows($session_query) === 0){
            //false session
            $validsession = false;
        }else{
            //valid session, update the timestamp and lastpage
            $session_result = db_query_result($session_query);
            $user_id = $session_result[0];
            db_query('UPDATE aac_sessions
                            SET timestamp = UNIX_TIMESTAMP(), lastpage = "'.db_escape($_SERVER['REQUEST_URI']).'"
                            WHERE userid = "'.db_escape($_REQUEST['usr']).'"
                                AND hash = "'.db_escape($_REQUEST['hash']).'"
                                AND ip = "'.db_escape($_SERVER['REMOTE_ADDR']).'"');
            $validsession = true;
        }
    }else{
        //no cookie or anything
        $validsession = false;
    }
    return array($validsession, $user_id);
}
$login = checkLogin();
$logged_in = $login[0];
$user_id = $login[1];
require_once('pages/class_user.php');
require_once('pages/class_timezone.php');

//check for subtopic querystring
if (!isset($_REQUEST['subtopic'])){
    echo json_encode(array('success' => 0, 'errormsg' => 'No subtopic'));
    exit;
}
//determine subtopic q-string
switch($_REQUEST['subtopic']){
    case "accountmanagement":
        require_once('pages/ajax_accountmanagement.php');
        break;
    case "creatures":
        require_once('pages/ajax_creatures.php');
        break;
    case "guilds":
        require_once('pages/ajax_guilds.php');
        break;
    default:
        echo json_encode(array('success' => 0, 'errormsg' => 'Invalid subtopic'));
}

?>
