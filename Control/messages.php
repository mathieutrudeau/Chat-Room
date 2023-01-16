<?php
    # ========================================================================
    # messages.php
    # ========================================================================
    # PURPOSE:
    # Verifies that all information that is required for user interaction is
    # present, performs the 'action', and returns info to the client via 
    # a JSON string.
    #
    # NOTE: when getting messages, the messages will be return, already formatted
    #       in html
    #
    #
    # INPUTS: by POST 
    #   action: "save_message", "get_messages","get_all_messages"
    #   database: (optional - defaults to 'chat')
    #
    # VALID ACTIONS:
    #   save_message
    #       INPUTS via POST: 
    #               message: string
    #       errors: user not logged in, 
    #               database error, 
    #               message too long,
    #       returns: error_number: integer, error_string: string, msg_saved: boolean
    #
    #   get_messages - gets messages (up to a default or specified maximum)
    #                  that the user has not seen
    #       INPUTS via POST:
    #               max_msgs: integer (optional... max messages to retreived at any given time)
    #               time_zone: integer (optional ... number of minutes difference from UTC)
    #       errors: user not logged in, 
    #               database error, 
    #       returns: json of:
    #               error_number: integer, 
    #               error_string: string, 
    #               msgs: array of html formatted msgs
    #
    #   get_all_messages - gets ALL messages (up to a default or specified maximum)
    #       INPUTS via POST:
    #               max_msgs: integer (optional... max messages to retreived at any given time)
    #               time_zone: integer (optional ... number of minutes difference from UTC)
    #       errors: user not logged in, 
    #               database error, 
    #       returns: json of:
    #               error_number: integer, 
    #               error_string: string, 
    #               msgs: array of html formatted msgs
    #
    #
    # ------------------------------------------------------------------------
    # Exception Handling
    # * if there is an unknown exception, this will return a JSON string 
    #   error_string: "We are experiencing technical difficulties"
    #   error_number: 99
    # * Exception message & other info will be written to the log file
    # ========================================================================
    
    # ========================================================================
    # include files
    # ========================================================================
    $this_dir = __DIR__;
    require_once("$this_dir/../Control/useful_functions.php");
    require_once("$this_dir/../Model/DB_messages.php");

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
        'DATABASE' => array(99,$dict_errors[$lang]["DATABASE"]),
        'NOT_LOGGED_IN' =>array(1,$dict_errors[$lang]['not_logged_in']),
        'TXT_TOO_LONG' => array(2,$dict_errors[$lang]['msg_txt_too_long']),
        'UNKNOWN_ACTION' => array(98,$dict_errors[$lang]["UNKNOWN_ACTION"])
        );
        
    
    # ========================================================================
    # read post variables
    # ========================================================================
    $database = "chattest";
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
    $output = array('error_number'=>0,'error_string'=>'','msgs'=>array(),'msg_saved'=>false);

    # ========================================================================
    # perform action
    # ========================================================================
    switch($action){

        # ========================================================================
        # GET ALL MESSAGES
        # ========================================================================
        case 'get_all_messages':

            // Try to connect to the database
            try{
                $db=connect($database);
            }
            catch(Exception $e){
                $output['error_number']=$e->getCode();
                $output['error_string']=$e->getMessage();
            }

            // Make sure the connection has been given
            if(isset($_POST['database'])){
                // Get all the messages
                try{
                    $messages=get_messages_older_than($db);
                    
                    // Add all the retreived messages to the output with the appropriate html tags and set them as seen
                    $id=0;
                    last_message_seen(0);
                    foreach($messages as $message){
                        $output['msgs'][] = "<div class='usernames'>".$message['user']."</div><div class='messages'>".$message['text']."</div>";
                        $id=$message['id'];
                    }
                    last_message_seen($id);
                }
                catch(Exception $e){
                    // Capture the error if it occurs
                    $output['error_number']=$e->getCode();
                    $output['error_string']=$e->getMessage();
                }
            }
            ob_clean();
            break;

        # ========================================================================
        # SAVE MESSAGE
        # ========================================================================
        case 'save_message':
            // Try to connect to the database
            try{
                $db=connect($database);
            }
            catch(Exception $e){
                $output['error_number']=$e->getCode();
                $output['error_string']=$e->getMessage();
            }

            // Make sure the connection has been given
            if(isset($_POST['database'])){

                // Save the message to the database and output true if it has been successully saved
                try{            
                    $result=save_message($db,logged_in_as(),$_POST['message']);
                    $output['msg_saved']=true;
                }
                catch(Exception $e){
                    // Capture the error if it occurs
                    $output['error_number']=$e->getCode();
                    $output['error_string']=$e->getMessage();
                }
            }
            ob_clean();
            break;

        # ========================================================================
        # GET MESSAGES
        # ========================================================================
        case 'get_messages':
            // Try to connect to the database
            try{
                $db=connect($database);
            }
            catch(Exception $e){
                $output['error_number']=$e->getCode();
                $output['error_string']=$e->getMessage();
            }
            
            // Make sure the connection to the database has been given
            if(isset($_POST['database'])){
                // Set the specified max, if not specified make it 0
                $max=0;
                if(isset($_POST['max_msgs'])){
                    $max=$_POST['max_msgs'];
                }

                // Get the id of the last message that was seen and get all the newer ones
                try{
                    $last = last_message_seen();
                    $messages=get_messages_older_than($db,$last,$max);

                    // Retrieve all the newer messages and set them as seen
                    foreach($messages as $message){
                        $output['msgs'][] = "<div class='usernames'>".$message['user']."</div><div class='messages'>".$message['text']."</div>";
                        $id=$message['id'];
                    }
                    if(isset($id)){
                        last_message_seen($id);
                    }
                }
                catch(Exception $e){
                    // Capture the error if it occurs
                    $output['error_number']=$e->getCode();
                    $output['error_string']=$e->getMessage();
                }
            }
            ob_clean();
            break;
    }

    # ========================================================================
    # print json string
    # ========================================================================
    ob_clean();
    print(json_encode($output));
    

    function return_error($o,$e) {
            ob_clean();
            $o['error_number'] = $e[0];
            $o['error_string'] = $e[1];
            echo json_encode($o);
            exit;
    }


    


?>
