<?php
//make sure other files are loaded through either index.php or ajax.php
$loadedProperly = true;

//Page load stats.
$db_queries = 0;
$startload = microtime();

require_once('init.php');

if (!isset($_REQUEST['subtopic'])){
    $_REQUEST['subtopic'] = 'latestnews';
}
$_REQUEST['subtopic'] = strtolower($_REQUEST['subtopic']);
if(file_exists('pages/page_'.$_REQUEST['subtopic'].'.php')){
    require_once('pages/page_'.$_REQUEST['subtopic'].'.php');
}else{
    require_once('pages/page_latestnews.php');
}
switch($_REQUEST['subtopic']){

    // Account
    case "accountmanagement":
    case "logout":
    case "premiumtokens":
    case "createaccount":
    case "admin":
        $navcat = '1';
        break;

    // Community
    case "characters":
    case "whoisonline":
    case "guilds":
    case "houses":
        $navcat = '2';
        break;

    // Statistics
    case "highscores":
    case "killstatistics":
        $navcat = '3';
        break;

    // Library
    case "creatures":
    case "maps":
    case "spells":
        $navcat = '4';
        break;

    // Support
    case "rules":
    case "staff":
    case "faq":
    case "contact":
        $navcat = '5';
        break;
    // News
    default:
        $navcat = '0';
}
/*
switch($_REQUEST['subtopic']){
    // News
    case "archive":
        //News Archive
        $navcat = '0';
        break;
    case "changelog":
        //....changelog?
        $navcat = '0';
        require_once('pages/page_changelog.php');
        break;

    // Account
    case "accountmanagement":
        //Account Login and management (duh)
        $navcat = '1';
        require_once('pages/page_accountmanagement.php');
        break;
    case "logout":
        $navcat = '1';
        require_once('pages/page_logout.php');
        break;
    case "premiumtokens":
        //Token management + purchase tokens
        $navcat = '1';
        require_once('pages/page_premiumtokens.php');
        break;
    case "createaccount":
        //...
        $navcat = '1';
        require_once('pages/page_createaccount.php');
        break;
    case "admin":
        //...
        $navcat = '1';
        require_once('pages/page_admin.php');
        break;

    // Community
    case "characters":
        //Character Search
        $navcat = '2';
        require_once('pages/page_characters.php');
        break;
    case "whoisonline":
        //whos online?!?!?
        $navcat = '2';
        require_once('pages/page_whoisonline.php');
        break;
    case "guilds":
        //guild list (and management?)
        $navcat = '2';
        require_once('pages/page_guilds.php');
        break;
    case "houses":
        $navcat = '2';
        require_once('pages/page_houses.php');
        break;

    // Statistics
    case "highscores";
        $navcat = '3';
        require_once('pages/page_highscores.php');
        break;
    case "killstatistics":
        //Last Kills
        $navcat = '3';
        break;

    // Library
    case "creatures";
        $navcat = '4';
        require_once('pages/page_creatures.php');
        break;
    case "maps";
        $navcat = '4';
        require_once('pages/page_maps.php');
        break;
    case "spells";
        $navcat = '4';
        require_once('pages/page_spells.php');
        break;

    // Support
    case "rules";
        $navcat = '5';
        require_once('pages/page_rules.php');
        break;
    case "staff";
        $navcat = '5';
        require_once('pages/page_staff.php');
        break;
    case "faq";
        $navcat = '5';
        require_once('pages/page_faq.php');
        break;
    case "contact";
        $navcat = '5';
        require_once('pages/page_contact.php');
        break;

    default:
        //News, front page
        $navcat = '0';
        require_once('pages/page_latestnews.php');
}/**/
$page_js = urlencode($_REQUEST['subtopic']).'&navcat='.$navcat;
if (isset($_REQUEST["action"])) $page_js .= '&action='.urlencode($_REQUEST["action"]);

if (isset($_REQUEST['logindata']) && $logged_in === false){
        printLogin('Account name or password is not correct.');
}
require_once('template.php');

?>
