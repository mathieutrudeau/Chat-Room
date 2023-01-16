<?php
/* ===========================================================================
*   Displays the Login Page using Smarty
* ============================================================================
*/

// Include Smarty
require_once '../smarty/libs/Smarty.class.php';

// Create a Smarty object
$smarty = new Smarty();
$smarty->setTemplateDir("../smarty/templates");
$smarty->setCompileDir("../smarty/templates_c");

// Display the Login Page
$smarty->assign("errorMsg","");
$smarty->display("Login.html");
?>