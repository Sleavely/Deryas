<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

// check login, then check for action
if ($logged_in === true || (isset($_REQUEST['action']) && $_REQUEST['action'] == "lostaccount")){

    if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'superrandomstringtoprovocedefaultbehavior';
    switch($_REQUEST['action']){
        case "lostaccount":
            require_once('page_accountmanagement/module_lostaccount.php');
            $page_title = 'Lost Account';
            $page_content = accmgrmod_lostAccount();
            break;
        case "editcharacter":
            require_once('page_accountmanagement/module_editchar.php');
            $page_title = 'Edit Character';
            $page_content = accmgrmod_editChar($user, $activity, $config);
            break;
        case "deletecharacter":
            require_once('page_accountmanagement/module_deletechar.php');
            $page_title = 'Delete Character';
            $page_content = accmgrmod_deleteChar($user, $config, $activity);
            break;
        default:
            //Standard logged-in page.
            $page_title = 'Account Management';
            if ($user->characterAmount > 0){
                $chars_text = '';
                $c_offset = 0;
                while ($c_offset < $user->characterAmount) {
                    $chars_text .= '
                    <div class="charbox">
                        <div class="charname">
                            '.$user->characters[$c_offset]["name"].'
                        </div>
                        <div class="chardesc">
                            Level '.$user->characters[$c_offset]["level"].' '.$config->vocations[$user->characters[$c_offset]["vocation"]].'
                        </div>
                        <div class="charbuttons">
                            <a href="?subtopic=accountmanagement&action=editcharacter&name='.urlencode($user->characters[$c_offset]["name"]).'" class="abutton">
                                <img src="images/icons/pencil.png" alt=""/>
                                Edit
                            </a>
                            <a href="?subtopic=accountmanagement&action=deletecharacter&name='.urlencode($user->characters[$c_offset]["name"]).'" class="abutton negative">
                                <img src="images/icons/delete.png" alt=""/>
                                Delete
                            </a>
                        </div>
                    </div>';
                    $c_offset++;
                }
            }else{
                $chars_text = '
                <div class="charbox">
                    <div class="charbuttons">
                        <a href="#" class="abutton positive viewcharcreatebutton">
                            <img src="images/icons/add.png" alt=""/>
                            Create Character
                        </a>
                    </div>
                </div>';
            }
            if ($user->permissions->isAdmin === true){
                $admin_text = '
                <h2>Admin</h2>
                <div class="charbox">
                    <div class="charbuttons">
                        '.($user->permissions->post_news === true ? '
                        <a href="?subtopic=admin&action=news" class="abutton">
                            <img src="images/icons/transmit.png" />
                            Add News
                        </a>' : '').'
                        '.($user->permissions->sessions === true ? '
                        <a href="#" class="abutton">
                            <img src="images/icons/eye.png" />
                            Sessions
                        </a>' : '').'
                        '.($user->permissions->transactions === true && $config->premiumtokens ? '
                        <a href="#" class="abutton">
                            <img src="images/lordofultima-goldicon.png" />
                            Transactions
                        </a>' : '').'
                        '.($user->permissions->reports === true ? '
                        <a href="#" class="abutton negative">
                            <img src="images/icons/exclamation.png" />
                            Reports
                        </a>' : '').'
                        '.($user->permissions->svnlog === true ? '
                        <a href="?subtopic=admin&action=svnlog" class="abutton">
                            <img src="images/icons/folder_magnify.png" />
                            SVN Log
                        </a>' : '').'
                        '.($user->permissions->inspiration === true ? '
                        <a href="?subtopic=admin&action=inspiration" class="abutton">
                            <img src="images/icons/images.png" />
                            Inspiration
                        </a>' : '').'
                        '.($user->permissions->import === true ? '
                        <a href="?subtopic=admin&action=import" class="abutton">
                            <img src="images/icons/server_database.png" />
                            Import
                        </a>' : '').'
						'.($user->permissions->phpinfo === true ? '
                        <a href="?subtopic=admin&action=phpinfo" class="abutton">
                            <img src="images/icons/tux.png" />
                            phpinfo()
                        </a>' : '').'
                        <a href="#" class="abutton" id="stuffbutton">
                            <img src="images/icons/hourglass.png" />
                            Stuff
                        </a>
                    </div>
                </div>';
            }else{
                $admin_text = '';
            }
            $passwordage_array = dates_diff($user->passwordage, time());
            if ($passwordage_array["month"] >= 1 || $passwordage_array["year"] > 0){
                if ($passwordage_array["month"] === 1 && $passwordage_array["year"] === 0){
                    $passwordtext = '1 month old';
                }elseif($passwordage_array["month"] > 1 && $passwordage_array["year"] === 0){
                    $passwordtext = $passwordage_array["month"].' months old';
                }elseif($passwordage_array["year"] === 1){
                    $passwordtext = '1 year old';
                }else{
                    $passwordtext = $passwordage_array["year"].' years old';
                }
            }else{
                $passwordtext = ($passwordage_array["day"] === 0 ? 'New' : ($passwordage_array["day"] === 1 ? '1 day old' : $passwordage_array["day"].' days old'));
            }
            $availableVocations = explode(",", $config->newcharacters["vocations"]);
            $page_content = '
<div>
    <h2>Account Information</h2>

        <div class="smallbox">
            <div class="smalltext" id="userdatacharacters">
                <span>Characters:</span> '.$user->characterAmount.'
            </div>
            <a href="#charcreateform" class="abutton positive viewcharcreatebutton" style="width: 150px;">
                <img src="images/icons/add.png" alt=""/>
                Add Character
            </a>
        </div>
        <div class="smallbox">
            <div class="smalltext">
                <span>Status:</span> '.($user->premdays > 0 ? 'Premium Account' : 'Free Account').'
            </div>
			'.($config->premiumtokens ? '
            <a href="?subtopic=premiumtokens" class="abutton positive" style="width: 150px;">
                <img src="images/icons/money.png" alt=""/>
                Premium Tokens
            </a>' : '').'
        </div>
        <div class="smallbox">
            <div class="smalltext" id="userdatapassword">
                <span>Password Age:</span> '.$passwordtext.'
            </div>
            <a href="#" class="abutton" style="width: 150px;" onclick="$(\'#changepwdform\').toggle(500);">
                <img src="images/icons/textfield_key.png" alt=""/>
                Change Password
            </a>
            <div class="editaccinfo" id="changepwdform">
                <label for="changepwdtxtone">New password: </label><input type="text" name="changepwdtxtone" id="changepwdtxtone" /><br />
                <label for="changepwdtxttwo" style="margin-left: 14px;">Confirm new: </label><input type="text" name="changepwdtxttwo" id="changepwdtxttwo" />
                <a href="#" class="abutton positive" id="changepwdbtn" style="bottom: 15px;">
                    <img src="images/icons/accept.png" alt=""/> Submit
                </a>
            </div>
        </div>
		<div class="smallbox">
            <div class="smalltext" id="userdatasecretquestion">
                <span>Secret Question:</span> '.( strlen($user->secretquestion) > 0 ? $user->secretquestion : '<i>You have no secret question.</i>' ).'
            </div>
            <a href="#" class="abutton" style="width: 150px;" onclick="$(\'#changesecretquestionform\').toggle(400);">
                <img src="images/icons/lock_edit.png" alt=""/>
                Change Question
            </a>
            <div class="editaccinfo" id="changesecretquestionform">
                <label for="changequestiontxt">New question: </label><input type="text" name="changequestiontxt" id="changequestiontxt" /><br />
                <label for="changeanswertxt" style="margin-left: 7px;">New answer: </label><input type="text" name="changeanswertxt" id="changeanswertxt" />
                <a href="#" class="abutton positive" id="changesecretquestionbtn" style="bottom: 15px;">
                    <img src="images/icons/accept.png" alt=""/> Save
                </a>
            </div>
        </div>
        <div class="smallbox">
            <div class="smalltext" id="userdatarealname">
                <span>Real Name:</span> '.$user->realname.'
            </div>
            <a href="#" class="abutton" style="width: 150px;" onclick="$(\'#changenameform\').toggle(400);">
                <img src="images/icons/information.png" alt=""/>
                Edit Name
            </a>
            <div class="editaccinfo" id="changenameform">
                <label for="changenametxt">New Name: </label><input type="text" maxlength="25" name="changenametxt" id="changenametxt" />
                <a href="#" class="abutton positive" id="changenamebtn">
                    <img src="images/icons/accept.png" alt=""/> Submit
                </a>
            </div>
        </div>
        <div class="smallbox">
            <div class="smalltext">
                <span>Location:</span> '.($user->location == null ? 'Unknown' : $user->location).'
            </div>
			'.( $config->debug ? '
            <a href="#" class="abutton" style="width: 150px;">
                <img src="images/icons/map.png" alt=""/>
                Change Location
            </a>' : '' ).'
        </div>
        <div class="smallbox">
            <div class="smalltext" id="userdataemail">
                <span>Email:</span> '.$user->email.'
            </div>
            <a href="#" class="abutton" style="width: 150px;" onclick="$(\'#changemailform\').toggle(500);">
                <img src="images/icons/email_edit.png" alt=""/>
                Change Email
            </a>
            <div class="editaccinfo" id="changemailform">
                <label for="changemailtxtone" style="margin-left: 22px;">New Address: </label><input type="text" name="changemailtxtone" id="changemailtxtone" /><br />
                <label for="changemailtxttwo">Confirm Address: </label><input type="text" name="changemailtxttwo" id="changemailtxttwo" />
                <a href="#" class="abutton positive" id="changemailbtn" style="bottom: 15px; right: 40px;">
                    <img src="images/icons/accept.png" alt=""/> Submit
                </a>
            </div>
        </div>
        <!-- div class="smallbox">
            <div class="smalltext">
                <span>Registered:</span> i dunno, are you?
            </div>
            <a href="#" class="abutton" style="width: 150px;">
                <img src="images/icons/table_link.png" alt=""/>
                Register Account
            </a>
        </div -->

    <h2>Characters</h2>
    <div id="characterlist">'.$chars_text.'</div>
    <div id="charcreateform" style="display: none;">
        <div class="charbox">
            <div class="charbuttons">
                <a href="#characterlist" class="abutton hidecharcreatebutton">
                    <img src="images/icons/arrow_left.png" alt=""/>
                    Back to Character List
                </a><br />
            </div>
            <div class="charbuttons" style="line-height: 30px; border-top: 1px solid #AAAAAA; padding-top: 10px;">
                <label for="newcharname" style="margin-left: 20px;">Name:</label> <input class="textinput" type="text" name="newcharname" id="newcharname" /><br />
                <label style="margin-left: 10px;">Gender:</label>
                <input type="radio" name="newchargender" id="newchargendermale" value="1" checked="checked"/><label for="newchargendermale" style="margin-right: 15px;"> Male </label>
                <input type="radio" name="newchargender" id="newchargenderfemale" value="0" /><label for="newchargenderfemale"> Female </label><br />';
                //available vocations
                if(count($availableVocations) > 1){
                    $pcav = '';
                    foreach($availableVocations as $v){
                        $pcav .= '<option value="'.$v.'">'.$config->vocations[$v].'</option>';
                    }
                    $page_content .= '
                    <label for="newcharvoc">Vocation:</label>
                    <select name="newcharvoc" id="newcharvoc">
                        '.$pcav.'
                    </select><br />';
                }
                //available towns
                $availableTowns = array();
                foreach($config->towns as $tid => $tinfo){
                    if($tinfo["selectable"] === true){
                        $availableTowns[] = $tid;
                    }
                }
                if(count($availableTowns) > 1){
                    $pcac = '';
                    foreach($availableTowns as $t){
                        $pcac .= '<option value="'.$t.'">'.$config->towns[$t]["name"].'</option>';
                    }
                    $page_content .= '
                    <label for="newchartown" style="margin-left: 21px;">Town:</label>
                    <select name="newchartown" id="newchartown">
                        '.$pcac.'
                    </select><br />';
                }
                $page_content .= '
                <a class="abutton" style="margin-left: 65px;" id="proceedcharcreatebutton" href="#charcreateform">
                    Proceed
                    <img alt="" src="images/icons/arrow_right.png"/>
                </a>
            </div>
        </div>
    </div>
    <div id="charcreateresponse" style="display: none;">
        <div class="charbox">
            <div class="charbuttons">
                <a class="abutton hidecharcreatebutton" id="charcreateresponsebutton" href="#characterlist">
                    <img alt="" src="images/icons/arrow_left.png"/>
                    Back
                </a><br />
            </div>
            <div class="charbuttons" style="line-height: 30px; border-top: 1px solid #AAAAAA; padding-top: 10px;">
                <p id="charcreateresponsetext">If you see this text something isn\'t working properly.</p>
            </div>
        </div>
    </div>

    '.$admin_text.'
</div>
';
    }
}else{
    $page_title = 'Account Management';
    printLogin();
}

?>
