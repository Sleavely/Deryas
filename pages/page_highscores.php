<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

$page_title = 'Highscores';
$page_content = '
<h2>Highscores</h2>
<div>
    <a href="?subtopic=highscores&action=experience" class="abutton">
        <img src="sprites.php?id=6280" />
        Experience
    </a>
    <a href="?subtopic=highscores&action=magic" class="abutton">
        <img src="sprites.php?id=8918" />
        Magic
    </a>
    <a href="?subtopic=highscores&action=fishing" class="abutton">
        <img src="sprites.php?id=10223" />
        Fishing
    </a>
    <a href="?subtopic=highscores&action=shielding" class="abutton">
        <img src="sprites.php?id=2526" />
        Shielding
    </a>
    <br />
    <a href="?subtopic=highscores&action=fist" class="abutton">
        <img src="sprites.php?id=6537" />
        Fist
    </a>
    <a href="?subtopic=highscores&action=sword" class="abutton">
        <img src="sprites.php?id=7385" />
        Sword
    </a>
    <a href="?subtopic=highscores&action=axe" class="abutton">
        <img src="sprites.php?id=2378" />
        Axe
    </a>
    <a href="?subtopic=highscores&action=club" class="abutton">
        <img src="sprites.php?id=2391" />
        Club
    </a>
    <a href="?subtopic=highscores&action=distance" class="abutton">
        <img src="sprites.php?id=8853" />
        Distance
    </a>
</div>';

$limitOffset = 0;
$limitCount = 30;
if (!isset($_REQUEST["action"])) $_REQUEST["action"] = 'experience';
$standard_query = true; //this will fetch data from the player_skills table
switch($_REQUEST["action"]){
    case "magic":
        $skillname = 'Magic Level';
        $standard_query = false;
        $highscore_query = db_query('SELECT name, maglevel AS skill FROM players WHERE NOT (name = "Account Manager") AND NOT (account_id = '.$config->deletedaccount.') AND group_id < '.$config->staffgroup.' ORDER BY maglevel DESC, manaspent DESC LIMIT '.$limitOffset.','.$limitCount);
        break;
    case "fishing":
        $skillname = 'Fishing';
        $skillid = 6;
        break;
    case "shielding":
        $skillname = 'Shielding';
        $skillid = 5;
        break;
    case "fist":
        $skillname = 'Fist Fighting';
        $skillid = 0;
        break;
    case "sword":
        $skillname = 'Sword Fighting';
        $skillid = 2;
        break;
    case "axe":
        $skillname = 'Axe Fighting';
        $skillid = 3;
        break;
    case "club":
        $skillname = 'Club Fighting';
        $skillid = 1;
        break;
    case "distance":
        $skillname = 'Distance Fighting';
        $skillid = 4;
        break;
    default:
        // "experience"
        $skillname = 'Level';
        $standard_query = false;
        $highscore_query = db_query('SELECT name, level AS skill FROM players WHERE NOT (name = "Account Manager") AND NOT (account_id = '.$config->deletedaccount.') AND group_id < '.$config->staffgroup.' ORDER BY skill DESC, experience DESC LIMIT '.$limitOffset.','.$limitCount);
}
if ($standard_query === true){
    $highscore_query = db_query('SELECT p.name, s.value AS skill FROM players AS p, player_skills AS s WHERE p.id = s.player_id AND NOT (p.name = "Account Manager") AND NOT (p.account_id = '.$config->deletedaccount.') AND p.group_id < '.$config->staffgroup.' AND s.skillid = '.$skillid.' ORDER BY s.value DESC, s.count DESC LIMIT '.$limitOffset.','.$limitCount);
}
$highscore_entries = '';
$headcount = 1;
while($s = mysql_fetch_array($highscore_query)){
    $highscore_entries .= '<tr>
                                <td>'.($headcount === 1 ? '<img src="sprites.php?id=10127" />' : ($headcount === 2 ? '<img src="sprites.php?id=10128" />' : ($headcount === 3 ? '<img src="sprites.php?id=10129" />' : $headcount))).'</td>
                                <td><a href="?subtopic=characters&name='.urlencode($s["name"]).'">'.$s["name"].'</a></td>
                                <td>'.$s["skill"].'</td>
                            </tr>';
    $headcount++;
}

// Generate the table with players
$page_content .= '<div class="charbox">
    <div class="charname">
        '.$skillname.'
    </div>
    <div class="charbuttons">
        <table style="margin-left: 25px; width: 500px;">
            <tbody>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Value</th>
                </tr>
                '.$highscore_entries.'
            </tbody>
        </table>
    </div>
</div>
';

?>
