<?php

function charsearchmod_Quests($userdata){
    $out = '
<div class="charbox">
    <a class="abutton minimizer" href="#">
        <img src="images/icons/zoom_out.png" alt=""/> Minimize
    </a>
    <div class="charname">
        Quests
    </div>
    <table class="datatable questtable minimizable">
        <tbody>
            <tr>
                <th>Name</th>
                <th>Status</th>
            </tr>
            <tr />
            <tr>
                <td>The Hills</td>
                <td class="done" style="color: #529214; font-weight: bold;">Conquered</td>
            </tr>
            <tr>
                <td>The Desert</td>
                <td class="underway" style="color: #529214;">Visited</td>
            </tr>
            <tr>
                <td>The Mountains</td>
                <td class="standby" style="font-style: italic;">Not Started</td>
            </tr>
            <tr>
                <td>Going To Heaven</td>
                <td class="lowlevel" style="color: #d12f19; font-style: italic;">Ineligible</td>
            </tr>
            <tr>
                <td>The Swamp</td>
                <td class="failure" style="color: #d12f19; font-weight: bold;">Failed</td>
            </tr>
        </tbody>
    </table>
</div>';
    return $out;
}

?>
