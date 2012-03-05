<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

$didout = '<img src="images/icons/feed.png" alt=""/><strong>Recent Activity</strong><br />';

$activity_query = db_query('SELECT
                                l.id,
                                a.name,
                                a.phrase,
                                a.usertype,
                                l.user,
                                l.timestamp,
                                l.target
                            FROM
                                3h_activities AS a,
                                3h_activitylog AS l
                            WHERE
                                a.id = l.activity
                            ORDER BY l.timestamp DESC
                            LIMIT 5');
while($a = mysql_fetch_array($activity_query)){
    //if acccount get realname
    if ($a["usertype"] != 'account'){
        $user = db_query_row('SELECT name, sex FROM players WHERE id = '.$a["user"]);
        $phrase = str_replace('$user', '<a href="?subtopic=characters&name='.urlencode($user["name"]).'">'.$user["name"].'</a>', $a["phrase"]);
        $phrase = str_replace('$sex', (intval($user["sex"]) === intval(1) ? 'his' : 'her'), $phrase);
        if($a["target"] != null){
            switch($a["name"]){
                case "gainedlevel":
                    $phrase = str_replace('$target', intval($a["target"]), $phrase);
                    break;
                case "createguild":
                    $guild_query = db_query_row('SELECT id, name FROM guilds WHERE id = '.$a["target"]);
                    $phrase = str_replace('$target', '<a href="?subtopic=guilds&name='.urlencode($guild_query["name"]).'">'.$guild_query["name"].'</a>', $phrase);
                    break;
                case "joinguild":
                    $guild_query = db_query_row('SELECT id, name FROM guilds WHERE id = '.$a["target"]);
                    $phrase = str_replace('$target', '<a href="?subtopic=guilds&name='.urlencode($guild_query["name"]).'">'.$guild_query["name"].'</a>', $phrase);
                    break;
                case "leaveguild":
                    $guild_query = db_query_row('SELECT id, name FROM guilds WHERE id = '.$a["target"]);
                    $phrase = str_replace('$target', '<a href="?subtopic=guilds&name='.urlencode($guild_query["name"]).'">'.$guild_query["name"].'</a>', $phrase);
                    break;
                case "died":
                    $phrase = str_replace('$target', intval($a["target"]), $phrase);
                    break;
            }
        }
        $didout .= '<div id="activity'.$a["id"].'">'.$phrase.' <br /><span>'.convertToFBTimestamp(intval($a["timestamp"])).'</span></div>';
    }
}

echo $didout;

?>
