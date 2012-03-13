<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

//looks messy when we have the querystring 'action' in url when its not needed
if (!isset($_REQUEST['action']) && isset($_REQUEST['name'])) $_REQUEST['action'] = 'view';
//if no action is set (after we checked for the 'name' querystring) show guild list
if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'superrandomstringtoprovocedefaultbehavior';
switch($_REQUEST['action']){
    case "create":
        require_once('page_guilds/module_create.php');
        $page_title = 'Create Guild';
        if($logged_in === true){
            $page_content = guildsmod_create($config, $user);
        }else{
            printLogin();
        }
        break;
    case "edit":
        if ($logged_in === true){
            require_once('page_guilds/module_edit.php');
            $guildpage = guildsmod_edit($config, $user);
            $page_title = $guildpage["title"];
            $page_content = $guildpage["content"];
        }else{
            $page_title = 'Guild Management';
            printLogin();
        }
        break;
    case "view":
        require_once('page_guilds/module_view.php');
        $guildpage = guildsmod_view($config, $user);
        $page_title = $guildpage["title"];
        $page_content = $guildpage["content"];
        break;
    default:
        $page_title = 'Guilds';
        $page_content = '<h2>Guilds</h2>';

        $page_content .= '<div class="charbox" style="padding: 8px; min-height: 25px;">
                            '.($logged_in === true ? '
                            <div class="charimage">
                                <a href="?subtopic=guilds&action=create" class="abutton">
                                    <img src="images/icons/group_add.png" alt=""/>
                                    Create Guild
                                </a>
                            </div>' : '').'
							'.($config->debug ? '
                            <div class="charname" style="'.($logged_in === true ? 'margin-top: 20px; ' : '').'margin-left: 20px; margin-bottom: 10px;">
                                Sorting Options
                            </div>
                            <div style="width: 100%; text-align: center;">
                                <a href="?subtopic=guilds&sort=name" class="abutton">
                                    <img src="images/icons/font.png" alt=""/>
                                    Name
                                </a>
                                <a href="?subtopic=guilds&sort=members" class="abutton">
                                    <img src="images/icons/group.png" alt=""/>
                                    Members
                                </a>
                                <a href="?subtopic=guilds&sort=strength" class="abutton">
                                    <img src="images/icons/lightning.png" alt=""/>
                                    Strength
                                </a>
                                <a href="?subtopic=guilds&sort=date" class="abutton">
                                    <img src="images/icons/date.png" alt=""/>
                                    Creation Date
                                </a>
                            </div>' : '').'
                          </div>';

        $guilds_query = db_query('SELECT g.id, g.name, p.name AS ownername, a.creationdate, a.alliance, a.description, a.logo FROM guilds AS g, aac_guilds AS a, players AS p WHERE g.id = a.id AND p.id = g.ownerid');
        $guildlist = '';
        while($guild = mysql_fetch_array($guilds_query)){
            $members = db_query_num('SELECT p.id FROM players AS p WHERE p.rank_id IN (SELECT id FROM guild_ranks WHERE guild_id = '.$guild["id"].')');
            $guildlist .= '<div class="charbox">
                            '.($guild["logo"] != null ? '
                            <div class="charimage">
                                <img src="'.$guild["logo"].'" alt="Guild Logo" /><!-- path to logos are stored by filename or entire path? -->
                            </div>' : '').'
                            <div class="charname">
                                <a href="?subtopic=guilds&name='.urlencode($guild["name"]).'">'.$guild["name"].'</a>
                            </div>
                            <table class="chardatatable">
                                <tbody>
                                    <tr>
                                        <th>Leader:</th>
                                        <td><a href="?subtopic=characters&name='.urlencode($guild["ownername"]).'">'.$guild["ownername"].'</a></td>
                                    </tr>
                                    <tr>
                                        <th>Members:</th>
                                        <td>'.$members.'</td>
                                    </tr>
									'.($config->debug ? '
                                    <tr>
                                        <th>Strength:</th>
                                        <td>Soon&trade;</td>
                                    </tr>
									' : '').'
                                    <tr>
                                        <th>Founded:</th>
                                        <td>'.date('F j Y',$guild["creationdate"]).'</td>
                                    </tr>
                                    '.($guild["description"] != null ? '
                                    <tr>
                                        <th>Description:</th>
                                        <td><div style="border: 1px solid #333333; background-color: #cccccc; padding: 10px;">
                                            '.$guild["description"].'
                                        </div></td>
                                    </tr>' : '').'
                                </tbody>
                            </table>
                        </div>';
        }
        $page_content .= $guildlist;
}

?>
