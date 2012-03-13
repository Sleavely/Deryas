<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

if(!isset($_REQUEST['sort'])) $_REQUEST['sort'] = 'name';
switch($_REQUEST['sort']){
    case "level":
        $online_sort = 'level';
        $online_order = 'DESC';
        break;
    case "vocation":
        $online_sort = 'vocation';
        $online_order = 'ASC';
        break;
    case "timeonline":
        $online_sort = 'lastlogin';
        $online_order = 'ASC';
        break;
    default:
        $online_sort = 'name';
        $online_order = 'ASC';
}
$online_list = '';
$online_amount = 0;
$staff_query = db_query('SELECT name, level, vocation, lastlogin FROM players WHERE online = 1 AND group_id >= '.$config->staffgroup.' ORDER BY name ASC');
while($gm = mysql_fetch_array($staff_query)){
    $gm["lastlogin"] = intval($gm["lastlogin"]);
    $online_list .= '<tr>
                <td><a href="?subtopic=characters&name='.urlencode($gm["name"]).'">'.$gm["name"].'</a></td>
                <td>-</td>
                <td><span style="color:#cc0000; font-weight: bold;">Staff</span></td>
                <td>'.ucfirst(convertToFBTimestamp($gm["lastlogin"])).'</td>
            </tr>';
    $online_amount++;
}
$online_query = db_query('SELECT name, level, vocation, lastlogin FROM players WHERE online = 1 AND group_id < '.$config->staffgroup.' ORDER BY '.$online_sort.' '.$online_order);
while($player = mysql_fetch_array($online_query)){
    $player["lastlogin"] = intval($player["lastlogin"]);
    $online_list .= '<tr>
                <td><a href="?subtopic=characters&name='.urlencode($player["name"]).'">'.$player["name"].'</a></td>
                <td>'.$player["level"].'</td>
                <td>'.$config->vocations[$player["vocation"]].'</td>
                <td>'.ucfirst(convertToFBTimestamp($player["lastlogin"])).'</td>
            </tr>';
    $online_amount++;
}
$page_title = 'Online List';
$page_content = '<h2>Who is online?</h2>
<div class="charbox">
    <div class="charname">
        Online List
    </div>
    <div class="chardesc">
        '.($online_amount === 0 ? 'There are no players online.' : '
            There '.($online_amount === 1 ? 'is 1 player' : 'are '.$online_amount.' players' ).' online.' ).'
    </div>

    <table class="datatable" style="width: 100%;">
        <tbody>
            <tr>
                <th style="width: 100px;"><a href="?subtopic=whoisonline&sort=name" class="abutton" style="padding: 4px;">
                    <img src="images/icons/user.png" />
                    Name
                </a></th>
                <th><a href="?subtopic=whoisonline&sort=level" class="abutton" style="padding: 4px;">
                    <img src="images/icons/cake.png" />
                    Level
                </a></th>
                <th><a href="?subtopic=whoisonline&sort=vocation" class="abutton" style="padding: 4px;">
                    <img src="images/icons/chart_organisation.png" />
                    Vocation
                </a></th>
                <th><a href="?subtopic=whoisonline&sort=timeonline" class="abutton" style="padding: 4px;">
                    <img src="images/icons/clock.png" />
                    Logged In
                </a></th>
            </tr>
            '.$online_list.'
        </tbody>
    </table>
</div>';

?>