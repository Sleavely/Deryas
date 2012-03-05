<?php

function accmgrmod_lostAccount(){
	// STEP 1: enter email
	if(!isset($_REQUEST['lostaccmailtxt'])){
		$output = 'This form will help you reset your password. If you\'ve forgotten your username you will need to contact an administrator.
					<form id="lostaccmailform" method="post" action="index.php?subtopic=accountmanagement&action=lostaccount">
						<label for="lostaccmailtxt">E-mail: </label><input type="text" class="textinput" name="lostaccmailtxt" id="lostaccmailtxt" />
						<a class="buttonsubmit abutton positive" href="#">
							<img src="images/icons/lock_go.png" alt=""/>
							Proceed
						</a>
					</form>';
	}else{
		// STEP 2: answer question of account of email
		if(!isset($_REQUEST['lostaccanswertxt'])){
			$question_query = db_query('SELECT 3ha.value FROM 3h_accounts AS 3ha WHERE 3ha.type = "secretquestion" AND NOT (3ha.value = "") AND 3ha.account_id = (SELECT id FROM accounts WHERE email = "'.db_escape($_REQUEST['lostaccmailtxt']).'")');
			if(mysql_num_rows($question_query) > 0){
				$question_result = mysql_fetch_object($question_query);
				$output = '<form id="lostaccquestionform" method="post" action="index.php?subtopic=accountmanagement&action=lostaccount">
								<input type="hidden" name="lostaccmailtxt" value="'.htmlentities($_REQUEST['lostaccmailtxt']).'" />
								<label for="lostaccanswertxt">'.htmlentities($question_result->value).' </label><input type="text" class="textinput" name="lostaccanswertxt" id="lostaccanswertxt" />
								<a class="buttonsubmit abutton positive" href="#">
									<img src="images/icons/lock_go.png" alt=""/>
									Proceed
								</a>
							</form>';
			}else{
				$output = 'Failed to retrieve secret question. Please check your spelling and try again. If the problem persists please contract an administrator.';
			}
		}else{
			//STEP 3: change password
			$answer_query = db_query('SELECT * FROM 3h_accounts AS 3ha WHERE 3ha.type = "secretanswer" AND NOT (3ha.value = "") AND 3ha.value = "'.db_escape($_REQUEST['lostaccanswertxt']).'" AND 3ha.account_id = (SELECT id FROM accounts WHERE email = "'.db_escape($_REQUEST['lostaccmailtxt']).'")');
			if(mysql_num_rows($answer_query) > 0){
				//user is verified
				$answer_result = mysql_fetch_object($answer_query);
				$lostaccount = new user($answer_result->account_id);
				
				//make new pwd and save it
				$newpwd = $lostaccount->makePassword();
				$lostaccount->setPassword($newpwd);
				
				//show new password to user
				$output = '<strong>Success!</strong><br />
							Your password has been reset. Your new password is:<br />
							<pre style="border: 1px solid #333333; background-color: #cccccc; padding: 10px; margin: 10px; width: 75px; letter-spacing:2px">'.$newpwd.'</pre>';
				
			}else{
				//most likely the answer is wrong, but its also possible that the email provided is wrong
				$output = '<strong>Wrong answer.</strong><br />Your IP has been recorded and the party van will be arriving shortly.';
			}
		}
	}
    $page_content = '<h2>Account Recovery</h2>
					<div class="charbox">
						<div class="charbuttons">
							'.$output.'
						</div>
					</div>';
    return $page_content;
}

?>
