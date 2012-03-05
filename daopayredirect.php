<?php

// ?appcode=56280&prodcode=101&pin=ohrieshaid&orderno=320210

if (isset($_REQUEST['appcode']) && isset($_REQUEST['prodcode']) && isset($_REQUEST['pin']) && isset($_REQUEST['orderno'])){
    if ($_REQUEST['appcode'] == 56280 && $_REQUEST['prodcode'] == 101){
        require_once('init.php');
        header('location: http://'.$config->webhost.'/?subtopic=premiumtokens&vendor=daopay&daopaypin='.$_REQUEST['pin']);
    }
}

?>
