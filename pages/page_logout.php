<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

if ($logged_in === true){
    db_query('DELETE FROM aac_sessions WHERE userid = '.db_escape($_COOKIE['usr']).' AND hash = "'.db_escape($_COOKIE['hash']).'"');
    setcookie('usr', "", time()-31536001);
    setcookie('hash', "", time()-31536001);
}
$logged_in = false;
$page_title = 'Logged Out';
printLogin();

?>
