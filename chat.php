<?php
  require_once("chat.class.php")
  $chat = new chat();
  $mode = $_POST['mode'];
  $id = 0;
  
  if($mode == 'SendAndRetrieveNew') {
    $name = $_POST['name'];
	$message = $_POST['message'];
	$color = $_POST['color'];
	$id = $_POST['id'];
	
	if($name != "" || $message != "" || $color != "") {
	  $chat->postNewMessage($name, $message, $color);
	}
  } else if($mode = 'DeleteAndRetrieveNew') {
    $chat->deleteAllMessages();
  } else if($mode = 'RetrieveNew') {
    $id = $_POST['id'];
  }
  
  if(ob_get_length()) ob_clean();
  
   //http headers set to prevent browser from caching any resource
   header('Cache-Control: no-cache, no-store, must-revalidate');
   header('Pragma: no-cache');
   header('Content-Type: text/xml');
   
   echo $chat->getNewMessages($id);
?>