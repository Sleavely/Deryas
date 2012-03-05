<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

//////DaoPay Stuff //////
//ApplicationID
$daopay_appid = '56280';
//Product Name
$daopay_productname = '101';

$default_stuff = false;
if ($logged_in === true){
	if($config->premiumtokens){
		if (isset($_REQUEST['vendor'])){
			if ($_REQUEST['vendor'] == 'daopay'){
				if (!isset($_REQUEST['daopaypin'])){
				$page_title = 'Purchase Tokens';
				$page_content = '<h2>Purchase Tokens - DaoPay</h2>
								<p>
									If you have already paid you may enter your pin here:
									<form method="post" action="">
										<input name="daopaypin" id="daopaypin" type="text"/>
										<input type="submit" value="Verify"/>
									</form>
								</p>
								<p>To start a new transaction, click <a href="http://daopay.com/payment/?appcode=56280&prodcode=101">here</a>.</p>
								<div class="author"><a href="javascript:history.go(-1)">Back</a></div>';
				}else{
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, 'http://daopay.com/svc/pincheck?appcode='.$daopay_appid.'&prodcode='.urlencode($daopay_productname).'&pin='.$_REQUEST['daopaypin']);
					curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					$outcome = curl_exec($curl);
					curl_close($curl);
					if(!empty($outcome)) {
						if(substr($outcome, 0, 2) == 'ok') {
							$awesome = true;
						}else{
							$awesome = false;
						}
					}else{
						//Try again later or wrong code.
						$page_title = 'Purchase Tokens';
						$page_content = '<h2>Purchase Tokens - DaoPay</h2>
										<p>Something went wrong, possibly because you entered the wrong PIN-code. If you are sure you didn\'t, just try again later.<div class="author"><a href="javascript:history.go(-1)">Back</a></div>';
					}
					if (isset($awesome)){
						if ($awesome === true){
							$page_title = 'Purchase Tokens';
							$page_content = '<h2>Purchase Tokens - DaoPay</h2>
										<p>Transaction complete. You now have '.$user->premtokens.'.</p>';
						}elseif ($awesome === false){
							$page_title = 'Purchase Tokens';
							$page_content = '<h2>Purchase Tokens - DaoPay</h2>
										<p>Failed to verify your PIN, <a href="javascript:history.go(-1)">try again</a>?</p>
										<p>Please remember that once you\'ve verified your PIN code you will not be able to use it again. To obtain more tokens you will have to purchase more PIN codes.';

						}
						//debugging the server response
						//$page_content = '<a href="javascript:history.go(-1)">try again</a>:<br /><br />'.$outcome;
					}
				}
			}elseif ($_REQUEST['vendor'] == 'paypal'){
				$page_title = 'Purchase Tokens';
				$page_content = '<h2>Purchase Tokens - PayPal</h2>
								<p>Stuff.</p>
								<div class="author"><a href="javascript:history.go(-1)">Back</a></div>';
			}else{
				$default_stuff = true;
			}
		}else{
			$default_stuff = true;
		}

		if ($default_stuff === true){
			$page_title = 'Premium Tokens';
			$page_content = '<h2>Token Balance</h2>
			<p>You have '.$user->premtokens.' tokens.</p>
			<h2>Purchase Tokens</h2>
			<p>We currently offer two payment methods, one by credit card (PayPal) and one by phone (DaoPay). <i>DaoPay pays us less</i>, thus using them will grant you fewer premium tokens.</p>
			<dl>
				<dt>
					<a href="?subtopic=premiumtokens&vendor=paypal" class="abutton" style="margin-left: 7px; width: 90px;">
						<img src="images/icons/creditcards.png" alt=""/>
						PayPal
					</a>
				</dt>
				<dt>
					<a href="?subtopic=premiumtokens&vendor=daopay" class="abutton" style="margin-left: 7px; width: 90px;">
						<img src="images/icons/telephone.png" alt=""/>
						DaoPay
					</a>
				</dt>
			</dl>
			<div class="author"><img src="images/premiumtokens.png"/></div>';
		}
	}else{
		$page_title = 'Premium Tokens';
		$page_content = '<h2>Premium Tokens</h2>
						<div class="smallbox">
							<div class="smalltext" style="color: #cc0000;">
								Premium tokens are disabled.
							</div>
						</div>';
	}
}else{
    $page_title = 'Premium Tokens';
    printLogin();
}

?>
