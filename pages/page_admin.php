<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'nothing';
$page_title = 'Admin Panel';
if ($logged_in === true && $user->permissions->isAdmin === true){
    
    if ($_REQUEST['action'] == 'news'){
        require_once('page_admin/module_news.php');
        $page_title = 'News Editor';
        if ($user->permissions->post_news === true){
            $page_content = adminmod_news($config, $user);
        }else{
            $page_content = '<h2>Access Denied</h2>
                            <p>You don\'t have permission to use this feature.</p>
                            <p><a href="javascript:history.go(-1)">Go back</a> the way you came.</p>';
        }

    }elseif($_REQUEST['action'] == 'sessions'){
        $page_title = 'Sessions';
        if ($user->permissions->sessions === true){
            $page_content = '<h2>Sessions</h2>
                            <p>itams</p>';
        }else{
            $page_content = '<h2>Access Denied</h2>
                            <p>You don\'t have permission to use this feature.</p>
                            <p><a href="javascript:history.go(-1)">Go back</a> the way you came.</p>';
        }

    }elseif($_REQUEST['action'] == 'svnlog'){
        require_once('page_admin/module_svnlog.php');
        $page_title = 'SVN Log';
        if ($user->permissions->svnlog === true){
            $page_content = adminmod_svnlog($config);
        }else{
            $page_content = '<h2>Access Denied</h2>
                            <p>You don\'t have permission to use this feature.</p>
                            <p><a href="javascript:history.go(-1)">Go back</a> the way you came.</p>';
        }

    }elseif($_REQUEST['action'] == 'inspiration'){
        require_once('page_admin/module_inspiration.php');
        $page_title = 'Inspiration';
        if ($user->permissions->inspiration === true){
            $page_content = adminmod_inspiration($user);
        }else{
            $page_content = '<h2>Access Denied</h2>
                            <p>You don\'t have permission to use this feature.</p>
                            <p><a href="javascript:history.go(-1)">Go back</a> the way you came.</p>';
        }

    }elseif($_REQUEST['action'] == 'import'){
        require_once('page_admin/module_import.php');
        $page_title = 'Import Data';
        if ($user->permissions->import === true){
            $page_content = adminmod_import($user);
        }else{
            $page_content = '<h2>Access Denied</h2>
                            <p>You don\'t have permission to use this feature.</p>
                            <p><a href="javascript:history.go(-1)">Go back</a> the way you came.</p>';
        }
	
	}elseif($_REQUEST['action'] == 'phpinfo'){
        if ($user->permissions->phpinfo === true){
            phpinfo();
			exit;
        }else{
            $page_content = '<h2>Access Denied</h2>
                            <p>You don\'t have permission to use this feature.</p>
                            <p><a href="javascript:history.go(-1)">Go back</a> the way you came.</p>';
        }

    }else{
        $page_content = '<h2>Admin Panel</h2>
                            <p>
                                Welcome to the administration area, '.$user->realname.'!
                                <div style="margin: 20px;">
                                    <span style="font-style: italic;">Here\'s a list of all the pages available:</span>
                                    <ul style="list-style-type: none;">
                                        <li><a href="?subtopic=admin&action=news">news</a></li>
                                        <li><a href="?subtopic=admin&action=sessions">sessions</a></li>
                                        <li><a href="?subtopic=admin&action=svnlog">svnlog</a></li>
                                        <li><a href="?subtopic=admin&action=inspiration">inspiration</a></li>
                                        <li><a href="?subtopic=admin&action=import">import data</a></li>
										<li><a href="?subtopic=admin&action=phpinfo">phpinfo()</a></li>
                                    </ul>
                                </div>
                            </p>
                            <pre>'.print_r($user,true).'</pre>';
    }
}else{
    $page_title = 'Staff Section';
    printLogin();
}

?>
