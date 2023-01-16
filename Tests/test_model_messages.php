<?php
$test_dir = __DIR__;
require("$test_dir/framework.php");
require("$test_dir/../Model/DB_messages.php");

$log_file = "$test_dir/test_model_log.log";
$login = "validate_user";
$connect = "connect";
$create_user = "create_user";
$user_exists = "user_exists";
$delete_user = "deactivate_user";
$database = "chattest";
$get_messages = "get_messages_older_than";
$max_messages = MAX_MESSAGES;

# --------------------------------------------------------------------------
# Set up initial state
# --------------------------------------------------------------------------
# use a special log file for this
ini_set('error_log',$log_file);
error_reporting(E_ALL);  

    
# =====================================================================
# Testing Model/DB_User.php
# =====================================================================
print "\n\n";
print "========================================================\n";
print "TESTING Model/DB_Messages.php\n";
print "========================================================\n";
    
# --------------------------------------------------------------------------
# Do I have the required files and functions?
# --------------------------------------------------------------------------
print "\n--------------------------------------------------------\n";
print "Files and functions exist?\n";
print "--------------------------------------------------------\n";

$functions_ok = 0;

$functions_ok += isa_function($get_messages, "'$get_messages function exists");

if ($functions_ok != 1) {
    bail_out('Missing some functions');
}


# --------------------------------------------------------------------------
# SETUP - EMPTY DATABASE OF ALL DATA
# --------------------------------------------------------------------------
print "\n--------------------------------------------------------\n";
print "Setting up database (set up 5 messages to start)\n";
print "--------------------------------------------------------\n";

# set up the test databases 
`mysql -u root < $test_dir/create_db.sql`;

# connect to the bad database;
try {
    $dbh_bad = connect('baddb');
}
catch (Exception $e) {
    bail_out('Could not connect to baddb - Aborting tests');
}

# connect to the database;
try {
    $dbh = connect($database);
}
catch (Exception $e) {
    bail_out('Could not connect to $database - Aborting tests');
}

# --------------------------------------------------------------------------
# Read Messages
# db starts with 5 messages,
# --------------------------------------------------------------------------
print "\n--------------------------------------------------------\n";
print "read messages (default inputs)\n";
print "--------------------------------------------------------\n";


$result = get_messages_older_than($dbh);
ok (is_array($result),"Get messages returns array");
is (count($result),5,"Five messages returned");
for($i=0;$i<5;$i++) {
    is($result[$i]['id'],$i+1,"Message ".($i+1) ." returned in correct order");
}
# random verification of text of message, and user
is($result[2]['text'],'lets party!',"msg 3 text is correct");
is($result[2]['user'],'sandy',"msg 3 username is correct");

print "\n--------------------------------------------------------\n";
print "read messages (after message 2)\n";
print "--------------------------------------------------------\n";

$result = get_messages_older_than($dbh,2);
ok (is_array($result),"Get messages returns array");
is (count($result),3,"Three messages returned");
for($i=0;$i<3;$i++) {
    is($result[$i]['id'],$i+3,"Message ". ($i+3). " returned in correct order");
}

print "\n--------------------------------------------------------\n";
print "read messages (after message 5)\n";
print "--------------------------------------------------------\n";

$result = get_messages_older_than($dbh,5);
ok (is_array($result),"Get messages returns array");
is (count($result),0,"No messages returned");

print "\n--------------------------------------------------------\n";
print "read messages (after message 1, limit 3 )\n";
print "... should return messages 3,4,5 (not 2,3,4)!\n";
print "--------------------------------------------------------\n";

$result = get_messages_older_than($dbh,1,3);
ok (is_array($result),"Get messages returns array");
is (count($result),3,"Three messages returned");
for($i=0;$i<3;$i++) {
    is($result[$i]['id'],$i+3,"Message ". ($i+3). " returned in correct order");
}

print "\n--------------------------------------------------------\n";
print "Bad Inputs:\n";
print "msgID is not a valid int, defaults to zero\n";
print "--------------------------------------------------------\n";

$result = get_messages_older_than($dbh,"a");
is (count($result),5,"Five messages returned");
for($i=0;$i<5;$i++) {
    is($result[$i]['id'],$i+1,"Message ".($i+1) ." returned in correct order");
}

print "\n--------------------------------------------------------\n";
print "Bad Inputs:\n";
print "Max number of messages is not a valid int, defaults to large number bigger than 5\n";
print "--------------------------------------------------------\n";

$result = get_messages_older_than($dbh,1,"x");
is (count($result),4,"Four messages returned");
for($i=0;$i<4;$i++) {
    is($result[$i]['id'],$i+2,"Message ". ($i+2). " returned in correct order");
}


print "\n--------------------------------------------------------\n";
print "Bad Inputs:\n";
print "Max number of messages is not > 0, defaults to large number bigger than 5\n";
print "--------------------------------------------------------\n";

