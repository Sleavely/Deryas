<?php

function adminmod_import($user){
    $out = '<h2>Data Import</h2>';
    $out .= '<div class="charbox">
                Importing these things can take a lot of time, be patient if the page takes a lot of time to load.<br />
                <div class="charbuttons">
                    <a href="?subtopic=admin&action=import&import=items" class="abutton">
                        Import Items
                    </a>
                    <a href="?subtopic=admin&action=import&import=creatures" class="abutton">
                        Import Creatures
                    </a>
                    <a href="?subtopic=admin&action=import" class="abutton">
                        Import Spells
                    </a>
                </div>
            </div>';
    if(isset($_REQUEST['import'])){
        switch($_REQUEST['import']){
            case "items":
                db_query('DELETE FROM aac_items');
                $out .= '<div class="charbox">
                            <div class="charbuttons">
                                <div style="font-size: 16px; color: #cc0000;">Imported <i>"./files/items.xml"</i></div>';
                $xml = simplexml_load_file("./files/items.xml");
                $out .= '<table class="chardatatable">';
                $querybuffer = array();
                $buffer = 0;
                foreach($xml->children() as $child){
                    if($buffer >= 10){
                        $bufferstrings = '';
                        foreach($querybuffer as $item){
                            $bufferstrings .= '('.$item["id"].',"'.db_escape($item["name"]).'",'.$item["article"].')';
                        }
                        $bufferstrings = str_replace(')(','),(',$bufferstrings);
                        db_query('INSERT INTO aac_items (itemid,name,article) VALUES '.$bufferstrings,true);
                        $buffer = 0;
                        $querybuffer = array();
                    }
                    if(isset($child["article"])){
                        $article = '"'.db_escape($child["article"]).'"';
                    }else{
                        $article = "null";
                    }
                    if(isset($child["id"])){
                        $querybuffer[] = array('id' => $child["id"], 'name' => $child["name"], 'article' => $article);
                        $buffer++;
                        $out .= '<tr><th>'.$child["id"].'</th><td>'.ucwords($child["name"]).'</td><td>'.$article.'</td></tr>';
                    }elseif(isset($child["fromid"])){
                        $current = (int)$child["fromid"];
                        while($current <= $child["toid"]){
                            $querybuffer[] = array('id' => $current, 'name' => $child["name"], 'article' => $article);
                            $out .= '<tr><th>'.$current.'</th><td>'.ucwords($child["name"]).'</td><td>'.$article.'</td></tr>';
                            $current++;
                            $buffer++;
                        }
                    }
                }
                if($buffer >= 1){
                        $bufferstrings = '';
                        foreach($querybuffer as $item){
                            $bufferstrings .= '('.$item["id"].',"'.db_escape($item["name"]).'",'.$item["article"].')';
                        }
                        $bufferstrings = str_replace(')(','),(',$bufferstrings);
                        db_query('INSERT INTO aac_items (itemid,name,article) VALUES '.$bufferstrings,true);
                        $buffer = 0;
                        $querybuffer = array();
                    }
                $out .= '</table></div></div>';
                break;

            case "creatures":
                db_query('DELETE FROM aac_creatures');
                $out .= '<div class="charbox">
                            <div class="charbuttons">
                                <div style="font-size: 16px; color: #cc0000;">Imported <i>./files/monsters/</i> via <i>"./files/monsters.xml"</i></div>
                            <table class="chardatatable">';
                $xml = simplexml_load_file("./files/monsters.xml");
                if(isset($_REQUEST['bajs'])){
                    $monster = simplexml_load_file("./files/monsters/goblins/goblin.xml");
                    var_dump($monster);
                    exit;
                }
                foreach($xml->children() as $monsterfile){
                    $monster = simplexml_load_file("./files/monsters/".$monsterfile["file"]);
                    $name = $monster["name"];
                    $experience = $monster["experience"];
                    $health = $monster->health["now"];
                    $summonable = 0;
                    $illusionable = 0;
                    $convinceable = 0;
                    foreach($monster->flags->children() as $flag){
                        foreach($flag->attributes() as $flagattr => $flagval){
                            if($flagattr == "summonable"){
                                $summonable = (int)$flagval;
                            }elseif($flagattr == "illusionable"){
                                $illusionable = (int)$flagval;
                            }elseif($flagattr == "convinceable"){
                                $convinceable = (int)$flagval;
                            }
                        }
                    }
                    $elements = array();
                    if(isset($monster->elements)){
                        foreach($monster->elements->children() as $element){
                            foreach($element->attributes() as $elattr => $elval){
                                $elements[$elattr] = (string)$element[$elattr];
                            }
                        }
                    }
                    $elements = serialize($elements);
                    $immunities = array();
                    if(isset($monster->immunities)){
                        foreach($monster->immunities->children() as $immunity){
                            foreach($immunity->attributes() as $imattr => $imval){
                                $immunities[$imattr] = (string)$immunity[$imattr];
                            }
                        }
                    }
                    $immunities = serialize($immunities);
                    $loot = array();
                    if(isset($monster->loot)){
                        foreach($monster->loot->children() as $item){
                            if(count($item->children()) > 0){
                                if(isset($item->inside)){
                                    $inside = array();
                                    foreach($item->inside->children() as $insider){
                                        $inside[] = array('itemid' => (string)$insider["id"], 'chance' => (string)$insider["chance"]);
                                    }
                                }
                            }
                            if(isset($inside)){
                                $loot[] = array('itemid' => (string)$item["id"], 'chance' => (string)$item["chance"], 'inside' => $inside);
                            }else{
                                $loot[] = array('itemid' => (string)$item["id"], 'chance' => (string)$item["chance"]);
                            }
                            unset($inside);
                        }
                    }
                    $loot = serialize($loot);
                    db_query('INSERT INTO aac_creatures (name, experience, health, summonable, illusionable, convinceable, elements, immunities, loot) VALUES ("'.db_escape($name).'",'.intval($experience).','.intval($health).','.intval($summonable).','.intval($illusionable).','.intval($convinceable).',"'.db_escape($elements).'","'.db_escape($immunities).'","'.db_escape($loot).'")');
                    $out .= '<tr><td>'.$name.'</td><td>'.$experience.'</td><td>'.$health.'</td><td>'.$summonable.'</td><td>'.$illusionable.'</td><td>'.$convinceable.'</td></tr>';
                    unset($singlefile);
                }
                $out .= '</table></div></div>';
                break;

            default:
                break;
        }
    }

    return $out;
}

?>
