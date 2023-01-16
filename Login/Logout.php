<?php
/* ===========================================================================
*   Logout the user by deleting his session key
*   Allows the user to Login again
*   Displays the Login Page using Smarty
* ============================================================================
*/

// Get the functions for the models
$this_dir = __DIR__;
require_once("$this_dir/../Model/DB_connect.php");
require_once("$this_dir/../Model/DB_messages.php");
require_once("$this_dir/../Model/DB_user.php");
require_once("$this_dir/../Control/useful_functions.php");

// Include Smarty
require_once '../smarty/libs/Smarty.class.php';

// Create a Smarty object
$smarty = new Smarty();
$smarty->setTemplateDir("../smarty/templates");
$smarty->setCompileDir("../smarty/templates_c");

// Reset the Login Info in the session so that the user is forced to Login again
reset_login_info();

// Display the Login Page so the User can Login again
$smarty->assign("errorMsg","");
$smarty->display("Login.html");
?>