<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

$didout = '<img src="images/icons/information.png" alt=""/><strong>Did you know..</strong><br />';

$didqueries = array(
                    //X% of all accounts use cip.nu emails
                    'SELECT ROUND(((SELECT count(id) AS amount FROM accounts WHERE email LIKE "%@hotmail.com")/(SELECT count(id) AS total FROM accounts))*100) AS cookie',
                    //there have been X news posts
                    'SELECT count(*) FROM aac_news',
                    //the staff on $config->servername are working hard to get the server working
                    null,
                    //players online this month
                    'SELECT count(*) FROM players WHERE lastlogin > (UNIX_TIMESTAMP()-(60*60*24*30))'
                );

//Dynamic querystuff goes here.
if (isset($_REQUEST['randnum'])){ $randnum = intval($_REQUEST['randnum'])-1; }else{ $randnum = rand(0,count($didqueries)-1); }
if ($didqueries[$randnum] != null){ $didresult = db_query_row($didqueries[$randnum]); }else{ $didresult = array('','','','','','','',''); }

//Strings goes here.
$didstrings = array(
                    $didresult[0].'% of all accounts use hotmail.com emails',
                    'there have been '.$didresult[0].' news posts',
                    'the staff on '.$config->servername.' are working hard to get the server working',
                    $didresult[0].' character'.($didresult[0] > 1 ? 's' : ($didresult[0] < 1 ? 's' : '')).' have logged on the past 30 days'
                   );

$didout .= $didstrings[$randnum].'?';

echo $didout;

?>
