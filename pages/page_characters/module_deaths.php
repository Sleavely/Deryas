<?php

function charsearchmod_Deaths($userdata){

    $out = '
<div class="charbox">
    <a class="abutton minimizer" href="#">
        <img src="images/icons/zoom_out.png" alt=""/> Minimize
    </a>
    <div class="charname">
        Deaths
    </div>';

    $deaths_query = db_query('SELECT
                                    d.id,
                                    e.name,
                                    d.date,
                                    d.level,
                                    k.final_hit,
                                    k.unjustified
                                FROM
                                    player_deaths AS d,
                                    killers AS k,
                                    environment_killers AS e
                                WHERE
                                    k.death_id = d.id AND
                                    k.id = e.kill_id AND
                                    d.player_id = '.$userdata["id"].'
                                ORDER BY d.date LIMIT 10');
    $deathcount = mysql_num_rows($deaths_query);
    if ($deathcount === 0){
        $out .= '<table class="datatable questtable minimizable">
            <tbody>
                <tr>
                    <td>No deaths on record.</td>
                </tr>
            </tbody>
        </table>';
    }else{
        $deathrows = '';
        while($d = mysql_fetch_array($deaths_query)){
            if (intval($d["final_hit"]) === intval(1)){
                $is_player = true;
                //if first characters are 'a ' or 'an ' then its not a player (this needs to be fixed..)
                if (stripos($d["name"],'a ') === 0 || stripos($d["name"],'an ') === 0) $is_player = false;
                $deathrows .= '
                                <tr><!-- d.id '.$d["id"].' -->
                                    <td>'.($is_player === true ? '<a href="?subtopic=characters&name='.urlencode($d["name"]).'">' : '').$d["name"].($is_player === true ? '</a>' : '').'</td>
                                    <td>'.$d["level"].'</td>
                                    <td>'.date('F j Y, G:i \C\E\T', $d["date"]).'</td>
                                </tr>';
            }
        }
        $out .= '<table class="datatable minimizable">
            <tbody>
                <tr>
                    <th>Killer</th>
                    <th>Level</th>
                    <th>Date</th>
                </tr>
                '.$deathrows.'
            </tbody>
        </table>';
    }
    $out .= '</div>';
    return $out;
}

?>
