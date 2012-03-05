<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

$page_title = 'News';
$page_content = '';

$news_query = db_query('SELECT id, title, content, author, timestamp FROM aac_news WHERE timestamp < UNIX_TIMESTAMP() ORDER BY timestamp DESC LIMIT 5');
while ($a = mysql_fetch_array($news_query)){
    $page_content .= '<h2>'.$a["title"].'</h2>
<p>'.nl2br($a["content"]).'</p>
<div class="author">
    '.$a["author"].',<br />
    <span style="font-weight: normal;">'.date("F j",$a["timestamp"]).'</span>
</div>
';
}

?>
