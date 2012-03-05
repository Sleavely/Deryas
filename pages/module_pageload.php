<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

$stopload = microtime();
$loadtime = $stopload-$startload;
if ($db_queries === 1){
    $queries_grammar = 'query';
}else{
    $queries_grammar = 'queries';
}
echo 'Page loaded with '.$db_queries.' '.$queries_grammar.' in '.$loadtime.' seconds.';

?>
