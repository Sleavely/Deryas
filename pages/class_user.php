<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

class user{
    public $id = 0;
    public $name = 'Guest';
    public $realname = 'Guest';
    public $location = null;
    public $email = null;
    public $group_id = 1;
    public $created = 0;
    public $premdays = 0;
    public $premtokens = 0;
    public $warnings = 0;
    public $passwordage = 0;
	public $secretquestion = '';
	public $secretanswer = '';
    public $characterAmount = 0;
    public $characters = array();
    public $permissions;
    public $timezone = 'CET';

    function __construct($user_id = 0) {
        if ($user_id > 0){
            /* Account */
            $accinfo = db_query_row('SELECT id, name, email, group_id, premdays, warnings FROM accounts WHERE id = '.intval($user_id));
            $this->id = $accinfo[0];
            $this->name = $accinfo[1];
            $this->email = $accinfo[2];
            $this->group_id = $accinfo[3];
            $this->premdays = $accinfo[4];
            $this->warnings = $accinfo[5];

            $extra_accinfo_query = db_query('SELECT type, value FROM 3h_accounts WHERE account_id = '.$this->id);
            while($e = mysql_fetch_array($extra_accinfo_query)){
                if ($e["type"] == 'created') $this->created = $e["value"];
                if ($e["type"] == 'realname') $this->realname = $e["value"];
                if ($e["type"] == 'location') $this->location = $e["value"];
                if ($e["type"] == 'passwordage') $this->passwordage = $e["value"];
				if ($e["type"] == 'secretquestion') $this->secretquestion = $e["value"];
				if ($e["type"] == 'secretanswer') $this->secretanswer = $e["value"];
            }
            if ($this->realname == 'Guest') $this->realname = $this->name;

            /* Characters */
            $this->characters = array();
            $chars_query = db_query('SELECT id, name, level, vocation, group_id, lookbody, lookfeet, lookhead, looklegs, looktype, maglevel, town_id, sex, lastlogin, balance, online, rank_id FROM players WHERE account_id =  '.intval($user_id));
            $this->characterAmount = mysql_num_rows($chars_query);
            if ($this->characterAmount > 0){
                $c_offset = 0;
                while ($row = mysql_fetch_array($chars_query)) {
                    $this->characters[$c_offset] = array('id' => $row["id"], 'name' => $row["name"], 'level' => $row["level"], 'vocation' => $row["vocation"], 'group_id' => $row["group_id"],
                                                                  'lookbody' => $row["lookbody"], 'lookfeet' => $row["lookfeet"], 'lookhead' => $row["lookhead"], 'looklegs' => $row["looklegs"], 'looktype' => $row["looktype"],
                                                                  'maglevel' => $row["maglevel"], 'town_id' => $row["town_id"], 'sex' => $row["sex"], 'lastlogin' => $row["lastlogin"], 'balance' => $row["balance"], 'online' => $row["online"], 'rank_id' => $row["rank_id"]);
                    $c_offset++;
                }
            }
        }
        /* Permissions */
		//TODO: move this to admin backend
            $p = array();

            //Post News
            $users = array('konto', 'yuvbeen', 'biggles');
            $p['post_news'] = false;
            if (in_array($this->name, $users)) $p['post_news'] = true;

            //Sessions
            $users = array('konto', 'yuvbeen', '555555', 'biggles');
            $p['sessions'] = false;
            if (in_array($this->name, $users)) $p['sessions'] = true;

            //Transactions
            $users = array('konto', 'yuvbeen', 'biggles');
            $p['transactions'] = false;
            if (in_array($this->name, $users)) $p['transactions'] = true;

            //Reports
            $users = array('konto', 'yuvbeen', 'biggles', '555555');
            $p['reports'] = false;
            if (in_array($this->name, $users)) $p['reports'] = true;

            //SVN Log
            $users = array('konto', 'yuvbeen', 'biggles', '555555');
            $p['svnlog'] = false;
            if (in_array($this->name, $users)) $p['svnlog'] = true;

            //Inspiration
            $users = array('konto', 'yuvbeen', 'biggles');
            $p['inspiration'] = false;
            if (in_array($this->name, $users)) $p['inspiration'] = true;

            //Import Data
            $users = array('konto', 'yuvbeen');
            $p['import'] = false;
            if (in_array($this->name, $users)) $p['import'] = true;
			
			//phpinfo()
            $users = array('konto', 'yuvbeen');
            $p['phpinfo'] = false;
            if (in_array($this->name, $users)) $p['phpinfo'] = true;

            //Is Admin
            $isAdmin = false;
            if (in_array(true, $p, true)) $isAdmin = true;

            //Set 'em
            $this->permissions = (object) array('isAdmin' => $isAdmin,
                                                'post_news' => $p['post_news'],
                                                'sessions' => $p['sessions'],
                                                'transactions' => $p['transactions'],
                                                'reports' => $p['reports'],
                                                'svnlog' => $p['svnlog'],
                                                'inspiration' => $p['inspiration'],
                                                'import' => $p['import'],
												'phpinfo' => $p['phpinfo']
                                            );
    }
	
	/**
	 * Saves password and updates password age for loaded user
	 * @param string $string Password to save
	 */
	public function setPassword($string){
		//update user db
		db_query('UPDATE accounts SET password = "'.db_escape($string).'" WHERE id = '.$this->id);
		
		//update password age in db & object
		$now = time();
		db_query('UPDATE 3h_accounts AS 3ha SET 3ha.value = '.$now.' WHERE 3ha.account_id = '.$this->id.' AND 3ha.type = "passwordage"');
		$this->passwordage = $now;
	}
	
	/**
	 * Generates a random password
	 * @param int $length Output string length
	 * @param string $chars Random [0-9a-zA-Z_-]{8}
	 * @return string The generated password
	 */
	public function makePassword($length = 8, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_'){
		$count = mb_strlen($chars);

		for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = mt_rand(0, $count - 1);
			$result .= mb_substr($chars, $index, 1);
		}
		
		return $result;
	}
}
$user = new user($user_id);
?>
