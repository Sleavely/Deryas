<?php
$config = array(
                    // WEBSITE
                    //hostname
                    'webhost' => 'deryas.otworlds.com',
                    //used in title and where name is mentioned
                    'servername' => 'Deryas',
                    //the header thingie
                    'serverlogo' => '<a href="index.php"><img src="images/logo.png" alt="Deryas"/></a>',
                    'serverslogan' => 'Testing site for Ramurika',
					
					// WEBSITE FEATURES
					//premium tokens
					'premiumtokens' => false,
					//features under development (set to false unless you want to show your users incomplete stuff)
					'debug' => true,

                    // GAME
                    //status server (used for checking players online, etc.)
                    'statushost' => 'localhost',
                    'statusport' => '7171',
                    //login server hostname (not used atm)
                    'loginhost' => 'localhost',
                    'loginport' => '7171',
                    //experience rate, used in the library to calculate creatures exp drop
                    'exprate' => 4,
                    //world ID for the gameserver
                    'worldid' => 0,
                    //group_id (and above) that will be handled as Staff (this shouldn't include normal players such as tutors)
                    'staffgroup' => 3,
                    //the account ID (not name) where "deleted" characters will be placed
                    'deletedaccount' => 6,
                    //minimum level to create a guild
                    'guildminimumlevel' => 8,

                    // MYSQL
                    //server
                    'dbhost' => 'localhost',
                    'dbport' => '3306',
                    //credentials
                    'dbuser' => 'root',
                    'dbpass' => '',
                    //schema (database that contains tables)
                    'dbschema' => 'otserv',

                    // SVN
                    //set to true if admins should be able to track
                    //important files through via a SVN server
                    'svnenabled' => true,
                    //repository server
                    'svnhost' => 'http://pundit:7170/svn/deryas/',
                    //credentials
                    //if anonymous then set to blank ''
                    'svnuser' => '',
                    'svnpass' => '',
                    //*files* to check, each must have a unique identifier
                    //example:
                    //        'worldmap' => '/trunk/data/world/map.otbm',
                    //          'config' => '/trunk/config.lua'
                    'svnfiles' => array(
                                       'city' => '/branches/Area_City/city.otbm',
                                        'cip' => '/branches/User_Cip/dusty hills.otbm',
                                 'corruption' => '/branches/User_Corruption/parched plains.otbm',
                                     'dunder' => '/branches/User_Dunder/yeti peaks.otbm',
                                     'verold' => '/branches/Area_Verold/verold.otbm',
                                 'chestquest' => '/branches/Mod_Chestquest/actions/scripts/quests/chestquest.lua'
                                       ),

                    //these are filled in automatically with the information that you edit a few lines down
                    'MySQL' => array(),
                    'vocations' => array(),
                    'groups' => array(),
                    'towns' => array(),
                    'quests' => array(), //<- this one is actually a separate config
                    'newcharacters' => array()
               );
$newcharacters = array(
                        'group_id' => 1,
                        //Levels to start at. Experience is calculated automatically by the website
                        'level' => 8,
                        'maglevel' => 1,
                        //health and mana is always full for new characters
                        'health' => 185,
                        'mana' => 35,
                        //outfit
                        'look' => array(
                                        'head' => 117,
                                        'body' => 41,
                                        'legs' => 50,
                                        'feet' => 115,
                                        'type' => array(
                                                        'male' => 128,
                                                        'female' => 136
                                                        )
                                        ),
                        //this one's pretty obvious
                        'cap' => 100,
                        //Vocations that you're allowed to choose from,
                        //if only one is selected users wont see an option to choose (since it's not necessary).
                        //Separate with a comma (no space)
                        //AT LEAST ONE MUST BE ENTERED
                        'vocations' => '1,2,3,4'
                      );
$vocations = array(
                    //Each vocation name, stored by vocationID
                    '0' => 'Rookie',
                    '1' => 'Sorcerer',
                    '2' => 'Druid',
                    '3' => 'Paladin',
                    '4' => 'Knight',
                    '5' => 'Master Sorcerer',
                    '6' => 'Elder Druid',
                    '7' => 'Royal Paladin',
                    '8' => 'Elite Knight'
               );
$towns = array(
               //Each town name, stored by townID
               '1' => array(
                            //name that will appear on search results and (if selectable) char creation page
                            'name' => 'Gravik',
                            //is it possible to start in this town?
                            //true = yes, false = no
                            //at least one of your towns must be selectable.
                            'selectable' => true,
                            'x' => 1200,
                            'y' => 1200,
                            'z' => 7
                           ),
               '2' => array(
                            'name' => 'Sueka',
                            'selectable' => false,
                            'x' => 1200,
                            'y' => 1201,
                            'z' => 7
                           ),
               '3' => array(
                            'name' => 'Tower of the Gods',
                            'selectable' => false,
                            'x' => 1200,
                            'y' => 1202,
                            'z' => 7
                           )
               );
// FORBIDDEN CHARACTER NAMES
// * Supports wildcards
// * Case-insensitive
$forbiddennames = array(
                    '*admin*',
                    '*god *',
                    '?m *',
                    'cip*',
                    'deryas*',
                    'staff*',
                    'game*master*'
               );




        ///////////////////////
////////// DON'T EDIT BELOW! //////////
        ///////////////////////

class ConfigManager {

    private $_data;

    public function __construct(Array $properties=array()){
      $this->_data = $properties;
    }

    // magic methods!
    public function __set($property, $value){
      return $this->_data[$property] = $value;
    }

    public function __get($property){
      return array_key_exists($property, $this->_data)
        ? $this->_data[$property]
        : null
      ;
    }
}
$config = new ConfigManager($config);
$config->vocations = $vocations;
$config->towns = $towns;
$config->newcharacters = $newcharacters;
$config->forbiddennames = $forbiddennames;
require_once('config.quests.php');
$config->quests = $quests;

?>
