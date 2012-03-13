<?php

function guildsmod_view($config, $user){

    $page_content = '<h2>View Guild</h2>';
    $guild_query = db_query('SELECT g.id, g.name, p.name AS ownername, a.creationdate, a.alliance, a.description, a.logo FROM guilds AS g, aac_guilds AS a, players AS p WHERE g.id = a.id AND p.id = g.ownerid AND g.name = "'.db_escape($_REQUEST['name']).'"');
    if(mysql_num_rows($guild_query) === 1){
        $guild = mysql_fetch_array($guild_query);
        $page_title = $guild["name"];
        $page_content .= '<div class="charbox">
                            <div class="charimage">
                                <a href="?subtopic=guilds&action=edit&name='.urlencode($guild["name"]).'" class="abutton" style="vertical-align: top;">
                                    <img src="images/icons/group_edit.png" alt=""/>
                                    Guild Management
                                </a>
								
								'.($guild["logo"] != null ? '
                                <img src="'.$guild["logo"].'" alt="Guild Logo" /><!-- path to logos are stored by filename or entire path? -->
								' : '').'
                            </div>
                            <div class="charname">
                                '.$guild["name"].'
                            </div>
                            <div class="charbuttons chardesc" style="min-height: 20px;">
                                '.($guild["description"] != null ? $guild["description"] : '').'
                            </div>
                            <table class="chardatatable" style="margin-left: 10px; width: 480px;">
                                <tbody>
                                    <tr>
                                        '.($config->debug ? '<th style="width: 250px;">Calculated Strength:</th>
                                        <td style="width: 100px;">Soon&trade;</td>' : '').'
                                        <th>Founded:</th>
                                        <td style="width: 250px;">'.date('F j Y',$guild["creationdate"]).'</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="font-size: 14px; font-weight: bold; margin-top: 20px; margin-left: 30px;">Members</div>
                            <table class="datatable" style="width: 465px;">
                                <tbody>
                                    <tr>
                                        <th></th>
                                        <th style="width: 170px">Name</th>
                                        <th>Level</th>
                                        <th>Vocation</th>
                                    </tr>';
        $members_query = db_query('SELECT p.name AS player_name, p.level AS player_level, p.vocation AS player_vocation, p.guildnick AS player_nick, r.id AS rank_id, r.name AS rank_name FROM guilds g, guild_ranks r, aac_guild_ranks ar, players p WHERE g.id = r.guild_id AND r.id = ar.id AND r.id = p.rank_id AND g.name = "'.db_escape($guild["name"]).'" ORDER BY ar.displaylevel DESC, p.name ASC');
        $latestrank = '';
        while($member = mysql_fetch_array($members_query)){
            if ($member["rank_name"] != $latestrank){
                $page_content .= '<tr style="height: 40px; vertical-align: bottom;">
                                    <th style="text-align: right;">'.$member["rank_name"].'</th>';
                $latestrank = $member["rank_name"];
            }else{
                $page_content .= '<tr>
                                    <th></th>';
            }
            $page_content .= '  <td><a href="?subtopic=characters&name='.urlencode($member["player_name"]).'">'.$member["player_name"].'</a>'.($member["player_nick"] != "" ? ' <span style="font-style: italic;">('.$member["player_nick"].')</span>' : '').'</td>
                                <td>'.$member["player_level"].'</td>
                                <td>'.$config->vocations[$member["player_vocation"]].'</td>
                              </tr>';
        }
        $page_content .= '      </tbody>
                            </table>';

        //if vice or higher, add invite box
        $isVice_query = db_query('SELECT p.id AS pid, r.id AS rid FROM players AS p, guild_ranks AS r WHERE p.rank_id = r.id AND r.level >= 2 AND r.guild_id = '.$guild["id"].' AND p.account_id = '.$user->id);
        if(mysql_num_rows($isVice_query) > 0 && $config->debug){
            $page_content .= '<div class="charbuttons">
                                <form method="post" action="?subtopic=guilds&name='.urlencode($guild["name"]).'">
                                    <label for="invitebox">Invite character: </label>
                                    <input type="text" id="invitebox" name="invitebox" class="textinput forminput" />
                                    <a href="#" class="abutton positive buttonsubmit">
                                        <img src="images/icons/group_add.png" alt=""/>
                                        Send invite
                                    </a>
                                </form>
                            </div>';
        }

        $page_content .= '</div>';
    }else{
        $page_title = 'Guilds';
        $page_content .= '<div class="charbox"><div class="charbuttons">There is no guild with that name.</div></div>';
    }

    $ret = array('title' => $page_title, 'content' => $page_content);
    return $ret;
}

?>
