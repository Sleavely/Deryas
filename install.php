<style type="text/css">*{font-family: verdana; font-size: 12px;}</style>
<h1 style="font-size: 16px;">THS AAC Installer</h1>
<?php

if(isset($_REQUEST['do'])){
    require_once('config.php');
    require_once('pages/class_thscore.php');
    $customTables = array('3h_accounts',
                          '3h_activities',
                          '3h_activitylog',
                          '3h_deaths',
                          '3h_loot',
                          '3h_namelocks',
                          '3h_players',
                          'aac_cache',
                          'aac_creatures',
                          'aac_guilds',
                          'aac_inspiration',
                          'aac_items',
                          'aac_news',
                          'aac_sessions'
                         );
    //count existing tables
    $tables3h = db_query_num('SHOW TABLES IN '.$config->dbschema.' LIKE "3h_%"');
    $tablesaac = db_query_num('SHOW TABLES IN '.$config->dbschema.' LIKE "aac_%"');
    if(($tables3h + $tablesaac) >= count($customTables) && $_REQUEST['do'] == 'step10'){
        echo '<p>The website has been installed. All tables are present and we\'re assuming they\'ve got the correct data.</p>
              <p style="color: #cc0000;">DONT FORGET TO REMOVE INSTALL.PHP OR PEOPLE WILL HAX YOU BIGTIME</p>';
    }else{
        echo '<p>Seems like you haven\'t installed this before. Good for you!</p>';
    }
}else{
?>
<p>This installer is not pretty. Live with it.</p>

<p>We're going to go through a few steps, nothing complicated (not for you, anyway. lucky bastard):</p>
<ol>
    <li>Check for already existing installation</li>
    <li>
        Create tables:
        <ul>
            <li>3h_accounts</li>
            <li>3h_activities</li>
            <li>3h_activitylog</li>
            <li>3h_deaths</li>
            <li>3h_loot</li>
            <li>3h_namelocks</li>
            <li>3h_players</li>
            <li>aac_cache</li>
            <li>aac_creatures</li>
            <li>aac_guilds</li>
            <li>aac_inspiration</li>
            <li>aac_items</li>
            <li>aac_news</li>
            <li>aac_sessions</li>
            <li>aac_spells</li>
        </ul>
    </li>
    <li>Import accounts to 3h_accounts</li>
    <li>Import players to 3h_players</li>
    <li>Import guilds to aac_guilds</li>
    <li>Insert serverstatus to aac_cache</li>
    <li>Import creatures to aac_creatures</li>
    <li>Import items to aac_items</li>
    <li>Import spells to aac_spells</li>
</ol>
<p>Importing stuff can be a intensive process and may take a lot of time for each step. Be patient and <strong>don't close the page</strong> or you will fuck everything up. And I'm not helping you clean it up :]</p>
<p>Make sure you're satisfied with the config (most of it can be changed later but I figured I'd put a small disclaimer here in case I forgot something.)</p>
<?php } ?>