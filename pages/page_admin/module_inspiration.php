<?php

function adminmod_inspiration($user){
    $out = '<h2>Mapping Inspiration</h2>';

    //process: add image
    if (isset($_REQUEST['inspirurl'])){
        $urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
        if (eregi($urlregex, $_REQUEST['inspirurl'])){
            //ok its a valid url.
            //do we already have it?
            if (db_query_num('SELECT id FROM aac_inspiration WHERE url = "'.db_escape($_REQUEST['inspirurl']).'"') == 0){
                db_query('INSERT INTO aac_inspiration (url, tags, uploader, timestamp) VALUES ("'.db_escape($_REQUEST['inspirurl']).'", "", '.$user->id.', UNIX_TIMESTAMP())');
            }else{
                $out .= '<div class="charbox">
                            <div class="charname" style="color: #cc0000;">
                                <img src="images/icons/exclamation.png" alt=""/> Already added!
                            </div>
                         </div>';
            }
        }else{
            $out .= '<div class="charbox">
                        <div class="charname" style="color: #cc0000;">
                            <img src="images/icons/exclamation.png" alt=""/> Invalid URL!
                        </div>
                     </div>';
        }
    }

    //search tags
    $out .= '<div class="charbox">
                <div class="charname">
                    <img src="images/icons/magnifier.png" alt=""/> Search Tag
                </div>
                <div class="charbuttons">
                    <form method="post" action="?subtopic=admin&action=inspiration">
                        <label for="inspirsearch">Tag: </label><input type="text" maxlength="45" size="30" name="inspirsearch" id="inspirsearch"/>
                        <a class="buttonsubmit abutton" href="#">
                            <img src="images/icons/rainbow.png" alt=""/>
                            Find!
                        </a>
                    </form>
                </div>
            </div>';
    
    //pagination
    // How many adjacent pages should be shown on each side?
        $adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM aac_inspiration";
	$total_pages = mysql_fetch_array(db_query($query));
	$total_pages = $total_pages['num'];

	/* Setup vars for query. */
	$limit = 5; 			//how many items to show per page
        if (isset($_REQUEST['page'])){
            $page = intval($_REQUEST['page']);
        }else{
            $page = 1;
        }
	if($page)
		$start = ($page - 1) * $limit; //first item to display on this page
	else
		$start = 0; //if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT url, tags, uploader, timestamp FROM aac_inspiration LIMIT $start, $limit";
	$result = db_query($sql);

	/* Setup page vars for display. */
	if ($page == 0) $page = 1;	//if no page var is given, default to 1.
	$prev = $page - 1;		//previous page is page - 1
	$next = $page + 1;		//next page is page + 1
	$lastpage = ceil($total_pages/$limit);	//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;                  //last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($page > 1)
			$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=$prev\">« previous</a>";
		else
			$pagination.= "<span class=\"disabled\">« previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=$counter\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=$counter\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=$lpm1\">$lpm1</a>";
				$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=$lastpage\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=1\">1</a>";
				$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=2\">2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=$counter\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=$lpm1\">$lpm1</a>";
				$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=$lastpage\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=1\">1</a>";
				$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=2\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=$counter\">$counter</a>";
				}
			}
		}

		//next button
		if ($page < $counter - 1)
			$pagination.= "<a href=\"?subtopic=admin&action=inspiration&page=$next\">next »</a>";
		else
			$pagination.= "<span class=\"disabled\">next »</span>";
		$pagination.= "</div>\n";
	}
        $out .= '<div class="charbox">
                    <div class="charname">
                        <img src="images/icons/book_open.png" alt=""/> Page '.$page.'
                    </div>
                    <div class="charbuttons">'.$pagination.'</div>
                </div>';

    //results
    while($row = mysql_fetch_array($result)){
        $out .= '<div class="charbox">
                    <div class="charbuttons">
                        <a href="'.$row['url'].'" target="_blank">
                            <img src="'.$row['url'].'" class="inspiration"/>
                        </a>
                    </div>
                    <div class="charname">
                        <img src="images/icons/tag_blue.png" alt=""/> Tags
                    </div>
                    <div class="chardesc">
                        '.str_replace(',',', ',$row['tags']).'
                    </div>
                </div>';
    }
    
    //pagination, again
    $out .= '<div class="charbox">
                    <div class="charname">
                        <img src="images/icons/book_open.png" alt=""/> Page '.$page.'
                    </div>
                    <div class="charbuttons">'.$pagination.'</div>
                </div>';

    //add image
    $out .= '<div class="charbox">
                <div class="charname">
                    <img src="images/icons/picture_add.png" alt=""/> Add Image
                </div>
                <div class="charbuttons">
                    <form method="post" action="?subtopic=admin&action=inspiration">
                        <input type="hidden" name="page" value="'.$page.'" />
                        <label for="inspirurl">Image URL: </label><input type="text" maxlength="100" size="30" name="inspirurl" id="inspirurl"/>
                        <a class="buttonsubmit abutton positive" href="#">
                            <img src="images/icons/add.png" alt=""/>
                            Submit
                        </a>
                    </form>
                </div>
            </div>';

    return $out;
}

?>
