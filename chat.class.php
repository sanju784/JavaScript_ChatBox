<?php
  require_once('config.php');
  require_once('chat_error_handler');
  
  class chat
  {
    private $mysqli;
	
	function __construct() {
	  $this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	}
	
	function __destruct() {
	  $this->mysqli->close();
	}
	
	public function deleteAllMsg() {
	  query = 'TRUNCATE table chat';
	  $result = $this->mysqli->query($query);
	}
	
	public function postNewMsg($user_name, $message, $color) {
	  $user_name = $this->mysqli->real_escape_string($user_name);
	  $message = $this->mysqli->real_escape_string($message);
	  $color = $this->mysqli->real_escape_string($color);
	  $query = 'INSERT INTO chat (posted_on, user_name, message, color)'.
	            ' VALUES (
				NOW(), 
				"' . $user_name . '", 
				"' . $message . '", 
				"' . $color . '")';
	  $result = $this->mysqli->query($query);
	}
	
	public function getNewMessages($id = 0) {
	  $id = $this->mysqli->real_escape_string($id);
	  if($id > 0) {
	    $query = 'SELECT message_id, user_name, message, color, DATE_FORMAT(posted_on,"%H:%i:%s")
		AS posted_on FROM chat WHERE message_id > '. $id. ' ORDER BY message_id DESC';
	  } else {
	    $query = 'SELECT message_id, user_name, message, color, posted_on
		FROM (SELECT message_id, user_name, message, color, DATE_FORMAT(posted_on, "%H:%i:%s")
		AS posted_on FROM chat ORDER BY message_id DESC LIMIT 50) AS LAST50
		ORDER BY message_id ASC';
	  }
	  $result = $this->mysqli->query($query);
	  
	  // TODO instead of xml response send json response
	  $response = '<?xml version="1.0" encoding="UTF-8" standlone="yes"?>';
	  $response .= '<response>';
	  $response .= $this->isDatabaseCleared($id);
	  
	  if($result->num_rows) {
	    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
		  $id = $row['message_id']; 
		  $color = $row['color']
		  $username = $row['user_name'];
		  $time = $row['posted_on'];
		  $message = $row['message'];
		  $response .= '<id>' . $id . '</id>' .
		               '<color>' . $color . '</color>'.
					   '<name>' . $username . '</name>'.
					   '<time>' . $time . '</time>'.
					   '<message>' . $message . '</message>';
		}
		$result->close();
	  }
	  $response .= '</response>';
	  return $response;
	}
	
	private function isDatabaseCleared($id) {
	  if($id > 0) {
	    $check_clear = 'SELECT count(*) old FROM chat where message_id<=' . $id;
		$result = $this->mysqli->query($check_clear);
		$row = $result->fetch_array(MYSQLI_ASSOC);
		if($row['old'] == 0) {
		  return '<clear>true</clear>';
		}
	  }
	  return '<clear>false</clear>';
	}
  }
?>