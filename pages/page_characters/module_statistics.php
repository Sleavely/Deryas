<?php

function charsearchmod_Statistics($userdata){
    $out = '
<div class="charbox">
    <a class="abutton minimizer" href="#">
        <img src="images/icons/zoom_out.png" alt=""/> Minimize
    </a>
    <div class="charname">
        Statistics
    </div>
    <div class="minimizable">
            <div class="imagebox first">
                <strong>Experience Gained</strong>
                <img class="block" src="http://chart.apis.google.com/chart?cht=lc&chs=450x100&chf=bg,s,00000000&chd=t:2345,532,7454,93531,483,0,21458&chds=0,100000&chxt=x,y,x,y&chxl=0:|Mon|Tue|Wed|Thu|Fri|Sat|Sun|1:||100+000|2:|Date|3:|Experience&chxp=2,50|3,50"/>
                <span class="imagetext">Cip gained a total of 125803 experience points the last 7 days.</span>
            </div>
            <div class="imagebox">
                <strong>Creatures Killed</strong>
                <img class="block" src="http://chart.apis.google.com/chart?cht=lc&chs=450x100&chf=bg,s,00000000&chd=t:26,532,81,293,251,0,48&chds=0,600&chxt=x,y,x,y&chxl=0:|Mon|Tue|Wed|Thu|Fri|Sat|Sun|1:||600|2:|Date|3:|Kills&chxp=2,50|3,50"/>
                <span class="imagetext">The past week 1231 creatures have been slain by Cip, who has a total of 59874 kills.</span>
            </div>
    </div>
</div>';
    return $out;
}

?>
