<?php

function guildsmod_create($config, $user){

    //which chars are eligble to create guild?
    $canCreate = array();
    foreach($user->characters as $character){
        if($character["level"] >= ($config->guildminimumlevel-1) && $character["rank_id"] == 0){
            $canCreate[] = array("id" => $character["id"], "name" => $character["name"]);
        }
    }
    $page_content = '<h2>Create Guild</h2>';
    if(count($canCreate) > 0){
        //guild name, owner, then wat? nothing? ok.
        $page_content .= '
<div class="charbox" id="guildcreateformdiv">
    <div class="charbuttons">
        <a href="?subtopic=guilds" class="abutton">
            <img src="images/icons/arrow_left.png" alt=""/>
            Back to Guild List
        </a><br />
    </div>
    <div class="charbuttons" style="line-height: 30px; border-top: 1px solid #AAAAAA; padding-top: 10px;">
        <label for="newguildname" style="margin-left: 10px;">Guild Name: </label><input class="textinput" type="text" name="newguildname" id="newguildname" /><br />
        <label for="newguildowner" style="margin-left: 40px;">Leader:</label>
        <select name="newguildowner" id="newguildowner">';
        $first = true;
        foreach($canCreate as $character){
            $page_content .= '<option value="'.$character["id"].'"'.($first === true ? ' selected="selected"' : '').'>'.$character["name"].'</option>';
            $first = false;
        }
        $page_content .= '
        </select><br />
        <a class="abutton" style="margin-left: 95px;" id="proceedguildcreatebutton" href="#">
            Proceed
            <img alt="" src="images/icons/arrow_right.png"/>
        </a>
    </div>
</div>
<div class="charbox" id="guildcreateresponsediv" style="display: none;">
    <div class="charbuttons">
        <a class="abutton negative" id="guildcreateresponsebutton" href="#">
            <img alt="" src="images/icons/arrow_left.png"/>
            Back
        </a><br />
    </div>
    <div class="charbuttons" style="border-top: 1px solid #AAAAAA; padding-top: 10px;">
        <p id="guildcreateresponsetext">If you see this text something isn\'t working properly.</p>
    </div>
</div>
';
    }else{
        $page_content .= '<div class="charbox" style="padding: 8px; color: #cc0000;">
                        <a href="javascript:history.go(-1)" class="abutton" style="margin-right: 20px;">
                            <img src="images/icons/arrow_left.png" alt="">
                            Back
                        </a>
                        You have no guildless characters <b>above</b> level '.($config->guildminimumlevel-1).'.
                    </div>';
    }

    return $page_content;
}

?>
