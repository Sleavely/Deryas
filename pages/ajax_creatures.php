<?php
if(!isset($loadedProperly)){
    echo json_encode(array('success' => 0, 'errormsg' => 'Page was not loaded properly.'));
    exit;
}

//check for subtopic querystring
if (!isset($_REQUEST['searchname'])){
    echo json_encode(array('success' => 0, 'errormsg' => 'No searchname'));
    exit;
}
echo json_encode(array('success' => 1));

?>
