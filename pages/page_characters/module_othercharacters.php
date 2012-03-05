<?php

function charsearchmod_OtherCharacters($config, $userdata){
    $char_query = db_query('SELECT name, level, vocation, group_id FROM players WHERE account_id = '.$userdata["account_id"].' AND NOT (name = "'.db_escape($userdata["name"]).'") ORDER BY name ASC');
    if (mysql_num_rows($char_query) > 0){
        $chars = '';
        while($p = mysql_fetch_array($char_query)){
            $chars .= '<tr>
                            <td><a href="?subtopic=characters&name='.urlencode($p["name"]).'">'.$p["name"].'</a></td>
                            <td>'.($p["group_id"] >= $config->staffgroup ? '-' : $p["level"]).'</td>
                            <td>'.($p["group_id"] >= $config->staffgroup ? '<span style="color:#cc0000; font-weight: bold;">Staff</span>' : $config->vocations[$p["vocation"]]).'</td>
                        </tr>';
        }
        $out = '
<div class="charbox">
    <a class="abutton minimizer" href="#">
        <img src="images/icons/zoom_out.png" alt=""/> Minimize
    </a>
    <div class="charname">
        Other Characters
    </div>
    <table class="datatable minimizable">
        <tbody>
            <tr>
                <th>Name</th>
                <th>Level</th>
                <th>Vocation</th>
            </tr>
            '.$chars.'
        </tbody>
    </table>
</div>';

    }else{
        $out = '';
    }
    return $out;
}

?>
