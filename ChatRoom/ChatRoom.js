// ==============================================================================================================
// Made by Mathieu Trudeau (2019)
// Name: ChatRoom.js
// Description: Periodically retrieves new messages from the database and saves any message that have been sent.
// ==============================================================================================================

// ==========================================================================================
// EVENTS & CONSTANTS
// ------------------------------------------------------------------------------------------
// Show all the messages (Up to a certain maximum) when the page loads
window.addEventListener("load", GetAllMessages);

// Add all the new messages every 10 seconds
let intervalID = window.setInterval(function(){
    GetMessages();
},10000);

// Name of the database to use
var DB_NAME = "chattest";

// ==========================================================================================
// GET NEW MESSAGES: AJAX CALL
// ------------------------------------------------------------------------------------------
function GetMessages(){
    // Prepare the ajax call
    var data={};
    data.action = "get_messages";
    data.database = DB_NAME;
    var urlName = "../Control/messages.php";

    // Get all new messages that have not been seen
    $.post(urlName,data,GetMessagesResponse);
}
// ------------------------------------------------------------------------------------------

// ==========================================================================================
// GET ALL MESSAGES: AJAX CALL
// ------------------------------------------------------------------------------------------
function GetAllMessages(){
    // Prepare the ajax call
    var data={};
    data.action = "get_all_messages";
    data.database = DB_NAME;
    var urlName = "../Control/messages.php";

    // Get all messages
    $.post(urlName,data,GetMessagesResponse);
}
// ------------------------------------------------------------------------------------------

// ==========================================================================================
// SAVE NEW MESSAGE: AJAX CALL
// ------------------------------------------------------------------------------------------
function SaveMessage(){
    // Get the message that we need to save
    var message = document.getElementById("message");
    
    // Prepare the ajax call
    var data = {}; 
    data.message = message.value;
    data.action = "save_message";
    data.database = DB_NAME;
    var urlName = "../Control/messages.php";

    // Save the message if its not an empty one
    if(message.value!=""){
        $.post(urlName,data,SaveMessageResponse);
    }
}
// ------------------------------------------------------------------------------------------

// ==========================================================================================
// SHOW MESSAGES: AJAX RESPONSE
// ------------------------------------------------------------------------------------------
function GetMessagesResponse(result){
    // Chat room div where the messages are displayed
    var chatRoom = document.getElementById('chatRoom');

    // Array containing the response of the AJAX call
    var messages = JSON.parse(result);

    // Show the messages on the chat room div if no errors occured
    if(messages['error_number']==0){
        // Append all the messages one-by-one to the chat room
        messages['msgs'].forEach(element => {
            
            // Create the message div
            var message = document.createElement('div');
            // Apply its content (Username & message)
            message.innerHTML=element;

            // Append it to the chat rooom
            chatRoom.appendChild(message);
            // Make sure the scroll adjust so that the latest message can be viewed
            message.scrollIntoView(false);
        });
    }
    else{
        // Log and show the error to the user
        HandleError("ERROR! Unable to retrieve messages.",messages['error_number'],messages['error_string']);
    }
}
// ------------------------------------------------------------------------------------------

// ==========================================================================================
// SAVE MESSAGE: AJAX RESPONSE
// ------------------------------------------------------------------------------------------
function SaveMessageResponse(result){
    // Array containing the response of the AJAX call
    var messages = JSON.parse(result);

    // Reset the text for the message input
    var message = document.getElementById("message");
    message.value="";

    // Show and log any error that prevent the message from being saved
    if(!messages['msg_saved']){
        HandleError("ERROR! Unable to send your message.",messages['error_number'],messages['error_string']);
    }

    // Prevents the form action from getting executed
    return false;
}
// ------------------------------------------------------------------------------------------

// ==========================================================================================
// ERROR HANDLER: LOG AND SHOW ANY ERROR THAT OCCURS TO THE USER
// ------------------------------------------------------------------------------------------
function HandleError(errorDescription,errorNumber,errorMessage){
    console.log("An ERROR occured.\nError #: "+errorNumber+"\nError Message: "+errorMessage);
    window.alert(errorDescription);
    // Stop the interval
    window.clearInterval(intervalID);
}
// ------------------------------------------------------------------------------------------
