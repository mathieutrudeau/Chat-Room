<?php
// Include Smarty
require_once '../smarty/libs/Smarty.class.php';

// Create a Smarty object
$smarty = new Smarty();
$smarty->setTemplateDir("../smarty/templates");
$smarty->setCompileDir("../smarty/templates_c");

$smarty->assign("errorMsg","");
$smarty->display("Signup.html");
?>