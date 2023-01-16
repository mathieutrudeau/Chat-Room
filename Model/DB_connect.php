<?php
    // I want all database errors to throw an exception
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    # =========================================================================
    # connect
    # =========================================================================
    # purpose: Connect to the requested database
    # inputs: database name (defaults to "chat")
    # outputs: mysqli object (database handle)
    # errors: Throws an exception if there is a database error
    # ---------------------------------------------------------------------------
    function connect ($database = "chattest")
    {
        # set info
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbport = "3306";
        $dbh = new mysqli($servername, $username, $password, $database, $dbport);

        # return connection object
        return $dbh;
    }
?>
