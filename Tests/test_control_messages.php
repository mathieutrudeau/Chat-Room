<?php
$test_dir = __DIR__;
require("$test_dir/framework.php");
require("$test_dir/../Model/DB_messages.php");

$log_file = "$test_dir/test_user_log.log";

# =====================================================================
# Testing messages.php
# =====================================================================
print "\n\n--------------------------------------------------------\n";
echo "TESTING Control/messages.php\n";
print "--------------------------------------------------------\n";
# Note: don't need to explicitly test if JSON string is returned,
#       because if it isn't, this test program would crash,
#       indicating an error
    
# ---------------------------------------------------------------------

print "\n--------------------------------------------------------\n";
print "Setting up database\n";
print "--------------------------------------------------------\n";

# set up the test databases dev and dev
`mysql -u root < $test_dir/create_db.sql`;

# connect to the database 'chattest';
try {
    $dbh_bad = connect('baddb');
}
catch (Exception $e) {
    bail_out('Could not connect to baddb - Aborting tests');
}

print "\n--------------------------------------------------------\n";
print "apache up and running?\n";
print "--------------------------------------------------------\n";

# is apapche up and running?
$result = post(array('action'=>'xxxx',
                        'user_name' => 'alex',
                        'pass' => 'alex',
                        'email' => 'alex@alex',
                        'database' => 'xxxx'),
                    "$test_dir/../Control/user.php");
ok($result,"apache server is running");
if (!$result) {
    bail_out("Please start your apache server, OR, you forgot to print the json string!");
}

if (strpos($result,"Sign-in") != false) {
    bail_out("You need to set your 'Share' applications to 'public'");
}

# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
# clear out cookies so that we start with a clean session
# ---------------------------------------------------------------------
remove_cookies();

print "\n--------------------------------------------------------\n";
print "Login";
print "\n--------------------------------------------------------\n";
$result = post(array('action'=>'login',
                        'database' => 'chattest',
                        'password' => 'sandy',
                        'username' => 'sandy'),
                    "$test_dir/../Control/user.php");

$obj = json_decode($result,true);
is ($obj['error_number'],0,"Logged in");

# ---------------------------------------------------------------------
# get all messages (and verify content of messages)
# ---------------------------------------------------------------------
print "\n--------------------------------------------------------\n";
print "Get all messages\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'get_all_messages',
                        'database' => 'chattest'),
                    "$test_dir/../Control/messages.php");

print ($result);

is_valid_JSON($result,"get_all_messages JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],0,"get_all_messages - no error");
is (count($obj['msgs']),5,"Five messages returned");
regex_is($obj['msgs'][0],"/<div.*?>hello everyone<\/div>/",
        "msg 0 html <div> tag with proper text in it");

print "\n--------------------------------------------------------\n";
print "Get messages - none should be returned because they have all been read\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'get_messages',
                        'database' => 'chattest'),
                    "$test_dir/../Control/messages.php");
is_valid_JSON($result,"get_messages JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],0,"get_messages - no error");
is (count($obj['msgs']),0,"No messages returned");

print "\n--------------------------------------------------------\n";
print "Get all messages - all should be returned\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'get_all_messages',
                        'database' => 'chattest'),
                    "$test_dir/../Control/messages.php");
is_valid_JSON($result,"get_all_messages JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],0,"get_all_messages - no error");
is (count($obj['msgs']),5,"Five messages returned");
regex_is($obj['msgs'][0],"/<div.*?>hello everyone<\/div>/",
        "msg 0 html <div> tag with proper text in it");

print "\n--------------------------------------------------------\n";
print "Save a new message\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'save_message',
                        'database' => 'chattest',
                        'message'=>"New message"),
                    "$test_dir/../Control/messages.php");
is_valid_JSON($result,"save_message JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],0,"save_message - no error");
ok ($obj['msg_saved'],"Returned success");

print "\n--------------------------------------------------------\n";
print "Get messages - only last one should be returned\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'get_messages',
                        'database' => 'chattest'),
                    "$test_dir/../Control/messages.php");
is_valid_JSON($result,"get_messages JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],0,"get_messages - no error");
is (count($obj['msgs']),1,"One message returned");
regex_is($obj['msgs'][0],"/<div.*?>New message<\/div>/",
        "msg 0 html <div> tag with proper text in it");
regex_is($obj['msgs'][0],"/<div.*?>sandy<\/div>/",
        "msg 0 html <div> tag with correct user in it");

print "\n--------------------------------------------------------\n";
print "Get messages - none should be returned\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'get_messages',
                        'database' => 'chattest'),
                    "$test_dir/../Control/messages.php");
is_valid_JSON($result,"get_messages JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],0,"get_messages - no error");
is (count($obj['msgs']),0,"No message returned");



print "\n--------------------------------------------------------\n";
print "Save a too long new message\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'save_message',
                        'database' => 'chattest',
                        'message'=>"
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
                        "),
                    "$test_dir/../Control/messages.php");
is_valid_JSON($result,"save_message JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],2,"save_message - too long");
ok (!$obj['msg_saved'],"Returned appropriate 'fail'");

print "\n--------------------------------------------------------\n";
print "Get messages - none should be returned\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'get_messages',
                        'database' => 'chattest'),
                    "$test_dir/../Control/messages.php");
is_valid_JSON($result,"get_messages JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],0,"get_messages - no error");
is (count($obj['msgs']),0,"No message returned");

print "\n--------------------------------------------------------\n";
print "Logout";
print "\n--------------------------------------------------------\n";
$result = post(array('action'=>'logoff',
                        'database' => 'chattest',
                        'password' => 'sandy',
                        'username' => 'sandy'),
                    "$test_dir/../Control/user.php");

$obj = json_decode($result,true);
is ($obj['error_number'],0,"Logged out");

print "\n--------------------------------------------------------\n";
print "Save a new message\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'save_message',
                        'database' => 'chattest',
                        'message'=>"New message"),
                    "$test_dir/../Control/messages.php");
is_valid_JSON($result,"save_message JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],1,"save_message - log out error");
ok (!$obj['msg_saved'],"Returned the correct 'false'");

print "\n--------------------------------------------------------\n";
print "Get messages - logout failure\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'get_messages',
                        'database' => 'chattest'),
                    "$test_dir/../Control/messages.php");
is_valid_JSON($result,"get_messages JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],1,"get_messages - log out error");

print "\n--------------------------------------------------------\n";
print "Get all messages - logout failure\n";
print "--------------------------------------------------------\n";
$result = post(array('action'=>'get_all_messages',
                        'database' => 'chattest'),
                    "$test_dir/../Control/messages.php");
is_valid_JSON($result,"get_messages JSON string");
$obj = json_decode($result,true);
is ($obj['error_number'],1,"get_messages - log out error");

# --------------------------------------------------------------------------
# Print Report
# --------------------------------------------------------------------------
finished();
exit();
