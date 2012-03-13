<?php

function charsearchmod_Account($userdata, $settings, $config){

    $extra_account_query = db_query('SELECT type, value FROM 3h_accounts WHERE account_id = '.$userdata["account_id"]);
    while($e = mysql_fetch_array($extra_account_query)){
        if ($e["type"] == 'created') $extra_created = $e["value"];
        if ($e["type"] == 'realname') $extra_realname = $e["value"];
        if ($e["type"] == 'location') $extra_location = $e["value"];
    }

    $out = '
<div class="charbox">
    <a class="abutton minimizer" href="#">
        <img src="images/icons/zoom_out.png" alt=""/> Minimize
    </a>
    <div class="charname">
        Account
    </div>
    <table class="chardatatable minimizable">
        <tbody>
            <tr>
                <th>Status:</th>
                <td>'.(intval($userdata["player_group"]) > intval(1) || intval($userdata["account_group"]) > intval($config->staffgroup) ? '<span style="color:#cc0000; font-weight: bold;">Staff</span>' : ($userdata["premdays"] > 0 ? '<span style="color:#529214; font-weight: bold;">Premium Account</span>' : 'Free Account')).'</td>
            </tr>
            '.(isset($extra_realname) && $settings->hasFlag('profilefield_realname') ? '<tr>
                <th>Real Name:</th>
                <td>'.$extra_realname.'</td>
            </tr>' : '').'
            '.(isset($extra_location) && $settings->hasFlag('profilefield_location') ? '<tr>
                <th>Location:</th>
                <td>'.$extra_location.'</td>
            </tr>' : '').'
            '.(isset($extra_created) ? '<tr>
                <th>Created:</th>
                <td>'.date('F j Y',$extra_created).'</td>
            </tr>' : '').'
            '.($config->debug ? '<tr>
                <th>Monetary Assets:</th>
                <td>~ gold coins</td>
            </tr>' : '').'
        </tbody>
    </table>
</div>';
    return $out;
}

?>
