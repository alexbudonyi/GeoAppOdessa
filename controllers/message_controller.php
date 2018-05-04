<?php
require_once($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/models/message.php");

class message_controller {
    
    //---------------work with map
    public function index() {
      
      require_once("views/message/index.php");
    }

    public static function send_message($subject, $message) {
    	Message::SendMessage($subject, $message);
    	return "ОК";
    }
}

 if ((isset($_POST['send_message'])) && (isset($_POST['subject'])) && (isset($_POST['message']))) {    
        message_controller::send_message($_POST['subject'], $_POST['message']);
    }
?>