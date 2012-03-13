<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

$page_title = 'Register Account';
$page_content = '<h2>Registration Form</h2>
				<div class="charbox">
					<div class="charbuttons">
						Account Name <input class="textinput" type="text" name="accname" id="accname" spellcheck="false"> <br />
						Password  <input class="textinput" type="password" name="accpwdone" id="accpwdone" spellcheck="false"> <br />
						Password again  <input class="textinput" type="password" name="accpwdtwo" id="accpwdtwo" spellcheck="false"> <br />
						Email <input class="textinput" type="text" name="accmail" id="accmail" spellcheck="false"><br />
						Captcha? Maybe.<br />
						<br />
						Want an account? Let us know.<br />
						You don\'t know who to talk to? Wait until the site is finished.
					</div>
				</div>
				';
?>
