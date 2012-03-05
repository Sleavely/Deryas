<?php

/**
 * This class is used to build lists of timezones by filtering country
 * and in some cases cities/areas (such as states in USA).
 * It might appear that this class would fit in well with the user class,
 * but for scalability and readability everything related to timezones is done here.
 *
 * @author Cip
 */
class timezone {
    
    function __construct($identifier = 'CET'){
        //print_r(timezone_identifiers_list());
        //print_r(timezone_abbreviations_list());
        date_default_timezone_set($identifier);
    }
}
$timezone = new timezone($user->timezone);
?>
