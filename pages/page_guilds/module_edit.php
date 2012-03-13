<?php

function guildsmod_edit($config, $user){
    $page_title = 'Guild Management';
    $page_content = '<h2>Edit Guild</h2>';
    //guild exists?
    $guild_query = db_query('SELECT g.id, g.name, p.name AS ownername, a.creationdate, a.alliance, a.description, a.logo, g.motd FROM guilds AS g, aac_guilds AS a, players AS p WHERE g.id = a.id AND p.id = g.ownerid AND g.name = "'.db_escape($_REQUEST['name']).'"');
    if(mysql_num_rows($guild_query) === 1){
        //set guild array and change title
        $guild = mysql_fetch_array($guild_query);
        $page_content = '<h2>Manage Guild: <span id="guildname">'.$guild["name"].'</span></h2>';
        //user can manage?
        //(if guild_ranks.level = 3 then player is leader)
        $manage_query = db_query('SELECT p.id AS pid, r.id AS rid FROM players AS p, guild_ranks AS r WHERE p.rank_id = r.id AND r.level = 3 AND r.guild_id = '.$guild["id"].' AND p.account_id = '.$user->id);
        if(mysql_num_rows($manage_query) >= 1){
            //get ranks
            $ranks_query = db_query('SELECT r.id, r.name, r.level, a.displaylevel FROM guild_ranks AS r, aac_guild_ranks AS a WHERE r.guild_id = '.$guild["id"].' AND r.id = a.id ORDER BY a.displaylevel DESC');
            $ranks_output = '<h2>Ranks</h2>';
            while($rank = db_query_result($ranks_query)){
                $ranks_output .= '<div class="charbox">
                                        <div class="charname">
                                            '.$rank["name"].'
                                        </div>
                                        <div class="chardesc">
                                            '.rand(0,5).' members
                                        </div>
                                        <div class="charbuttons">
                                            <a href="#" class="abutton">
                                                <img src="images/icons/pencil.png" alt=""/>
                                                Edit
                                            </a>
                                            <a href="#" class="abutton negative">
                                                <img src="images/icons/delete.png" alt=""/>
                                                Delete
                                            </a>
                                        </div>
                                    </div>';
            }

            //build final output
            $page_content .= '<div class="charbox">
                                <div class="charbuttons">
                                    <a href="javascript:history.go(-1)" class="abutton" style="margin-right: 20px;">
                                        <img src="images/icons/arrow_left.png" alt="">
                                        Back
                                    </a>
                                    <p>This page lets you edit guild description, MOTD, ranks, alliance, and other fun stuff. As an added bonus (free of charge!) it also allows for deletion. Complete wipeout!</p>
                                </div>
                              </div>
                              <h2>Guild Settings</h2>
                              <div class="charbox" id="guildmetabox">
                                <div class="charname">
                                    Description
                                </div>
                                <div class="charbuttons">
                                    <textarea style="width: 400px; height: 80px;" id="guildmetadescription">'.$guild["description"].'</textarea>
                                </div>
                                <div class="charname">
                                    Message of the day
                                </div>
                                <div class="charbuttons">
                                    <textarea style="width: 400px; height: 80px;" id="guildmetamotd">'.$guild["motd"].'</textarea>
                                </div>
                                <div class="charbuttons">
                                    <a href="#" class="abutton" id="guildmetasavebutton">
                                        <img src="images/icons/disk.png" alt="">
                                        Save Settings
                                    </a>
                                </div>
								<div id="guildmetaresponse" style="display: none; margin: 15px;"></div>
                              </div>
                              '.($config->debug ? $ranks_output : '').'
                              ';
        }else{
            //TODO: check for vice-leader (guild_ranks.level = 2) and print MOTD if so
            $page_content .= '<div class="charbox">
                                <div class="charbuttons" style="color: #cc0000;">
                                    <a href="javascript:history.go(-1)" class="abutton" style="margin-right: 20px;">
                                        <img src="images/icons/arrow_left.png" alt="">
                                        Back
                                    </a>
                                    You do not have permission to do this.
                                </div>
                              </div>';
        }
    }else{
        $page_content .= '<div class="charbox">
                            <div class="charbuttons" style="color: #cc0000;">
                                <a href="javascript:history.go(-1)" class="abutton" style="margin-right: 20px;">
                                    <img src="images/icons/arrow_left.png" alt="">
                                    Back
                                </a>
                                Guild does not exist.
                            </div>
                          </div>';
    }
    return array('title' => $page_title, 'content' => $page_content);
}

?>
