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

        case "create":
            if(isset($_REQUEST['newguildname'])){
                if(strlen($_REQUEST['newguildname']) >= 3 && strlen($_REQUEST['newguildname']) < 21){
                    if(preg_match('/^the /', strtolower($_REQUEST['newguildname'])) === 0){
                        if(preg_match('/^[A-Z][a-z]+(( of){0,1}[ ][A-Z][a-z]+){0,1}$/', $_REQUEST['newguildname']) === 1){
                            if(isset($_REQUEST['newguildowner']) && intval($_REQUEST['newguildowner']) > 0){
                                //owner exists and belongs to $user?
                                $exist_and_belong_query = 'SELECT name FROM players WHERE id = '.intval($_REQUEST['newguildowner']).' AND account_id = '.$user->id.' AND rank_id = 0 AND level >= '.$config->guildminimumlevel;
                                if(db_query_num($exist_and_belong_query) === 1){
                                    //guild name taken?
                                    $guildname_available_query = 'SELECT id FROM guilds WHERE name = "'.db_escape($_REQUEST['newguildname']).'"';
                                    if(db_query_num($guildname_available_query) === 0){
                                        //guilds
                                        $createGuild_query = db_query('INSERT INTO guilds (world_id, name, ownerid, creationdata, motd) VALUES ('.$config->worldid.', "'.db_escape($_REQUEST['newguildname']).'", '.intval($_REQUEST['newguildowner']).', UNIX_TIMESTAMP(), "")');
                                        //aac_guilds
                                        $getGuild_query = db_query_row($guildname_available_query);
                                        db_query('INSERT INTO aac_guilds (id, type, creationdate, description) VALUES ('.$getGuild_query["id"].', 1, UNIX_TIMESTAMP(), "")');
                                        //aac_guild_ranks
                                        db_query('INSERT INTO aac_guild_ranks (id, displaylevel) SELECT id, level FROM guild_ranks WHERE guild_id = '.$getGuild_query["id"]);
                                        //players set rank_id
                                        $getRank_query = db_query_row('SELECT id FROM guild_ranks WHERE guild_id = '.$getGuild_query["id"].' AND level = 3');
                                        db_query('UPDATE players SET rank_id = '.$getRank_query["id"].' WHERE id = '.intval($_REQUEST['newguildowner']));
                                        //acitvity
                                        $activity->add('createguild', intval($_REQUEST['newguildowner']), $getGuild_query["id"]);
                                        //echo
                                        echo json_encode(array('success' => 1, 'responsetext' => '<a href="?subtopic=guilds&name='.urlencode($_REQUEST['newguildname']).'">'.$_REQUEST['newguildname'].'</a> has been created.'));
                                    }else{
                                        $errormsg = 'Guild name is taken.';
                                    }
                                }else{
                                    $errormsg = 'One or more of the following applies:<ul><li>the character does not exist</li><li>the character is not yours</li><li>the character is already in a guild</li><li>the character is not level '.$config->guildminimumlevel.'</li></ul>';
                                }
                            }else{
                                $errormsg = 'Owner is not defined.';
                            }
                        }else{
                            $errormsg = 'Invalid name.<br /><br /> PS. Don\'t forget that names start with capital letters!';
                        }
                    }else{
                        $errormsg = 'Name cant begin with "the ", it will say "<i>Leader of the Guildname</i>" anyway.';
                    }
                }else{
                    $errormsg = 'Name must be between 3 and 20 letters.';
                }
            }else{
                $errormsg = 'No guild name set.';
            }
            if(isset($errormsg)) echo json_encode(array('success' => 0, 'errormsg' => $errormsg));
            break;
			
		case 'edit':
			if(isset($_REQUEST['guildname'])){
				//lets see if it exists, and if so, load it
				$guild_query = db_query('SELECT g.id, g.name, g.ownerid, p.name AS ownername, p.account_id as owneraccount, a.creationdate, a.alliance, a.description, a.logo, g.motd FROM guilds AS g, aac_guilds AS a, players AS p WHERE g.id = a.id AND p.id = g.ownerid AND g.name = "'.db_escape($_REQUEST['guildname']).'"');
				if(mysql_num_rows($guild_query) === 1){
					$guild = mysql_fetch_array($guild_query);
					//owner belongs to $user?
					if($guild['owneraccount'] == $user->id){
					
						//lets see what it is exactly we want to do
						switch($_REQUEST['subaction']){
							case 'meta':
								if(isset($_REQUEST['metadesc']) && isset($_REQUEST['metamotd'])){
									//which ones changed?
									$changed = array();
									
									if($_REQUEST['metadesc'] != $guild['description']){
										//update db
										db_query('UPDATE aac_guilds SET description = "'.db_escape($_REQUEST['metadesc']).'" WHERE id = '.$guild['id']);
										$changed[] = 'description';
									}
									
									if($_REQUEST['metamotd'] != $guild['motd']){
										db_query('UPDATE guilds SET motd = "'.db_escape($_REQUEST['metamotd']).'" WHERE id = '.$guild['id']);
										$changed[] = 'message of the day';
									}
									
									//output
									if(count($changed) > 0){
										echo json_encode(array('success' => 1, 'responsetext' => '<strong>'.ucfirst(implode(' and ',$changed).' has been saved.').'</strong>'));
										break 2;
									}else{
										$errormsg = 'These are already the current settings!';
									}
								}else{
									$errormsg = 'Description and/or MOTD is missing.';
								}
								break;
								
							default:
								$errormsg = 'Invalid subaction.';
						}
						
					}else{
						$errormsg = 'You are not the owner of '.$guild['name'].'!';
					}
				}else{
					$errormsg = 'Invalid guild.';
				}
			}else{
				$errormsg = 'No guild name set.';
			}
			if(isset($errormsg)) echo json_encode(array('success' => 0, 'errormsg' => $errormsg));
			break;

        default:
            echo json_encode(array('success' => 0, 'errormsg' => 'Invalid action.'));
    }

}else{
    echo json_encode(array('success' => 0, 'errormsg' => 'Not logged in. Reload the page to login again.'));
}

?>
