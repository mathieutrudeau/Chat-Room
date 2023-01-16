<?php

    /* ========================================================================================
        Made by: Mathieu Trudeau
        Student id: 1754757
        functions: get_messages_older_than & save_message
        purpose: - the purpose of get_message_older_than is to get all the that where save before
        the given id.
        - The purpose of save_message is to save the text of a message sent by a user.
       ========================================================================================
    */ 

    $this_dir = __DIR__;

    # include the connection routines
    require_once("$this_dir/DB_connect.php");
    
    # include the user routines
    require_once("$this_dir/DB_user.php");


    //CONSTANTS
    const MAX_MESSAGES=10;
    
    //ERROR CODES
    const ERROR_LOGOUT =1;
    const ERROR_INVALID_DB_HANDLE=3;
    const ERROR_TEXT_TOO_LONG=2;
    const ERROR_INVALID_USER=4;


    # include code for formatting
    # require_once("$this_dir/../View/message_view.php");
    
    # ===========================================================================
    # Message format
    # ===========================================================================
    # array ("text" =>  string,     # message text
    #        "id"   =>  integer     # message id
    #        "user" =>  string      # name of user who sent the message
    #       "time"  =>  string      # time message was sent (Unix UTC timestamp)
    # ---------------------------------------------------------------------------

    # ===========================================================================
    # get_message_older_than
    # ===========================================================================
    # purpose: returns messages that have a message ID greater than msgID
    #          ... limited to the latest $maxMessages
    # inputs: database handle
    #         msgID (defaults to zero)
    #         maxMessages (defaults to MAX_MESSAGES)
    # returns: returns an array of messages (see msg construct above)
    # errors: Throws an exception _only_ if there is a database error
    # ---------------------------------------------------------------------------
    function get_messages_older_than ($dbh, $msgID=0, $maxMessages = MAX_MESSAGES) {
        
        // VALIDATION
        // -----------------------------------------------------------------------
        // Validates that the maxMessages is an integer
        if(!is_int($maxMessages)){
            $maxMessages=MAX_MESSAGES;
        }
        // Validates that the maxMessages is a positive integer 
        elseif($maxMessages<=0){
            $maxMessages=MAX_MESSAGES;
        }

        if(!is_logged_in()){
            throw new Exception("Error: User is logged out.", $code=ERROR_LOGOUT);
        }
        
        // Validates that the dbh object is actually an object
        if(gettype($dbh)!="object"){
            throw new Exception("Error: Connection is not an object.", $code=ERROR_INVALID_DB_HANDLE);
        }
        // -----------------------------------------------------------------------


        
        // Get all messages with a messages id superior to the given one and retrieves a maximum of n (maxMessages)
        $sql = "select * from(select messages.text, messages.id, users.username as user, messages.senttime as time from messages inner join users 
        on users.id = messages.userID where messages.id > ? order by messages.id desc limit ?)Var1 Order By id asc";

        // Get the messages form the database
        $stmnt = $dbh->prepare($sql);
        $stmnt->bind_param("ii",$msgID, $maxMessages);
        $stmnt->execute();
        $result = $stmnt->get_result();
        
        // Array that stores all the results
        $rows = array();

        $id=0;

        // Add all the retrieved messages to the array
        while ($row = $result->fetch_assoc()) {        
            array_push($rows,$row);
        }
                
        // Close the statement
        $stmnt->close();

        // Return an array with all the messages
        return $rows;
    }

        
    # ===========================================================================
    # save_message
    # ===========================================================================
    # purpose: returns all the messages that have a message ID greater than msgID
    # inputs: $dbh      # database handle
    #         $user     # user who sent the message 
    #         $text     # text of the message (empty string allowed)
    # returns: null
    # errors: Throws an exception if :
    #               there is a database error
    #               user doesn't exist or is invalid (empty string)
    #               the text string exceeds the maximum
    # ---------------------------------------------------------------------------
    function save_message($dbh,$user,$text) {

        // VALIDATION
        // -----------------------------------------------------------------------
        // Validates that the dbh object is actually an object
        if(gettype($dbh)!="object"){
            throw new Exception("Error: Connection is not an object.", $code=ERROR_INVALID_DB_HANDLE);
        }

        if(!is_logged_in()){
            throw new Exception("Error: User is logged out.", $code=ERROR_LOGOUT);
        }

        // Make sure the text is not too long to be stored in the database
        if(strlen($text)>1024){
            throw new Exception("Error: Text is too long.", $code=ERROR_TEXT_TOO_LONG);
        }
        // -----------------------------------------------------------------------

        // Get the id for the user from the database
            $sql = "select id from users where username=?";
            $stmnt = $dbh->prepare($sql);
            $stmnt->bind_param("s",$user);
            $stmnt->bind_result($userID);
            $stmnt->execute();
            $stmnt->fetch();
            $stmnt->close();

            // If the user does not exist, throw an exception
            if($userID==null){
                throw new Exception("Error: User does not exist.", $code=ERROR_INVALID_USER);
            }

            // Everything is good, so save the message in the database
            $sql="INSERT INTO messages (userID,text) VALUE (?,?)";
            $stmnt=$dbh->prepare($sql);
            $stmnt->bind_param('is',$userID,$text);
            $stmnt->execute();
            $stmnt->close();
    }
?>