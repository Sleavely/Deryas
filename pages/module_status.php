<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

class Otstatus
{
        //* Made by Kofel (kofels@gmail.com)
        //* Under GPL
        //* Writed in PHP5 and used SimpleXML
	private $OTS=array();
	private $info=array();
	public function __construct($ip,$port=7171)
	{
		$this->OTS['IP']=$ip;
		$this->OTS['PORT']=$port;

	}
	public function update()
	{
		$socketHandler=@fsockopen($this->OTS['IP'], $this->OTS['PORT'], $errno, $errstr, 1);
		if(!$socketHandler)
		{
			return 0; //offline
		}
		else
		{
			$tmp = '';
			$info = chr(6).chr(0).chr(255).chr(255).'info';
                        stream_set_timeout($socketHandler, 2);
			fwrite($socketHandler, $info);
                        $loopcount = 0;
			while (!feof($socketHandler))
			{
                                stream_set_timeout($socketHandler, 2);
				$tmp .= fgets($socketHandler, 1024);
                                $loopcount++;
                                if($loopcount === 10 && $tmp == ''){
                                    return 0;
                                }
			}
			fclose($socketHandler);
			$this->info = $tmp;
                        if (strlen($tmp) === 0){
                            return 1; //read from cache instead
                        }else{
                            return 2; //proceed to get data
                        }
		}
	}
	public function parse()
	{
			$xml=new SimpleXMLElement($this->info);
			$tmp=array();
			$tmp['serverinfo']['uptime']=(int)$xml->serverinfo->attributes()->uptime;
			$tmp['serverinfo']['ip']=(string)$xml->serverinfo->attributes()->ip;
			$tmp['serverinfo']['name']=(string)$xml->serverinfo->attributes()->servername;
			$tmp['serverinfo']['port']=(int)$xml->serverinfo->attributes()->port;
			$tmp['serverinfo']['location']=(string)$xml->serverinfo->attributes()->location;
			$tmp['serverinfo']['site']=(string)$xml->serverinfo->attributes()->url;
			$tmp['serverinfo']['server']=(string)$xml->serverinfo->attributes()->server;
			$tmp['serverinfo']['version']=(int)$xml->serverinfo->attributes()->version;
			$tmp['serverinfo']['client']=(int)$xml->serverinfo->attributes()->client;
			$tmp['owner']['name']=(string)$xml->owner->attributes()->name;
			$tmp['owner']['email']=(string)$xml->owner->attributes()->email;
			$tmp['players']['online']=(int)$xml->players->attributes()->online;
			$tmp['players']['max']=(int)$xml->players->attributes()->max;
			$tmp['players']['peak']=(int)$xml->players->attributes()->peak;
			$tmp['monsters']['total']=(int)$xml->monsters->attributes()->total;
			$tmp['map']['name']=(string)$xml->map->attributes()->name;
			$tmp['map']['author']=(string)$xml->map->attributes()->author;
			$tmp['map']['width']=(int)$xml->map->attributes()->width;
			$tmp['map']['height']=(int)$xml->map->attributes()->height;
			$tmp['motd']=(string)$xml->motd;
			return $tmp;
	}
}

function statusmodule(){
    global $config;
    $online = 0;
    $cache_query = db_query('SELECT dateline, value1, value2 FROM aac_cache WHERE name = "serverstatus"');
    $cache_result = db_query_result($cache_query);

    if ($cache_result[0] <= time()-120){
        $a=new Otstatus($config->statushost, $config->statusport);
        $online = $a->update();
    }else{
        if ($cache_result[1] == 'online') $online = 1;
    }
    if ($online > 0){
        if ($online === 2){
            $response = $a->parse();
            $players = $response['players']['online'];
        }else{
            $players = $cache_result[2];
        }
        $status = '<span style="color: #00bb00;">Online</span>';
    }else{
        $status = '<span style="color: #cc0000;">Offline</span>';
        $players = 0;
    }
    if (isset($a)) db_query('UPDATE aac_cache SET dateline = UNIX_TIMESTAMP(), value1 = "'.($online > 0 ? 'online' : 'offline').'", value2 = '.$players.' WHERE name = "serverstatus"');
    echo '<img src="images/icons/server_connect.png" alt=""/><strong>Server Status: '.$status.'</strong><br />
            '.($online > 0 ? 'Players Online: '.$players : '');
}
statusmodule();


?>
