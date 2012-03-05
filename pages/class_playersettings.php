<?php

/**
 * Description of playerSettings
 *
 * @author Cip
 */
class playerSettings {

    public $player_id = 0;
    /**
     * stores flags locally between database saves
     * @var int
     */
    public $flags = 0;
    public $availableFlags = array(
                                    1 => 'profilefield_magiclevel',
                                    2 => 'profilefield_assets',
                                    4 => 'profilefield_quests',
                                    8 => 'profilefield_statistics',
                                   16 => 'profilefield_account',
                                   32 => 'profilefield_otherchars',
                                   64 => 'newsfeed_showme',
                                  128 => 'newsfeed_deaths',
                                  256 => 'newsfeed_advances',
                                  512 => 'profilefield_realname',
                                 1024 => 'profilefield_location'
                                  );

    function __construct($player_id = 0){
        // no need to check if the player exists, it is done outside of the class
        $this->player_id = $player_id;
        //Fetch stored flags for account
        $flags_query = db_query('SELECT value FROM 3h_players WHERE type = "settings" AND player_id = '.$player_id);
        if (mysql_num_rows($flags_query) > 0){
            $flags_result = mysql_fetch_row($flags_query);
            $this->flags = intval($flags_result[0]);
        }else{
            //Create flags entry for this account
            foreach ($this->availableFlags as $i => $v) {
                $this->flags = $this->flags + $i;
            }
            db_query('INSERT INTO 3h_players (player_id, type, value) VALUES ('.$player_id.',"settings",'.$this->flags.')');
        }
    }

    /**
	 *  Flag operators
	 *
	 * 	if ((siffra AND flag) == siffra) //if hasFlag
	 * 	siffra = (siffra OR flag) //Add Flag
	 * 	siffra = (siffra XOR flag) //Toggle Flag (Add&Remove)
	 * 	siffra = (siffra AND NOT (flag)) //Remove Flag
	 *
	 */

	/**
	 * int getFlag(string $FlagName)
	 */
	public function getFlag($name = 'canSuckCockForMoney'){
		if (in_array($name,$this->availableFlags)){
			$key = array_search($name, $this->availableFlags);
			return $key;
		}else{
			return 0;
		}
	}

	/**
	 * bool hasFlag(string $FlagName)
	 */
	public function hasFlag($flagname){
		$flag = $this->getFlag($flagname);
		if (($this->flags & $flag) == $flag){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * bool setFlag(string $FlagName)
	 */
	public function setFlag($FlagName){
		$FlagValue = $this->getFlag($FlagName);
		if (!$this->hasFlag($FlagName)){
			$this->flags = ($this->flags | $FlagValue);
			db_query('UPDATE 3h_players SET value = '.$this->flags.' WHERE type = "settings" AND player_id = '.$this->player_id);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * bool removeFlag(string $FlagName)
	 */
	public function removeFlag($FlagName){
		$FlagValue = $this->getFlag($FlagName);
		if ($this->hasFlag($FlagName)){
			$this->flags = ($this->flags & ~$FlagValue);
			db_query('UPDATE 3h_players SET value = '.intval($this->flags).' WHERE type = "settings" AND player_id = '.$this->player_id);
			return true;
		}else{
			return false;
		}
	}


}
?>
