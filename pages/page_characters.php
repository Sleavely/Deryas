<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

$page_title = 'Characters';
$page_content = '
<h2>Character Search</h2>
<div style="margin-left: 20px; margin-top: 10px;">
    <form method="post" action="?subtopic=characters">
        <label for="name">Name: </label><input class="textinput" type="text" name="name" id="name" '.(isset($_REQUEST['name']) && !empty($_REQUEST['name']) ? 'value="'.htmlentities($_REQUEST['name']).'" ' : '').'/> <a class="abutton buttonsubmit" href="#">Search</a>
    </form>
</div>';

if (isset($_REQUEST['name'])){
    $search_query = db_query('SELECT
                                     p.id,
                                     p.name,
                                     p.level,
                                     p.maglevel,
                                     p.vocation,
                                     p.sex,
                                     p.lastlogin,
                                     p.group_id AS player_group,
                                     p.town_id,
                                     p.rank_id,
                                     p.balance,
                                     p.account_id,
                                     a.group_id AS account_group,
                                     a.premdays,
                                     p.online
                              FROM
                                     players AS p,
                                     accounts AS a
                              WHERE
                                     p.name = "'.db_escape($_REQUEST['name']).'"
                                 AND
                                     p.account_id = a.id
                                 AND
                                     NOT (a.id = '.$config->deletedaccount.')
                            ');
    if (mysql_num_rows($search_query) === 1 && strtolower($_REQUEST['name']) != 'account manager'){
        $search_result = db_query_result($search_query);
        require_once('pages/class_playersettings.php');
        $settings = new playerSettings($search_result["id"]);

        $extra_player_query = db_query('SELECT type, value FROM 3h_players WHERE player_id = '.$search_result["id"]);
        while($e = mysql_fetch_array($extra_player_query)){
            if ($e["type"] == 'created') $extra_created = $e["value"];
            if ($e["type"] == 'comment') $extra_comment = $e["value"];
        }

        $house_query = db_query_row('SELECT id, owner, name FROM houses WHERE owner = '.$search_result["id"]);
        if ($house_query) $extra_house = $house_query["name"];

        $guild_query = db_query_row('SELECT g.name AS guildname, r.name AS guildrank, p.guildnick FROM guilds AS g, guild_ranks AS r, players AS p WHERE p.rank_id = r.id AND r.guild_id = g.id AND p.id = '.$search_result["id"]);
        if ($guild_query){
            $extra_guildname = $guild_query["guildname"];
            $extra_guildrank = $guild_query["guildrank"];
            $extra_guildnick = $guild_query["guildnick"];
        }

        $page_content .= '<h2>Character Profile - '.$search_result["name"].'</h2>

        <div class="charbox">
            <div class="charname">
                Data
            </div>
            <table class="chardatatable">
                <tbody>
                    <tr>
                        <th>Name:</th>
                        <td>'.$search_result["name"].'</td>
                    </tr>
                    '.(intval($search_result["player_group"]) >= intval($config->staffgroup) ? '' : '
                    <tr>
                        <th>Level:</th>
                        <td>'.$search_result["level"].'</td>
                    </tr>
                    '.($settings->hasFlag('profilefield_magiclevel') ? '
                    <tr>
                        <th>Magic Level:</th>
                        <td>'.$search_result["maglevel"].'</td>
                    </tr>' : '').'
                    <tr>
                        <th>Vocation:</th>
                        <td>'.$config->vocations[$search_result["vocation"]].'</td>
                    </tr>').'
                    <tr>
                        <th>Gender:</th>
                        <td>'.(intval($search_result["sex"]) === intval(1) ? 'Male' : 'Female').'</td>
                    </tr>
                    '.(isset($extra_created) ? '
                    <tr>
                        <th>Created:</th>
                        <td>'.date('F j Y',$extra_created).'</td>
                    </tr>' : '').'
                    <tr>
                        <th>Last Login:</th>
                        <td>'.(intval($search_result["online"]) === intval(1) ? '<span style="font-weight: bold; color: #529214;">Online</span>' : (intval($search_result["lastlogin"]) > intval(0) ? date('F j Y, G:i \C\E\T', $search_result["lastlogin"]) : 'Never' )).'</td>
                    </tr>
                    '.(intval($search_result["player_group"]) > intval($config->staffgroup) ? '
                    <tr>
                        <th>Position:</th>
                        <td><span style="color:#cc0000; font-weight: bold;">Staff</span></td>
                    </tr>' : '').'
                    
                </tbody>
            </table>
        </div>
        <div class="charbox">
            <div class="charname">
                Information
            </div>
            <table class="chardatatable">
                <tbody>
                    <tr>
                        <th>Residence:</th>
                        <td>'.$config->towns[$search_result["town_id"]]['name'].'</td>
                    </tr>
                    '.(isset($extra_house) ? '
                    <tr>
                        <th>House:</th>
                        <td>'.$extra_house.'</td>
                    </tr>' : '').'
                    '.(isset($extra_guildname) ? '
                    <tr>
                        <th>Guild:</th>
                        <td>'.$extra_guildrank.' of the <a href="?subtopic=guilds&name='.urlencode($extra_guildname).'">'.$extra_guildname.'</a>'.($extra_guildnick != "" ? ' ('.$extra_guildnick.')' : '').'</td>
                    </tr>' : '').'
                    '.($settings->hasFlag('profilefield_assets') && $config->debug ? '
                    <tr>
                        <th>Assets:</th>
                        <td>' . /* $search_result["balance"] plus items and depots */ '~ gold coins</td>
                    </tr>' : '').'
                    '.(isset($extra_comment) ? '
                    <tr>
                        <th>Comment:</th>
                        <td><div style="border: 1px solid #333333; background-color: #cccccc; padding: 10px;">
                            '.nl2br($extra_comment).'
                        </div></td>
                    </tr>' : '').'
                </tbody>
            </table>
        </div>
        ';

        require_once('page_characters\module_deaths.php');
        $page_content .= charsearchmod_Deaths($search_result);

        if ($settings->hasFlag('profilefield_quests') && $config->debug){
            require_once('page_characters/module_quests.php');
            $page_content .= charsearchmod_Quests($search_result);
        }
        if ($settings->hasFlag('profilefield_statistics') && $config->debug){
            require_once('page_characters/module_statistics.php');
            $page_content .= charsearchmod_Statistics($search_result);
        }
        if ($settings->hasFlag('profilefield_account')){
            require_once('page_characters/module_account.php');
            $page_content .= charsearchmod_Account($search_result, $settings, $config);
        }
        if ($settings->hasFlag('profilefield_otherchars')){
            require_once('page_characters/module_othercharacters.php');
            $page_content .= charsearchmod_OtherCharacters($config, $search_result);
        }
        
    }else{
        $page_content .= '<div style="color: #cc0000; font-weight: bold; margin-left: 20px;">The character does not exist.</div>';
    }
}

?>
