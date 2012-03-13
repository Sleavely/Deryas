<?php

function accmgrmod_editChar($user, $activity, $config){

    if (!isset($_REQUEST["name"])) $_REQUEST["name"] = '3r89hndo389sfn';
    $char_exists = db_query('SELECT id FROM players WHERE name = "'.db_escape($_REQUEST["name"]).'" AND account_id = '.$user->id);
    if (mysql_num_rows($char_exists) === 1){

        //get settings and stuff
        require_once('pages/class_playersettings.php');
        $char_result = mysql_fetch_array($char_exists);
        $settings = new playerSettings($char_result["id"]);
        $comment_query = db_query('SELECT type, value FROM 3h_players WHERE type = "comment" AND player_id = '.$char_result["id"]);
        if (mysql_num_rows($comment_query) === 1){
            $comment_result = mysql_fetch_array($comment_query);
            $comment_text = $comment_result["value"];
        }else{
            $comment_text = '';
        }

        //update values before displaying them
        if (isset($_REQUEST["update"])){
            $settings->flags = 0;
            foreach ($_REQUEST["pf"] as $profilefield) {
                switch($profilefield){
                    case "magiclevel":
                        $settings->setFlag('profilefield_magiclevel');
                        break;

                    case "assets":
                        $settings->setFlag('profilefield_assets');
                        break;

                    case "quests":
                        $settings->setFlag('profilefield_quests');
                        break;

                    case "statistics":
                        $settings->setFlag('profilefield_statistics');
                        break;

                    case "account":
                        $settings->setFlag('profilefield_account');
                        break;

                    case "realname":
                        $settings->setFlag('profilefield_realname');
                        break;

                    case "location":
                        $settings->setFlag('profilefield_location');
                        break;

                    case "otherchars":
                        $settings->setFlag('profilefield_otherchars');
                        break;
                }
            }
            if (isset($_REQUEST["pfcomment"])){
                if ($_REQUEST["pfcomment"] != $comment_text){
                    //isnert update delete
                    if ($comment_text == ''){
                        //doesnt exist, INSERT
                        db_query('INSERT INTO 3h_players (player_id, type, value) VALUES ('.$char_result["id"].', "comment", "'.db_escape($_REQUEST["pfcomment"]).'")');
                    }elseif($comment_text != '' && $_REQUEST["pfcomment"] != ''){
                        //neither is empty, UPDATE
                        db_query('UPDATE 3h_players SET value = "'.db_escape($_REQUEST["pfcomment"]).'" WHERE type = "comment" AND player_id = '.$char_result["id"]);
                    }else{
                        //new is empty, DELETE
                        db_query('DELETE FROM 3h_players WHERE type = "comment" AND player_id = '.$char_result["id"]);
                    }
                    $activity->add('changedcomment', $char_result["id"]);
                }
                $comment_text = $_REQUEST["pfcomment"];
            }
            foreach ($_REQUEST["nf"] as $newsfeed) {
                switch($newsfeed){
                    case "showme":
                        $settings->setFlag('newsfeed_showme');
                        break;

                    case "died":
                        $settings->setFlag('newsfeed_deaths');
                        break;

                    case "gainedlevel":
                        $settings->setFlag('newsfeed_advances');
                        break;
                }
            }

        }

        $page_content = '<h2>Edit Character - '.$_REQUEST["name"].'</h2>
                         <form name="editcharacter" method="post" action="?subtopic=accountmanagement&action=editcharacter&name='.urlencode($_REQUEST["name"]).'">
                             <input type="hidden" name="update" value="true" />
                             <div class="charbox">
                                 <div class="charname">
                                     Profile Fields
                                 </div>
                                 <div class="chardesc">
                                     These options decide what others can see about your character.
                                 </div>
                                 <div style="margin: 10px;">
                                     <input type="checkbox" '.($settings->hasFlag('profilefield_magiclevel') ? 'checked="checked"' : '').' name="pf[]" value="magiclevel" id="pfmagiclevel"/><label for="pfmagiclevel"> Magic Level</label><br />
                                     '.($config->debug ? '<input type="checkbox" '.($settings->hasFlag('profilefield_assets') ? 'checked="checked"' : '').' name="pf[]" value="assets" id="pfassets"/><label for="pfassets"> Assets</label><br />' : '').'
                                     '.($config->debug ? '<input type="checkbox" '.($settings->hasFlag('profilefield_quests') ? 'checked="checked"' : '').' name="pf[]" value="quests" id="pfquests"/><label for="pfquests"> Quests Module</label><br />' : '').'
                                     '.($config->debug ? '<input type="checkbox" '.($settings->hasFlag('profilefield_statistics') ? 'checked="checked"' : '').' name="pf[]" value="statistics" id="pfstatistics"/><label for="pfstatistics"> Statistics Module</label><br />' : '').'
                                     <input type="checkbox" '.($settings->hasFlag('profilefield_account') ? 'checked="checked"' : '').' name="pf[]" value="account" id="pfaccount"/><label for="pfaccount"> Account Module</label><br />
                                     <dl style="margin-top: 5px;">
                                        <dd style="margin-left: 20px;"><input type="checkbox" '.($settings->hasFlag('profilefield_realname') ? 'checked="checked"' : '').' name="pf[]" value="realname" id="pfrealname"/><label for="pfrealname"> Real Name</label></dd>
                                        <dd style="margin-left: 20px;"><input type="checkbox" '.($settings->hasFlag('profilefield_location') ? 'checked="checked"' : '').' name="pf[]" value="location" id="pflocation"/><label for="pflocation"> Location</label></dd>
                                     </dl>
                                     <input type="checkbox" '.($settings->hasFlag('profilefield_otherchars') ? 'checked="checked"' : '').' name="pf[]" value="otherchars" id="pfotherchars"/><label for="pfotherchars"> Other Characters Module</label><br />
                                     <label for="pfcomment" style="vertical-align: 150%;">Comment: </label><textarea style="margin-top: 15px;" rows="3" cols="40" name="pfcomment" id="pfcomment">'.$comment_text.'</textarea>
                                 </div>
                                 <div class="author">
                                     <input type="submit" value="Save Settings" />
                                 </div>
                             </div>
                             <div class="charbox">
                                 <div class="charname">
                                     News Feed
                                 </div>
                                 <div class="chardesc">
                                     The Recent Activity module shows people\'s deaths, guild wars, level-ups, etc.
                                 </div>
                                 <div style="margin: 10px;">
                                    <input type="checkbox" '.($settings->hasFlag('newsfeed_showme') ? 'checked="checked"' : '').' name="nf[]" value="showme" id="nfshowme"/> <label for="nfshowme">Show my recent activities to others</label><br />
                                    <dl style="margin-top: 5px;">
                                        <dd style="margin-left: 20px;"><input type="checkbox" '.($settings->hasFlag('newsfeed_deaths') ? 'checked="checked"' : '').' name="nf[]" value="died" id="nfdied"/> <label for="nfdied">Deaths</label></dd>
                                        <dd style="margin-left: 20px;"><input type="checkbox" '.($settings->hasFlag('newsfeed_advances') ? 'checked="checked"' : '').' name="nf[]" value="gainedlevel" id="nfgainedlevel"/> <label for="nfgainedlevel">Advances</label></dd>
                                    </dl>
                                 </div>
                                 <div class="author">
                                     <input type="submit" value="Save Settings" />
                                 </div>
                             </div>
                         </form>';
            }else{
                $page_content = '<h2>Edit Character</h2>
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
