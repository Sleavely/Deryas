<?php

function accmgrmod_deleteChar($user, $config, $activity){

    if (!isset($_REQUEST["name"])) $_REQUEST["name"] = '3r89hndo389sfn';
    $char_exists = db_query('SELECT id, name FROM players WHERE name = "'.db_escape($_REQUEST["name"]).'" AND account_id = '.$user->id);
    if (mysql_num_rows($char_exists) === 1){
        $char_result = mysql_fetch_array($char_exists);
        if(isset($_REQUEST['delete'])){
            $activity->add('deletedchar', $char_result["id"]);
            db_query('INSERT INTO 3h_players (player_id, type, value) ('.$char_result["id"].', "oldaccount", '.$user->id.')');
            db_query('UPDATE players SET account_id = '.$config->deletedaccount.', rank_id = 0 WHERE id = '.$char_result["id"]);
            $page_content = '<h2>Delete Character - '.$_REQUEST["name"].'</h2>
                                <div class="charbox">
                                    <div style="margin: 10px;">
                                        '.$_REQUEST["name"].' has been deleted.
                                    </div>
                                    <div class="author">
                                        <a class="abutton positive" href="?subtopic=accountmanagement">
                                            <img src="images/icons/arrow_left.png" alt=""/>
                                            Go Back
                                        </a>
                                     </div>
                                </div>';
        }else{
            $page_content = '<h2>Delete Character - '.$_REQUEST["name"].'</h2>
                             <form name="deletecharacter" method="post" action="?subtopic=accountmanagement&action=deletecharacter&name='.urlencode($_REQUEST["name"]).'">
                                 <input type="hidden" name="delete" value="true" />
                                 <div class="charbox">
                                     <div class="charname">
                                         Confirm Delete
                                     </div>
                                     <div class="chardesc">
                                         Do you really want to remove '.$char_result["name"].'?
                                     </div>
                                     <div style="margin: 10px;">
                                         Keep in mind that when you delete a character it does not make a name available, it simply hides the character from all parts of the game and frees up slots on your account.
                                     </div>
                                     <div class="author">
                                         <a class="buttonsubmit abutton negative" href="#">
                                            <img src="images/icons/exclamation.png" alt=""/>
                                            Delete
                                        </a>
                                        <a class="abutton" href="?subtopic=accountmanagement">
                                            <img src="images/icons/heart.png" alt=""/>
                                            Cancel
                                        </a>
                                     </div>
                                 </div>
                             </form>';
        }
    }else{
        $page_content = '<h2>Delete Character</h2>
                         <p>Invalid character, one of the following applies:</p>
                         <div>
                            <ul>
                                <li>You did not enter a name</li>
                                <li>The character does not exist</li>
                                <li>The character is not yours</li>
                            </ul>
                         </div>';
    }
    return $page_content;
}

?>
