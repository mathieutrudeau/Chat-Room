<?php


    # include the connection routines
    $this_dir = __DIR__;
    require_once("$this_dir/DB_connect.php");

    # ===========================================================================
    # validate_user
    # ===========================================================================
    # purpose: validates the username/password combination of an active user
    # inputs: database handle
    #         user name (case insensitive)
    #         user password (case sensitive)
    # Note:
    #   passwords are sent in as plain text, but compared to encrypted
    #      passwords stored in database using php password_verify()
    #
    # returns: true if the credentials are correct, false otherwise
    # errors: Throws an exception _only_ if there is a database error
    # ---------------------------------------------------------------------------
        
    function validate_user ($dbh, $user, $pass) {
        
        # get password from database for this user
        $sql = "select password from users where username = lower(?) and active_flag = 1";
        $sth = $dbh->prepare($sql);
        $sth->bind_param("s",$user);
        $sth->execute();
        $sth->bind_result($hash);
        
        # if we have a result, user exists
        if ($sth->fetch()) {
            
            # do the passwords match?
            if (password_verify($pass,$hash)) {
                return true;
            }
            else {
                error_log("$user failed to verify password");
                return false;
            }
        }
        
        # the user does not exist
        else {
            error_log("$user tried to validate, but does not exist");
            return false;
        }
        
        return false;
        
    } 
    
   # ===========================================================================
    # create_user
    # ===========================================================================
    # purpose: creates a new user IF that user username is unique among 
    #          active users
    # inputs: database handle 
    #         new user name (case insensitive, non-empty string)
    #         new user password (case sensitive, non-empty string)
    #         new user email (case insensitive)
    #
    # Note:
    #   passwords are sent in as plain text, encrypted using php
    #      password_hash() function, using the PASSWORD_DEFAULT flag
    #      for the type of encryption
    #      The encrypted password is saved in the database
    #
    # returns: 
    #         true if the user was created, 
    #         false if username already exists
    # errors: Throws an exception if there is a database error
    #         Throws an exception if the username and/or password is an empty string
    # ---------------------------------------------------------------------------
    function create_user($dbh, $user, $pass, $email, $first=NULL, $last=NULL, $tel=NULL) {

        # username and password cannot be empty
        if (empty($user)) {
            throw new Exception('Username is empty');
        }
        if (empty($pass)) {
            throw new Exception("Password is empty");
        }
        
        # does the user already exists
        if (user_exists($dbh, $user)) {
            error_log("Cannot create <$user>, already exists");
            return false;
        }

        # hash the password
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        # insert user into database
        $sql = "insert into users (username, password, email, active_flag, first_name, last_name, phone, created_date) 
            values (lower(?),?,lower(?),1,?,?,?,CURRENT_TIMESTAMP())";
        $sth = $dbh->prepare($sql);
        $sth->bind_param("ssssss",$user,$hash,$email,$first,$last,$tel);
        $sth->execute();
        
        return true;
        
    }
    
    # =========================================================================
    # user_exists
    # =========================================================================
    # purpose: does the user exist in the database and is an active account
    # inputs: database handle
    #         user name (case insensitive)
    # returns: true or false
    # errors: Throws an exception if there is a database error
    # -------------------------------------------------------------------------
    function user_exists( $dbh, $user) {
        
        # does the user exist and is she active?
        $sql = "select username from users where username=lower(?) and active_flag=1";
        $sth = $dbh->prepare($sql);
        $sth->bind_param("s",$user);
        $sth->execute();
        $sth->bind_result($db_user);
        
        # if we have a result, user exists
        if ($sth->fetch()) {
            return true;
        }
        else {
            return false;
        }

    }    

    # =========================================================================
    # user_id
    # =========================================================================
    # purpose: gets the user id of an ACTIVE user
    # inputs: database handle
    #         user name (case insensitive)
    # returns: user_id (0 implies no active user by that name)
    # errors: Throws an exception if there is a database error
    # -------------------------------------------------------------------------
    function user_id( $dbh, $user) {
        
        $sql = "select id from users where username=lower(?) and active_flag=1";
        $sth = $dbh->prepare($sql);
        $sth->bind_param("s",$user);
        $sth->execute();
        $sth->bind_result($db_id);
        
        # if row exists, return the user id (active user exists)
        if($sth->fetch()) {
            return $db_id;
        }
        
        # if row does not exist, active user by that name does not exist
        else{
            return 0;
        }

    }    

    # =========================================================================
    # deactivate_user
    # =========================================================================
    # purpose: removes user from list of valid users
    #          - username will be available for new users
    # inputs: database handle
    #         user name (case insensitive)
    # returns: true if there is no active account for that user
    #          true if user was deactivated
    #          false otherwise
    # errors: Throws an exception _only_ if there is a database error
    # -------------------------------------------------------------------------
    function deactivate_user ( $dbh, $user) {
        
        # deactivate the user
        $sql = "update users set active_flag=0 where username=lower(?)";
        $sth = $dbh->prepare($sql);
        $sth->bind_param("s",$user);
        $sth->execute();

        # make sure
        return !user_exists($dbh,$user);
    }

    # =========================================================================
    # reactivate_user
    # =========================================================================
    # purpose: if username not taken by a new user, reactive this user
    #          otherwise return false
    # inputs: database handle
    #         user name (case insensitive)
    # returns: true if user is reactivated
    #          false otherwise
    # errors: Throws an exception _only_ if there is a database error
    # -------------------------------------------------------------------------
    function reactivate_user ( $dbh, $user) {
        
        # can only reactivate user is someone else has not taken that name
        if (user_exists($dbh, $user)) {
            return false;
        }
        
        # re-activate the user
        $sql = "update users set active_flag=1 where username=lower(?)";
        $sth = $dbh->prepare($sql);
        $sth->bind_param("s",$user);
        $sth->execute();

        # make sure
        return user_exists($dbh,$user);
    }

?>
