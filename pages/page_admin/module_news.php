<?php

function adminmod_news($config, $user){
    if ($user->characterAmount > 0){
        $chars_text = '';
        $staff_text = '';
        $c_offset = 0;
        while ($c_offset < $user->characterAmount) {
            if ($user->characters[$c_offset]["group_id"] >= $config->staffgroup){
                $staff_text .= '<option value="'.$user->characters[$c_offset]["name"].'">'.$user->characters[$c_offset]["name"].'</option>';
            }else{
                $chars_text .= '<option value="'.$user->characters[$c_offset]["name"].'">'.$user->characters[$c_offset]["name"].'</option>';
            }
            $c_offset++;
        }
    }else{
        $chars_text = '<option value="customauthor">-None-</option>';
        $staff_text = '<option value="customauthor">-None-</option>';
    }
    $out = '<h2>News Editor</h2>
            <div class="charbox">
                <div class="charbuttons">
                    <a href="#" class="abutton positive addnewsbtn">
                        <img src="images/icons/add.png" alt=""/>
                        Add News
                    </a>
                </div>
                <div id="addnewsdiv" style="display: none; margin: 10px;">
                    <form method="post" action="?subtopic=admin&action=news">
                        <label for="addnewstitle">Title: </label><input type="text" name="title" id="addnewstitle" /><br />
                        <label for="addnewscontent" style="vertical-align: 200%;">Content: </label><textarea name="content" id="addnewscontent" rows="4" cols="40"></textarea><br />
                        <label for="addnewsauthor">Author: </label>
                        <select name="author" id="addnewsauthor" class="charselect">
                            <optgroup label="Staff Characters">
                                '.$staff_text.'
                            </optgroup>
                            <optgroup label="Normal Characters">
                                '.$chars_text.'
                            </optgroup>
                            <optgroup label="Other"></optgroup>
                            <option value="customauthor">Add Custom Name</option>
                        </select><br />
                        <label for="addnewscustomauthor" class="customauthorhide" style="display: none;">Custom Name: </label><input type="text" name="customauthor" id="addnewscustomauthor" class="customauthorhide" style="display: none;" /><br />
                        <input type="hidden" name="newsitem" value="new" />
                        <a class="buttonsubmit abutton positive" href="#" style="margin-top: 10px;">
                            <img src="images/icons/layout_add.png" alt=""/>
                            Submit
                        </a>
                    </form>
                </div>
            </div>';
    if (isset($_REQUEST["newsitem"])){
        if ($_REQUEST["newsitem"] == 'new'){
            $edit_title = (!empty($_REQUEST["title"]) ? $_REQUEST["title"] : 'Unnamed Article');
            $edit_content = $_REQUEST["content"];
            $edit_author = ($_REQUEST["author"] != 'customauthor' ? $_REQUEST["author"] : $_REQUEST["customauthor"]);
            $edit_date = time();
            db_query('INSERT INTO aac_news (title, content, author, timestamp) VALUES ("'.db_escape($edit_title).'", "'.db_escape($edit_content).'", "'.db_escape($edit_author).'", '.$edit_date.')');
        }// elseif 34 (where 34 is news id)
            //if subaction == edit
            //else delete
    }
    $news_query = db_query('SELECT title, content, author, timestamp FROM aac_news ORDER BY timestamp DESC LIMIT 10');
    while($n = mysql_fetch_array($news_query)){
        $out .= '<div class="charbox">
                     <div class="charname">
                         '.$n["title"].'
                     </div>
                     <div class="chardesc">
                         '.$n["author"].', '.date('F j',$n["timestamp"]).'
                     </div>
                     <div class="charbuttons">
                         <a href="#" class="abutton editnewsbtn">
                             <img src="images/icons/pencil.png" alt=""/>
                             Edit
                         </a>
                         <a href="#" class="abutton negative deletenewsbtn">
                             <img src="images/icons/delete.png" alt=""/>
                             Delete
                         </a>
                     </div>
                     <p>'.nl2br($n["content"]).'</p>
                 </div>';
    }
    return $out;
}

?>
