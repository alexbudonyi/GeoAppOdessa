<?php
	class Message {
		public static function SendMessage($subject, $message) {
			 $to = "alex.budonyi@gmail.com";
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
            //$headers[] = 'From: Birthday Reminder <birthday@example.com>';

            $m = mail($to, $subject, $message, implode("\r\n", $headers));
            if (!$m) {
                $errorMessage = error_get_last()['message'];
            }
		}
	}
?>