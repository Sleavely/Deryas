<?php if(!isset($loadedProperly)) exit('Unhappy cat is unhappy with you.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $page_title . ' - '.$config->servername; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="styles.css" media="all" />
    <!-- link rel="stylesheet" href="jquery-ui-1.7.2.custom.css" type="text/css" media="all" / -->
    <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="js/ui.js"></script>
    <script type="text/javascript" src="js/aac.php?subtopic=<?php echo $page_js; ?>"></script>
</head>

<body>
	<div id="container">
		<div id="header">
			<h1><?php echo $config->serverlogo; ?></h1>
			<h3><?php echo $config->serverslogan; ?></h3>
		</div>
		<div id="nav">
			<div id="navheader">Navigation</div>
                        <div id="navcontent">

                            <h3><a href="#nav-home">Home</a></h3>
                            <div>
                                <a class="indent" href="?subtopic=latestnews">News</a>
                                <?php if($config->debug){ ?><a class="indent" href="?subtopic=changelog">Changelog</a><?php } ?>
                                <?php if($config->debug){ ?><a class="indent" href="#"><span style="text-decoration: line-through;">Server Info</span></a><?php } ?>
                            </div>

                            <h3><a href="#nav-account">Account</a></h3>
                            <div>
                                <?php if ($logged_in === false){ ?>
                                <a class="indent" href="?subtopic=accountmanagement">Login</a>
                                <a class="indent" href="?subtopic=createaccount"><span style="text-decoration: line-through;">Create</span></a>
                                <?php } ?>
                                <?php if ($logged_in === true){ ?>
                                <a class="indent" href="?subtopic=accountmanagement">Manage Account</a>
								<?php if($config->premiumtokens){ ?>
                                <a class="indent" href="?subtopic=premiumtokens">Premium Tokens</a>
								<a class="indent" href="?subtopic=accountmanagement&action=shop">Shop</a>
								<?php } ?>
                                <?php if ($user->permissions->isAdmin === true){ ?>
                                <a class="indent" href="?subtopic=admin">Admin</a>
                                <?php } ?>
                                <a class="indent" href="?subtopic=logout">Logout</a>
                                <?php } ?>
                                
                            </div>

                            <h3><a href="#nav-community">Community</a></h3>
                            <div>
                                <a class="indent" href="?subtopic=characters">Characters</a>
                                <a class="indent" href="?subtopic=whoisonline">Online List</a>
                                <a class="indent" href="?subtopic=guilds">Guilds</a>
                                <?php if($config->debug){ ?><a class="indent" href="?subtopic=houses">Houses</a><?php } ?>
                                <a class="indent" href="http://otfans.net/forumdisplay.php?552-Ramurika" target="_blank">Forum</a>
                            </div>

                            <h3><a href="#nav-statistics">Statistics</a></h3>
                            <div>
                                <a class="indent" href="?subtopic=highscores">Highscores</a>
                                <?php if($config->debug){ ?><a class="indent" href="#kills"><span style="text-decoration: line-through;">Kills</span></a><?php } ?>
                            </div>

                            <h3><a href="#nav-library">Library</a></h3>
                            <div>
                                <?php if($config->debug){ ?><a class="indent" href="?subtopic=creatures">Creatures</a><?php } ?>
                                <a class="indent" href="?subtopic=maps">Maps</a>
                                <?php if($config->debug){ ?><a class="indent" href="?subtopic=spells">Spells</a><?php } ?>
                            </div>

                            <h3><a href="#nav-support">Support</a></h3>
                            <div>
                                <?php if($config->debug){ ?><a class="indent" href="#"><span style="text-decoration: line-through;">Server Info</span></a><?php } ?>
                                <a class="indent" href="?subtopic=rules">Rules</a>
                                <a class="indent" href="?subtopic=faq">FAQ</a>
                                <a class="indent" href="?subtopic=staff">Staff</a>
                                <a class="indent" href="?subtopic=contact">Contact</a>
                            </div>

                        </div>
		</div>
        
		<div id="content">
                    
                    <?php if($logged_in === true){ ?>
                    <div style="text-align: center;">
                        <a href="?subtopic=accountmanagement" class="abutton">
                            <img src="images/icons/table_edit.png" alt=""/>
                            Manage Account
                        </a>
						<?php if($config->premiumtokens){ ?>
                        <a href="?subtopic=premiumtokens" class="abutton positive">
                            <img src="images/icons/money.png" alt=""/>
                            Premium Tokens
                        </a>
						<?php } ?>
                        <a href="?subtopic=logout" class="abutton negative">
                            <img src="images/icons/door_open.png" alt=""/>
                            Log Out
                        </a>
                    </div>
                    <?php } ?>

                    <?php echo $page_content; ?>

		</div>


		<div id="rightpane">
                    <div id="serverstatus">
                        <?php require_once('pages/module_status.php'); ?>
                    </div>
                    <div id="didyouknow">
                        <?php require_once('pages/module_didyouknow.php'); ?>
                    </div>
                    <div id="activity">
                        <?php require_once('pages/module_activity.php'); ?>
                    </div>
                        
                </div>
        
		<div id="footer">
			<p>
                            THS AAC &copy; Copyright 2010-<?php echo date('Y'); ?> | <a href="http://triplehead.net" target="_blank">Triplehead Solutions</a><br />
                            <span id="pageload"><?php require_once('pages/module_pageload.php'); ?></span>
                        </p>
                        
	 	</div>
	</div>
</body>
</html>
