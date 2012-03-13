<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

$page_title = 'Creatures';
$page_content = '<h2>Creatures</h2>
                 <div class="charbox">
                    <div class="charname">
                        <img src="images/icons/magnifier.png" alt=""/> Search
                    </div>
                    <div class="charbuttons">
                        <label for="searchname">Name: </label><input type="text" class="textinput" maxlength="45" size="30" name="searchname" id="searchname"/>
                    </div>
                </div>';

if(!isset($_REQUEST['name'])){
    $creature_query = db_query('SELECT name, experience, health, summonable, illusionable, convinceable, loot FROM aac_creatures ORDER BY name ASC');
    while($creature = mysql_fetch_array($creature_query)){
        $lootArr = unserialize($creature["loot"]);
        $lootQstr = 'SELECT name, article FROM aac_items WHERE itemid IN (0';
        foreach($lootArr as $item){
            $lootQstr .= ','.$item["itemid"];
        }
        $lootQstr = str_replace('IN (,', 'IN (', $lootQstr.')');
        $lootQuery = db_query($lootQstr);
        $lootStr = '';
        while($lootItem = mysql_fetch_array($lootQuery)){
            $lootStr .= ($lootItem["article"] != null ? $lootItem["article"].' ' : '').$lootItem["name"].', ';
        }
        $lootStr = ucfirst(str_replace(', .-.', '.', $lootStr.'.-.'));
        $page_content .= '<div class="charbox">
                            <div class="charimage">
                                <!--img src="http://images4.wikia.nocookie.net/__cb20081107204041/tibia/en/images/4/45/Goblin.gif" alt="Creature Image" /-->
                            </div>
                            <div class="charname">
                                '.$creature["name"].'
                            </div>
                            <div class="charbuttons">
                                <table class="chardatatable">
                                    <tbody>
                                        <tr>
                                            <th>Health</th>
                                            <td>'.$creature["health"].'</td>
                                        </tr>
                                        <tr>
                                            <th>Experience</th>
                                            <td>'.$creature["experience"].'</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="datatable">
                                    <tbody>
                                        <tr>
                                            <th>Summonable</th>
                                            <th>Convinceable</th>
                                            <th>Illusionable</th>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center;">'.($creature["summonable"] == 1 ? '<img src="images/icons/tick.png" alt="Yes" />' : '<img src="images/icons/cross.png" alt="No" />').'</td>
                                            <td style="text-align: center;">'.($creature["convinceable"] == 1 ? '<img src="images/icons/tick.png" alt="Yes" />' : '<img src="images/icons/cross.png" alt="No" />').'</td>
                                            <td style="text-align: center;">'.($creature["illusionable"] == 1 ? '<img src="images/icons/tick.png" alt="Yes" />' : '<img src="images/icons/cross.png" alt="No" />').'</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <strong>Loot</strong>
                                '.($lootStr != ".-." ? '
                                <div>'.$lootStr.'</div>
                                ' : '
                                <div style="font-style: italic;">None.</div>
                                ').'
                                
                            </div>
                        </div>';
    }
}

?>
