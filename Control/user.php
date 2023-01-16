<?php
    # ========================================================================
    # user.php
    # ========================================================================
    # PURPOSE:
    # Verifies that all information that is required for user interaction is
    # present, performs the 'action', and returns info to the client via 
    # a JSON string.
    # 
    # SESSION DATA
    # if user logs in, then set session 'username' to logged in user
    # if user logs out, then set session 'username' to ''
    # if user deactivates themselves, then set username to ''
    # if anything happens with a user, set "last_activity" to time()
    #
    # INPUTS: by POST (they may not be set - you need to check)
    #   action: "login" or "create_user" or "deactivate_user" or "logoff"
    #           "logged_in_as"
    #   username:   "username"
    #   password:   "password"
    #   email:      "email" 
    #   database: (optional - defaults to 'chat')
    #
    # VALID ACTIONS:
    #   login
    #       errors: username is null or empty string, 
    #               password is null or empty string, 
    #               username/password is invalid 
    #       session: sets 'username' and 'last_activity'
    #
    #   create_user
    #       errors: user with that name already exists, 
    #               username is null or empty string, 
    #               password is null or empty string
    #               email is null
    #       session: sets 'username' and 'last_activity'
    #
    #   deactivate_user - can only deactivte your own account
    #       errors: username is null or empty string
    #               password is null or emtpy string
    #               username/password is invalid (can only delete yourself)
    #       session: sets 'username' to ""
    #
    #   logoff
    #       session: sets 'username' to ""
    #
    #   logged_in_as
    #
    # PRINTS for all actions (JSON string):
    #   username: username            (if user logged off, set to "", else username)
    #   error_string: error string    (set to empty string if no error)
    #   error_number: integer         (integer uniquely defining the error)
    #
    # Error Numbers & Messages: 
    #    (error number 0 and error message '' indicates no errors)
    #  
    #   1    Username is already taken
    #   2    Invalid username/password 
    #  99    Experiencing technical difficulties 
    #   3    Email address is not defined 
    #   4    Username is not defined 
    #   5    Password is not defined 
    #   6    Could not deactivate user  
    #  98    Requested action is not available 
    #
    # Exception Handling
    # * if there is an exception, this will return a JSON string 
    #   error_string: "We are experiencing technical difficulties"
    #   error_number: 99
    # * Exception message & other info will be written to the log file
    # ========================================================================
    
    # ========================================================================
    # include files
    # ========================================================================
    $this_dir = __DIR__;
    require_once("$this_dir/../Control/useful_functions.php");
    require_once("$this_dir/../Model/DB_user.php");

    # ========================================================================
    # set up error handling
    # ========================================================================
    error_reporting(E_ALL);
    ini_set('display_errors','On');
    ini_set('error_log',"../$this_dir/log_file.log");
    ini_set('log_errors','On');
    
    # ========================================================================
    # set up error strings array
    # ========================================================================
    $lang = get_set_language();
    global $dict_errors;
    $ERRORS = array(
        'USER_TAKEN'=>array(1,$dict_errors[$lang]["USER_TAKEN"]),
        'USERNAME_PASSWORD'=>array(2,$dict_errors[$lang]["invalid_user_pass"]),
        'DATABASE' => array(99,$dict_errors[$lang]["DATABASE"]),
        'EMAIL_NOT_DEFINED' => array(3,$dict_errors[$lang]["missing_email"]), 
        'USERNAME_NOT_DEFINED' => array(4,$dict_errors[$lang]["missing_user"]), 
        'PASSWORD_NOT_DEFINED' => array(5,$dict_errors[$lang]["missing_password"]), 
        'NOT_DEACTIVATED' => array(6,$dict_errors[$lang]["NOT_DEACTIVATED"]),
        'UNKNOWN_ACTION' => array(98,$dict_errors[$lang]["UNKNOWN_ACTION"])
        );
        
    
    # ========================================================================
    # read post variables
    # ========================================================================
    $database = "chat";
    if (! empty($_POST['database'])) {
        $database = $_POST['database'];
    }
    $action = "";
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    }
    

    # ========================================================================
    # json output array
    # ========================================================================
    $output = array('username'=>"",'error_number'=>0,'error_string'=>'');

    # ========================================================================
    # catch any thrown exception
    # ========================================================================
    set_exception_handler('ExceptionHandler');
    function ExceptionHandler ($e) {
        global $ERRORS;
        error_log($e->getLine().":".$e->getFile()."\n".$e->getMessage()."\n");
        $output['error_number'] = $ERRORS['DATABASE'][0];
        $output['error_string'] = $ERRORS['DATABASE'][1] . " ".
        $e->getLine().":".$e->getFile()."\n".$e->getMessage()."\n";
        print json_encode($output);
        exit();
    }


    # ========================================================================
    # perform action
    # ========================================================================
    switch ($action) {

        # --------------------------------------------------------------------
        # login
        # --------------------------------------------------------------------
        case 'login':
            if (empty($_POST['username'])) {
                $output['error_number'] = $ERRORS['USERNAME_NOT_DEFINED'][0];
                $output['error_string'] = $ERRORS['USERNAME_NOT_DEFINED'][1];
            }
            elseif (empty($_POST['password'])) {
                $output['error_number'] = $ERRORS['PASSWORD_NOT_DEFINED'][0];
                $output['error_string'] = $ERRORS['PASSWORD_NOT_DEFINED'][1];
            }
            else {
                $dbh = connect($database);
                $result = validate_user($dbh, $_POST['username'], $_POST['password']);
                if ($result) {
                    $output['username'] = $_POST['username'];
                    save_login_info($_POST['username']);
                }
                else {
                    $output['error_number'] = $ERRORS['USERNAME_PASSWORD'][0];
                    $output['error_string'] = $ERRORS['USERNAME_PASSWORD'][1];
                }
            }
            break;

        # --------------------------------------------------------------------
        # create_user
        # --------------------------------------------------------------------
        case 'create_user':
            if (empty($_POST['username'])) {
                $output['error_number'] = $ERRORS['USERNAME_NOT_DEFINED'][0];
                $output['error_string'] = $ERRORS['USERNAME_NOT_DEFINED'][1];
            }
            elseif (empty($_POST['password'])) {
                $output['error_number'] = $ERRORS['PASSWORD_NOT_DEFINED'][0];
                $output['error_string'] = $ERRORS['PASSWORD_NOT_DEFINED'][1];
            }
            elseif (!isset($_POST['email'])) {
                $output['error_number'] = $ERRORS['EMAIL_NOT_DEFINED'][0];
                $output['error_string'] = $ERRORS['EMAIL_NOT_DEFINED'][1];
            }
            else {
                $dbh = connect($database);
                $result = create_user($dbh, $_POST['username'], $_POST['password'], $_POST['email']);
                if ($result) {
                    $output['username'] = $_POST['username'];
                    save_login_info($_POST['username']);
                }
                else {
                    $output['error_number'] = $ERRORS['USER_TAKEN'][0];
                    $output['error_string'] = $ERRORS['USER_TAKEN'][1];
                }
            }
            break;
            
        # --------------------------------------------------------------------
        # deactivate_user
        # --------------------------------------------------------------------
        case 'deactivate_user':
            if (empty($_POST['username'])) {
                $output['error_number'] = $ERRORS['USERNAME_NOT_DEFINED'][0];
                $output['error_string'] = $ERRORS['USERNAME_NOT_DEFINED'][1];
            }
            elseif (empty($_POST['password'])) {
                $output['error_number'] = $ERRORS['PASSWORD_NOT_DEFINED'][0];
                $output['error_string'] = $ERRORS['PASSWORD_NOT_DEFINED'][1];
            }
            else {
                $dbh = connect($database);
                
                # can user deactivate themselves?
                $result = validate_user($dbh, $_POST['username'], $_POST['password']);
                if ($result) {
                    $result = deactivate_user($dbh, $_POST['username']);
                    reset_login_info();
                }
                
                # no they can't, because they didn't have the right password
                else {
                    $output['error_number'] = $ERRORS['USERNAME_PASSWORD'][0];
                    $output['error_string'] = $ERRORS['USERNAME_PASSWORD'][1];
                }

           }
            break;
            
        # --------------------------------------------------------------------
        # logoff
        # --------------------------------------------------------------------
        case 'logoff':
            reset_login_info();
            break;
            
        # --------------------------------------------------------------------
        # logged_in_as
        # --------------------------------------------------------------------
        case 'logged_in_as':
            $output['username'] = logged_in_as();
            break;

        # --------------------------------------------------------------------
        # default
        # --------------------------------------------------------------------
        default:
            $output['error_number'] = $ERRORS['UNKNOWN_ACTION'][0];
            $output['error_string'] = $ERRORS['UNKNOWN_ACTION'][1];
            
    }

    # ========================================================================
    # print json string
    # ========================================================================
    echo json_encode($output);
    

?>
