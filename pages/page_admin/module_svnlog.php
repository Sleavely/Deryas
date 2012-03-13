<?php

function adminmod_svnlog($config){
    $out = '<br /><strong>Sorting files; newest first.</strong><br />';
    if ($config->svnenabled === true){
        require_once("module_svnlog/phpsvnclient.php");
        $phpsvnclient = new phpsvnclient($config->svnhost, (strlen($config->svnuser) > 0 ? $config->svnuser : null), (strlen($config->svnpass) > 0 ? $config->svnpass : null));
        $files = $config->svnfiles;
        $times = array();
        foreach($files as $key => $value){
            $tmp = $value;
            $files[$key] = $phpsvnclient->getFileLogs($tmp);
            $files[$key]["current"] = $files[$key][count($files[$key])-1];
            $times[strtotime($files[$key]["current"]["date"])] = $key;
            $files[$key]["path"] = $tmp;
        }
        krsort($times, SORT_NUMERIC);
        foreach ($times as $key => $value) {
            $out .= '<h2>'.$files[$value]["path"].'</h2>
        <div class="charbox">
            <div class="charname">
                '.convertToFBTimestamp($files[$value]["current"]["date"]).'
            </div>
            <table class="chardatatable" style="width: 450px;">
                <tbody>
                    <tr>
                        <th>Revision:</th>
                        <td>'.$files[$value]["current"]["version"].'</td>
                    </tr>
                    <tr>
                        <th>Author:</th>
                        <td>'.$files[$value]["current"]["author"].'</td>
                    </tr>
                    <tr>
                        <th>Comment:</th>
                        <td><div style="border: 1px solid #333333; background-color: #dddddd; padding: 10px; width: 350px;">
                            '.nl2br($files[$value]["current"]["comment"]).'
                        </div></td>
                    </tr>
                </tbody>
            </table>
        </div>';
        }
    }else{
        $out = '<h2>Error</h2>This feature has been disabled.';
    }
    return $out;
}
        
?>