<?php
/* ===========================================================================
*   Made By Mathieu Trudeau (2019)
*   Name: Process_Signup.php
*   Description: Validates that the user entered all the required fields 
*               correctly and logs him in if he does. Otherwise gives an error.
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

// If false, we can create the user
// If true, we will show an error
$hasError=false;
// Error messages to show the user if an error occurs
$errorMsg="";

// =======================================================================================
//  Retrieve all the Non-required fields
// =======================================================================================
// Get the signup information for the user
// The firstname, lastname and telephone # are not required, so set them to null if not left blank
if(isset($_POST['firstname']) && trim($_POST['firstname'])!=""){
    $firstname=$_POST['firstname'];
}
else{
    $firstname=NULL;
}
if(isset($_POST['lastname'])&&trim($_POST['lastname'])!=""){
    $lastname=$_POST['lastname'];
}
else{
    $lastname=NULL;
}
if(isset($_POST['telephone'])&&trim($_POST['lastname'])!=""){
    $telephone=$_POST['telephone'];
}
else{
    $telephone=NULL;
}

// =======================================================================================
// Retrieve all the REQUIRED fields and validate them
// =======================================================================================
// The username, email and password fields are REQUIRED, so give an error if left empty
if(isset($_POST['username'])&&trim($_POST['username'])!="" && isset($_POST['email'])&&trim($_POST['email'])!="" && isset($_POST['password'])&&trim($_POST['password'])!=""){
    $username=$_POST['username'];
    $email=$_POST['email']; 
    $password=$_POST['password'];
}
else{
    $hasError=true;
    $errorMsg.="Please provide a username, email AND password.";
}
// Check if both passwords match
if(!$hasError){
    if(!isset($_POST['passwordRetyped'])||$_POST['passwordRetyped']!=$password){
        $hasError=true;
        $errorMsg.="Passwords do not match.";
    }
}

// =======================================================================================
// Create the user in the database
// =======================================================================================
// Create the user if there is no error
if(!$hasError){
    // Connect to the database
    $conn=connect();

    // We successfully connected to the database
    if($conn){
        // Create the user with the provided fields
        if(create_user($conn,$username,$password,$email,$firstname,$lastname,$telephone)){
            // Nothing to do here (no errors = success)
        }
        else{
            // If the user cannot be created, it already exists
            $hasError=true;
            $errorMsg="Username already exists. Try again with another username.";
        }
    }
    else{
        // No able to connect
        $hasError=true;
        $errorMsg.="Error while connecting to the database.";
    }
}

// =======================================================================================
// Go to the chat room or show an error
// =======================================================================================
if($hasError){
    // Show the errors to the user on the Signup page
    $smarty->assign("errorMsg",$errorMsg);
    $smarty->display("Signup.html");
}
else{
    // Login the user and go to the chat room
    save_login_info($username);
    $smarty->assign("username",$username);
    $smarty->display("ChatRoom.html");
}
?>