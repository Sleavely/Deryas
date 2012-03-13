<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');
/*
 * The core contains functions that theoretically could
 * be used in any project, such as database-related functions.
 *
 * Copyright (c) 2008-2010 Triplehead Solutions
 *         All rights reserved.
 */
$MySQL = array();
$MySQL['Host'] = $config->dbhost;
$MySQL['Port'] = $config->dbport;

//What's our login?
$MySQL['User'] = $config->dbuser;
$MySQL['Password'] = $config->dbpass;

//What's the name of the database?
$MySQL['Database'] = $config->dbschema;
$config->MySQL = (object) $MySQL;

function db_connect (){
	global $config;
	if (!$link = mysql_connect($config->MySQL->Host, $config->MySQL->User, $config->MySQL->Password)) {
	    return false;
	    exit;
	}

	if (!mysql_select_db($config->MySQL->Database, $link)) {
	    return false;
	    exit;
	}
	mysql_set_charset('utf8');
	return $link;
}
$db_connection = db_connect();

function db_query ($querystring, $error_reporting = false){
	global $db_connection;
	global $db_queries;
	if (isset($querystring)){
		$result = mysql_query($querystring, $db_connection);
                $db_queries++;
		if (!$result) {
			if ($error_reporting == true){
    			echo 'MySQL Error: ' . mysql_error() ."
";
    			echo 'Query: ' . $querystring;
			}else{
				return false;
			}
    		exit;
		}
		return $result;
	}else{
		return false;
	}
}

function db_query_num($querystring){
	if (isset($querystring)){
		$query = db_query($querystring, true);
		if ($query != false){
			return mysql_num_rows($query);
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function db_query_result($query){
	if (isset($query)){
		return mysql_fetch_array($query);
	}else{
		return false;
	}
}

function db_query_row($querystring){
	if (isset($querystring)){
		$query = db_query($querystring);
		if ($query != false){
			if (mysql_num_rows($query) > 0){
				return mysql_fetch_array($query);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function db_escape($escapestring){
	global $db_connection;
	return mysql_real_escape_string($escapestring, $db_connection);
}

function dates_diff($d1, $d2){
/* compares two timestamps and returns array with differencies (year, month, day, hour, minute, second)
*/
  //check higher timestamp and switch if neccessary
  if ($d1 < $d2){
    $temp = $d2;
    $d2 = $d1;
    $d1 = $temp;
  }
  else {
    $temp = $d1; //temp can be used for day count if required
  }
  $d1 = date_parse(date("Y-m-d H:i:s",$d1));
  $d2 = date_parse(date("Y-m-d H:i:s",$d2));
  //seconds
  if ($d1['second'] >= $d2['second']){
    $diff['second'] = $d1['second'] - $d2['second'];
  }
  else {
    $d1['minute']--;
    $diff['second'] = 60-$d2['second']+$d1['second'];
  }
  //minutes
  if ($d1['minute'] >= $d2['minute']){
    $diff['minute'] = $d1['minute'] - $d2['minute'];
  }
  else {
    $d1['hour']--;
    $diff['minute'] = 60-$d2['minute']+$d1['minute'];
  }
  //hours
  if ($d1['hour'] >= $d2['hour']){
    $diff['hour'] = $d1['hour'] - $d2['hour'];
  }
  else {
    $d1['day']--;
    $diff['hour'] = 24-$d2['hour']+$d1['hour'];
  }
  //days
  if ($d1['day'] >= $d2['day']){
    $diff['day'] = $d1['day'] - $d2['day'];
  }
  else {
    $d1['month']--;
    $diff['day'] = date("t",$temp)-$d2['day']+$d1['day'];
  }
  //months
  if ($d1['month'] >= $d2['month']){
    $diff['month'] = $d1['month'] - $d2['month'];
  }
  else {
    $d1['year']--;
    $diff['month'] = 12-$d2['month']+$d1['month'];
  }
  //years
  $diff['year'] = $d1['year'] - $d2['year'];
  return $diff;
}

function convertToFBTimestamp( $d ) {
  $d = ( is_string( $d ) ? strtotime( $d ) : $d ); // Date in Unix Time

  if( ( time() - $d ) < 10 )
    return 'a few seconds ago';
  if( ( time() - $d ) < 60 )
    return ( time() - $d ).' seconds ago';
  if( ( time() - $d ) < 120 )
    return 'about a minute ago';
  if( ( time() - $d ) < 3600 )
    return (int) ( ( time() - $d ) / 60 ).' minutes ago';
  if( ( time() - $d ) == 3600 )
    return '1 hour ago';
  if( date( 'Ymd' ) == date( 'Ymd' , $d ) )
    return (int) (( time() - $d ) / 3600).' hour'.((int)(( time() - $d ) / 3600) === 1 ? '' : 's').' ago';
  if( ( time() - $d ) < 86400 )
    return 'Yesterday at '.date( 'g:ia' , $d );
  if( ( time() - $d ) < 259200 )
    return date( 'l \a\t g:ia' , $d );
  if( date( 'Y' ) == date( 'Y' , $d ) )
    return date( 'F, jS \a\t g:ia' , $d );
  return date( 'j F Y \a\t g:ia' , $d );
}

?>
