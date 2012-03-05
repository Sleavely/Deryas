<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

function printLogin($errormsg = false){
    global $page_content;
    if ($_REQUEST['subtopic'] == 'logout'){
        $path = '?subtopic=accountmanagement';
    }else{
        $path = $_SERVER['REQUEST_URI'];
    }
    if ($errormsg === false){
        $errortext = '';
    }else{
        $errortext = '<p style="color: #FF0000;">'.$errormsg.'</p>';
    }
    $page_content = '<h2>Login</h2>
    <p>The page you are trying to view requires you to be logged in.</p>

    <div style="line-height: 30px;">
        <form method="post" action="'.$path.'">
            <input type="hidden" name="logindata" id="logindata" value="logindata" />
            <label for="accname">Account Name:</label> <input class="textinput forminput" type="text" name="accname" id="accname" spellcheck="false" /><br />
            <label for="accpwd" style="margin-left: 29px;">Password:</label> <input class="textinput forminput" type="password" name="accpwd" id="accpwd" /><br />
            <div style="margin-left: 25px;">
                <a class="buttonsubmit abutton positive" href="#">
                    <img src="images/icons/bullet_key.png" alt=""/>
                    Login
                </a>
                '.$errortext.'
            </div>
        </form>
    </div>

    <p style="margin-top: 60px; margin-left: 25px;">
        <a class="abutton" href="?subtopic=createaccount">
            <img src="images/icons/user_add.png" alt=""/>
            Don\'t have an account?
        </a>
        <a class="abutton" href="?subtopic=accountmanagement&action=lostaccount">
            <img src="images/icons/help.png" alt=""/>
            Lost your password?
        </a>
    </p>';
}

if (isset($_REQUEST['logindata']) && $logged_in === false){
    //User has sent data. Now lets see what we\'ve got!
    $verify_query = db_query('SELECT id FROM accounts WHERE name = "'.db_escape($_REQUEST['accname']).'" AND password = "'.db_escape($_REQUEST['accpwd']).'"');
    if (mysql_num_rows($verify_query) === 1){
        $verify_result = db_query_result($verify_query);
        $user_id = $verify_result[0];
        $timestamp = time();
        $user_hash = md5(md5($_REQUEST['accpwd']).$timestamp);
        setcookie('usr', $user_id);
        setcookie('hash', $user_hash);
        db_query('INSERT INTO aac_sessions (userid, hash, timestamp, ip, lastpage)
                                    VALUES ('.intval($user_id).',"'.db_escape($user_hash).'",'.$timestamp.',"'.db_escape($_SERVER['REMOTE_ADDR']).'","'.db_escape($_SERVER['REQUEST_URI']).'")');
        $logged_in = true;
    }
}

?>
