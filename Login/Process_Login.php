<?php
/* ===========================================================================
*   Made By Mathieu Trudeau (2019)
*   Name: Process_Login.php
*   Description: Validates that the user that is attempting to Login
*               has entered a valid username & password.
*               If he did, redirect him to the chatroom html page.
*               Otherwise, give him an error message.
* ============================================================================
*/

// Include Smarty
require_once '../smarty/libs/Smarty.class.php';

// Create a Smarty object
$smarty = new Smarty();
$smarty->setTemplateDir("../smarty/templates");
$smarty->setCompileDir("../smarty/templates_c");

// Get the functions for the models
$this_dir = __DIR__;
require_once("$this_dir/../Model/DB_connect.php");
require_once("$this_dir/../Model/DB_messages.php");
require_once("$this_dir/../Model/DB_user.php");
require_once("$this_dir/../Control/useful_functions.php");

// False if no errors
// True if an error occurs (Abort Login)
$hasError=false;
// Error message that is shown to the user
$errorMsg="";

// =======================================================================================
// Make sure that the user entered both a USERNAME & PASSWORD
// =======================================================================================
// Make sure that all fields have been filled
if(isset($_POST['username']) && trim($_POST['username'])!="" && isset($_POST['password']) && trim($_POST['password'])!=""){
    $username=$_POST['username'];
    $password=$_POST['password'];
}
else{
    // If its not the case, let the user know
    $hasError=true;
    $errorMsg.="Please provide a username AND password.";
}
// ---------------------------------------------------------------------------------------


// =======================================================================================
// TRY TO LOGIN
// =======================================================================================
// If both fields where filled, validate if the user exists in the database
if(!$hasError){
    // Connect to the database
    $conn=connect();

    // Did we successfully connect to the database?
    // If yes, validate user,
    // If not, show an error
    if($conn){
        // Check if the user does not exist or does not have an active account
        if(!validate_user($conn,$username,$password)){
            // In case the account is deactivated, try making it active
            if(!reactivate_user($conn,$username)){
                // The account simple does not exist, so the username or password must be incorrect
                $hasError=true;
                $errorMsg="Invalid account. Make sure that the username and password are correct.";
            }
        }
    }
    else{
        // Connection to the database failed
        $hasError=true;
        $errorMsg.="Error while connecting to the database.";
    }
}

// =======================================================================================
// LOGIN OR GIVE ERROR
// =======================================================================================
// Either go to the chatroom if the login was successful,
// Or show an error if it was not
if($hasError){
    // Show the login error
    $smarty->assign("errorMsg",$errorMsg);
    $smarty->display("Login.html");
}
else{
    // Login to chat room
    save_login_info($username);
    $smarty->assign("username",$username);
    $smarty->display("ChatRoom.html");
}
?>