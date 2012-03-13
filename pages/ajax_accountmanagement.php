<?php
if(!isset($loadedProperly)){
    echo json_encode(array('success' => 0, 'errormsg' => 'Page was not loaded properly.'));
    exit;
}

if($logged_in){
    //check for subtopic querystring
    if (!isset($_REQUEST['action'])){
        echo json_encode(array('success' => 0, 'errormsg' => 'No action'));
        exit;
    }
    //determine action q-string
    switch($_REQUEST['action']){

        case "addcharacter":
            /*
             *** if:
             * name isset
             * gender isset
             * more than 1 voc in config & isset $_REQUEST voc & !empty() & is voc from config & is selectable
             * same as above, but city
             * name is valid
             * name isnt banned/namelocked
             * player has 15 chars?
             */
            if(isset($_REQUEST['newcharname']) && !empty($_REQUEST['newcharname'])){
                if(isset($_REQUEST['newchargender']) && intval($_REQUEST['newchargender'] < 2)){ //gender is 0 (female) or 1 (male)
                    $_REQUEST['newchargender'] = intval($_REQUEST['newchargender']);
                    $availableVocations = explode(",", $config->newcharacters["vocations"]);
                    if(count($availableVocations) > 1){ //theres more than one to choose from
                        if(isset($_REQUEST['newcharvoc'])){ //make sure one has been selected
                            $_REQUEST['newcharvoc'] = intval($_REQUEST['newcharvoc']);
                            if(in_array($_REQUEST['newcharvoc'], $availableVocations)){ //is it one of the selectable ones?
                                $validVoc = true;
                            }else{
                                $validVoc = false;
                                $errormsg = 'Unselectable vocation defined';
                            }
                        }else{
                            $validVoc = false;
                            $errormsg = 'No vocation defined';
                        }
                    }else{ //only one can be selected, so its no big deal
                        $_REQUEST['newcharvoc'] = $availableVocations[0];
                        $validVoc = true;
                    }
                    if($validVoc === true){
                        $availableTowns = array();
                        //count availableTowns
                        //if more than one check that one has been submitted and that its one of the available ones
                        //else assign the only selectable one
                        foreach($config->towns as $tid => $town){
                            if($town['selectable'] === true) $availableTowns[] = $tid;
                        }
                        if(count($availableTowns) > 1){
                            if(isset($_REQUEST['newchartown'])){
                                $_REQUEST['newchartown'] = intval($_REQUEST['newchartown']);
                                if(in_array($_REQUEST['newchartown'], $availableTowns)){ //is it one of the selectable ones?
                                    $validTown = true;
                                }else{
                                    $validTown = false;
                                    $errormsg = 'Unselectable vocation defined';
                                }
                            }else{
                                $errormsg = 'No town specified.';
                                $validTown = false;
                            }
                        }else{
                            $_REQUEST['newchartown'] = $availableTowns[0];
                            $validTown = true;
                        }

                        if($validTown === true){
                            //now all that remains is checking NAME (valid format, banned/namelocked) and 15 CHAR LIMIT
                            //lets start with the char limit because it's less CPU intensive
                            $charnum = db_query_num('SELECT id FROM players WHERE account_id = '.$user->id);
                            if($charnum < 15){
                                //checking for namelock is also less CPU intensive, let's do that next
                                if(db_query_num('SELECT id FROM 3h_namelocks WHERE name = "'.db_escape($_REQUEST['newcharname']).'"') === 0){
                                    //i forgot; we should probably see if the name isnt already taken
                                    if(db_query_num('SELECT id FROM players WHERE name = "'.db_escape($_REQUEST['newcharname']).'"') === 0){
                                        //1. Validate Name
                                        //2. ???
                                        //3. Profit

                                        $validName = true;
                                        //Length Check
                                        if(strlen($_REQUEST['newcharname']) >= 3 && strlen($_REQUEST['newcharname']) < 21){
                                            //Format Check
                                            if(preg_match('/^[A-Z][a-z]+([ ][A-Z][a-z]+){0,1}$/', $_REQUEST['newcharname']) === 1){
                                                //Config Forbidden Names Check
                                                foreach($config->forbiddennames as $name){
                                                    $tmpf = str_replace('?','[A-Za-z ]?',$name);
                                                    $tmpf = str_replace('*','[A-Za-z ]*',$tmpf);
                                                    if(preg_match('/^'.$tmpf.'$/i',$_REQUEST['newcharname']) === 1){
                                                        $validName = false;
                                                        $errormsg = 'Forbidden name.';
                                                    }
                                                }
                                            }else{
                                                $validName = false;
                                                $errormsg = 'Invalid name format.';
                                            }
                                        }else{
                                            $validName = false;
                                            $errormsg = 'Name must be between 3 and 20 letters.';
                                        }

                                        if($validName === true){
                                            //PROFIT!
                                            /*
                                             *** Tables to update:
                                             * players - lotsofstuff
                                             * player_skills - stuff
                                             * 3h_activitylog - activity:2, user:{newcharacterID}, timestamp:UNIX_TIMESTAMP()
                                             * 3h_players - player_id:{newcharacterID}, type:created, value:UNIX_TIMESTAMP()
                                             * 3h_players - player_id:{newcharacterID}, type:settings, value:2035
                                             */
                                            db_query('INSERT INTO players
                                            (name,                                     world_id,                              group_id,                              account_id,   level,                              vocation,                   health,                              healthmax,                           experience,                                                                                                                                                                                                                                  lookbody,                                   lookfeet,                                  lookhead,                                  looklegs,                                  looktype,                                                                                                                                          maglevel,                              mana,                              manamax,                              town_id,                             posx,                                                       posy,                                                   posz,                                                     cap,                              sex,                               conditions) VALUES
                                            ("'.db_escape($_REQUEST['newcharname']).'",'.$config->worldid.','.$config->newcharacters["group_id"].','.$user->id.','.$config->newcharacters["level"].','.$_REQUEST['newcharvoc'].','.$config->newcharacters["health"].','.$config->newcharacters["health"].','.((50*($config->newcharacters["level"]-1)*($config->newcharacters["level"]-1)*($config->newcharacters["level"]-1)-150*($config->newcharacters["level"]-1)*($config->newcharacters["level"]-1)+400*($config->newcharacters["level"]-1))/3).','.$config->newcharacters["look"]["body"].','.$config->newcharacters["look"]["feet"].','.$config->newcharacters["look"]["head"].','.$config->newcharacters["look"]["legs"].','.(intval($_REQUEST['newchargender']) === 1 ? $config->newcharacters["look"]["type"]["male"] : $config->newcharacters["look"]["type"]["female"]).','.$config->newcharacters["maglevel"].','.$config->newcharacters["mana"].','.$config->newcharacters["mana"].','.intval($_REQUEST['newchartown']).','.$config->towns[intval($_REQUEST['newchartown'])]["x"].','.$config->towns[intval($_REQUEST['newchartown'])]["y"].','.$config->towns[intval($_REQUEST['newchartown'])]["z"].','.$config->newcharacters["cap"].','.intval($_REQUEST['newchargender']).',"")', true);
                                            $newplayer = db_query_row('SELECT id FROM players WHERE name = "'.db_escape($_REQUEST['newcharname']).'"');
                                            db_query('INSERT INTO player_skills (player_id, skillid, value, count) VALUES ('.$newplayer[0].',0,10,0)'); //fist
                                            db_query('INSERT INTO player_skills (player_id, skillid, value, count) VALUES ('.$newplayer[0].',1,10,0)'); //club
                                            db_query('INSERT INTO player_skills (player_id, skillid, value, count) VALUES ('.$newplayer[0].',2,10,0)'); //sword
                                            db_query('INSERT INTO player_skills (player_id, skillid, value, count) VALUES ('.$newplayer[0].',3,10,0)'); //axe
                                            db_query('INSERT INTO player_skills (player_id, skillid, value, count) VALUES ('.$newplayer[0].',4,10,0)'); //dist
                                            db_query('INSERT INTO player_skills (player_id, skillid, value, count) VALUES ('.$newplayer[0].',5,10,0)'); //shield
                                            db_query('INSERT INTO player_skills (player_id, skillid, value, count) VALUES ('.$newplayer[0].',6,10,0)'); //fish
                                            db_query('INSERT INTO 3h_players (player_id, type, value) VALUES ('.$newplayer[0].',"created",UNIX_TIMESTAMP())');
                                            db_query('INSERT INTO 3h_players (player_id, type, value) VALUES ('.$newplayer[0].',"settings",2035)');
                                            db_query('INSERT INTO 3h_activitylog (activity, user, timestamp) VALUES (2,'.$newplayer[0].',UNIX_TIMESTAMP())');
                                            echo json_encode(array('success' => 1, 'totalcharacters' => $charnum+1, 'name' => $_REQUEST['newcharname'], 'urlname' => urlencode($_REQUEST['newcharname']), 'level' => $config->newcharacters['level'], 'vocation' => $config->vocations[$_REQUEST['newcharvoc']]));
                                        }else{
                                            echo json_encode(array('success' => 0, 'errormsg' => $errormsg));
                                        }
                                    }else{
                                        echo json_encode(array('success' => 0, 'errormsg' => 'Name is taken.'));
                                    }
                                }else{
                                    echo json_encode(array('success' => 0, 'errormsg' => 'Name is banned due to a previous namelock.'));
                                }
                            }else{
                                echo json_encode(array('success' => 0, 'errormsg' => 'You may not create more than 15 characters.'));
                            }
                        }else{
                            echo json_encode(array('success' => 0, 'errormsg' => $errormsg));
                        }

                    }else{
                        echo json_encode(array('success' => 0, 'errormsg' => $errormsg));
                    }
                }else{
                    echo json_encode(array('success' => 0, 'errormsg' => 'Invalid gender.'));
                }
            }else{
                echo json_encode(array('success' => 0, 'errormsg' => 'Character name is empty.'));
            }
            break;

        case "changepassword":
            if (!isset($_REQUEST['changepwdtxtone']) || empty($_REQUEST['changepwdtxtone']) || !isset($_REQUEST['changepwdtxttwo']) || empty($_REQUEST['changepwdtxttwo'])){
                echo json_encode(array('success' => 0, 'errormsg' => 'One field is empty'));
                exit;
            }
            if ($_REQUEST['changepwdtxtone'] != $_REQUEST['changepwdtxttwo']){
                echo json_encode(array('success' => 0, 'errormsg' => 'Fields dont match'));
                exit;
            }
            $newpass = $_REQUEST['changepwdtxtone'];
            $user->setPassword($newpass);
            echo json_encode(array('success' => 1));
            break;
			
		case "changesecretquestion":
			if (!isset($_REQUEST['changequestiontxt']) || empty($_REQUEST['changequestiontxt']) || !isset($_REQUEST['changeanswertxt']) || empty($_REQUEST['changeanswertxt'])){
                echo json_encode(array('success' => 0, 'errormsg' => 'One field is empty'));
                exit;
            }
			$newquestion = $_REQUEST['changequestiontxt'];
			$newanswer = $_REQUEST['changeanswertxt'];
			$questionexists_query = db_query('SELECT 3ha.value FROM 3h_accounts AS 3ha WHERE 3ha.account_id = '.$user->id.' AND 3ha.type = "secretquestion"');
			if(mysql_num_rows($questionexists_query) > 0){
				db_query('UPDATE 3h_accounts AS 3ha SET 3ha.value = "'.db_escape($newquestion).'" WHERE 3ha.account_id = '.$user->id.' AND 3ha.type = "secretquestion"');
				db_query('UPDATE 3h_accounts AS 3ha SET 3ha.value = "'.db_escape($newanswer).'" WHERE 3ha.account_id = '.$user->id.' AND 3ha.type = "secretanswer"');
			}else{
				db_query('INSERT INTO 3h_accounts (account_id, type, value) VALUES ('.$user->id.', "secretquestion", "'.db_escape($newquestion).'")');
				db_query('INSERT INTO 3h_accounts (account_id, type, value) VALUES ('.$user->id.', "secretanswer", "'.db_escape($newanswer).'")');
			}
			echo json_encode(array('success' => 1, 'newquestion' => (strlen($newquestion) > 0 ? $newquestion : '<i>You have no secret question.</i>')));
			break;

        case "changerealname":
            if (!isset($_REQUEST['changenametxt']) || empty($_REQUEST['changenametxt'])){
                echo json_encode(array('success' => 0, 'errormsg' => 'No name'));
                exit;
            }
            $newname = htmlentities($_REQUEST['changenametxt']);
            db_query('UPDATE 3h_accounts AS 3ha SET 3ha.value = "'.db_escape($newname).'" WHERE 3ha.account_id = '.$user->id.' AND 3ha.type = "realname"');
            echo json_encode(array('success' => 1, 'newname' => $newname));
            break;
		
        case "changeemail":
            if (!isset($_REQUEST['changemailtxtone']) || empty($_REQUEST['changemailtxtone']) || !isset($_REQUEST['changemailtxttwo']) || empty($_REQUEST['changemailtxttwo'])){
                echo json_encode(array('success' => 0, 'errormsg' => 'One field is empty'));
                exit;
            }
            if ($_REQUEST['changemailtxtone'] != $_REQUEST['changemailtxttwo']){
                echo json_encode(array('success' => 0, 'errormsg' => 'Fields dont match'));
                exit;
            }
            $newmail = $_REQUEST['changemailtxtone'];
            if (filter_var($newmail, FILTER_VALIDATE_EMAIL) === false){
                echo json_encode(array('success' => 0, 'errormsg' => 'Invalid email'));
                exit;
            }
            db_query('UPDATE accounts SET email = "'.db_escape($newmail).'" WHERE id = '.$user->id);
            echo json_encode(array('success' => 1));
            break;

        default:
            echo json_encode(array('success' => 0, 'errormsg' => 'Invalid action'));
    }
}else{
    echo json_encode(array('success' => 0, 'errormsg' => 'Not Logged In'));
}

?>