$result = get_messages_older_than($dbh,1,0);
is (count($result),4,"Four messages returned");
for($i=0;$i<4;$i++) {
    is($result[$i]['id'],$i+2,"Message ". ($i+2). " returned in correct order");
}

print "\n--------------------------------------------------------\n";
print "Bad Inputs: Exception thrown\n";
print "dbh is not an mysqli object\n";
print "--------------------------------------------------------\n";
try {
    $x = 15;
    $result = get_messages_older_than($x,1,0);
    ok (false,"Exception was NOT thrown when database handle is not a database handle");
}
catch (Exception $e) {
    ok (true,"Exception was thrown when database handle is not a database handle");
    is($e->getCode(),ERROR_INVALID_DB_HANDLE,"Error exception number is correct");
}

print "\n--------------------------------------------------------\n";
print "Bad Inputs: Exception thrown\n";
print "database error (table doesn't exist)\n";
print "--------------------------------------------------------\n";
$dbh_bad = connect('baddb');
try{
    get_messages_older_than($dbh_bad,'sandy');
    ok (false,"Exception was NOT thrown when database was bad");
}
catch(Exception $e){
    ok (true, "Exception was thrown when database was bad");
}


# --------------------------------------------------------------------------
# Save Messages
# db starts with 5 messages,
# --------------------------------------------------------------------------
$dbh = connect($database);

print "\n--------------------------------------------------------\n";
print "save message - no output, no exception thrown\n";
print "--------------------------------------------------------\n";
save_message($dbh,"sandy","hello world");
ok(true,"Program didn't crash saving a message");
$result = get_messages_older_than($dbh);
$last_message = $result[count($result)-1];
is($last_message['id'],6,"last message retrieved has id 6");
is($last_message['user'],'sandy',"last message was sent by sandy");
is($last_message['text'],"hello world","last message retrieved has correct text");


print "\n--------------------------------------------------------\n";
print "save message - empty text\n";
print "--------------------------------------------------------\n";
save_message($dbh,"bob","");
ok(true,"Program didn't crash saving an empty message");
$result = get_messages_older_than($dbh);
$count = count($result);
$last_message = $result[$count-1];
is($last_message['id'],7,"last message retrieved has id 7");
is($last_message['user'],'bob',"last message was sent by bob");
is($last_message['text'],"","last message retrieved has correct text");

print "\n--------------------------------------------------------\n";
print "save message - null text\n";
print "--------------------------------------------------------\n";
save_message($dbh,"bob",null);
ok(true,"Program didn't crash saving a null message");
$result = get_messages_older_than($dbh);
$count = count($result);
$last_message = $result[$count-1];
is($last_message['id'],8,"last message retrieved has id 8");
is($last_message['user'],'bob',"last message was sent by bob");
is($last_message['text'],null,"last message retrieved has correct text");

print "\n--------------------------------------------------------\n";
print "Bad Inputs: Exception thrown\n";
print "dbh is not an mysqli object\n";
print "--------------------------------------------------------\n";
try {
    $x = 15;
    $result = save_message($x,"bob","");
    ok (false,"Exception was NOT thrown when database handle is not a database handle");
}
catch (Exception $e) {
    ok (true,"Exception was thrown when database handle is not a database handle");
    is($e->getCode(),ERROR_INVALID_DB_HANDLE,"Error exception number is correct");
}

print "\n--------------------------------------------------------\n";
print "Bad Inputs: Exception thrown\n";
print "user is invalid\n";
print "--------------------------------------------------------\n";
try {
    $x = 15;
    $result = save_message($dbh,"bobby","say what?");
    ok (false,"Exception was NOT thrown when invalid user");
}
catch (Exception $e) {
    ok (true,"Exception was thrown when invalid user");
    is($e->getCode(),ERROR_INVALID_USER,"Error exception number is correct");
}

print "\n--------------------------------------------------------\n";
print "Bad Inputs: Exception thrown\n";
print "text is waa...aay too long\n";
print "--------------------------------------------------------\n";
try {
    $x = 15;
    $result = save_message($dbh,"bob","say what?
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx xxxx 
    ");
    ok (false,"Exception was NOT thrown when text too long");
}
catch (Exception $e) {
    ok (true,"Exception was thrown when text too long");
    is($e->getCode(),ERROR_TEXT_TOO_LONG,"Error exception number is correct");
}

print "\n--------------------------------------------------------\n";
print "Bad Inputs: Exception thrown\n";
print "database error (table doesn't exist)\n";
print "--------------------------------------------------------\n";
$dbh_bad = connect('baddb');
try{
    save_message($dbh_bad,'sandy',"die!");
    ok (false,"Exception was NOT thrown when database was bad");
}
catch(Exception $e){
    ok (true, "Exception was thrown when database was bad");
}



# --------------------------------------------------------------------------
# Print Report
# --------------------------------------------------------------------------
finished();
exit();

    
    

    

?>