<?php

require_once ($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/connection.php");
require_once($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/models/privileged_user.php");

//User::Register('login1', 'email1', 'pass1', 2);
//User::Login('system', '2222');
class User {
    public $id;

    public $login;
    public $password;

    public $email;

    public $created;
//  public $modified;

//$id, $login, $password, $email, $created
    public function __construct() {
        /*$this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
        $this->create_date = $created;*/
        //$this->last_modified = $modified;

    }

    public static function SendNewPass($email) {
        $db_connection = Db::getInstance();
        $select_query = "SELECT * FROM users where email = '$email' LIMIT 1";
        $result = pg_query($db_connection, $select_query);

        if (!empty(pg_fetch_row($result))) {
            while ($row = pg_fetch_row($result)) {
                $id = $row[0];
                $insert_query = "UPDATE users SET password = 'temporary1' WHERE id = '$id';";
                $result = pg_query($db_connection, $insert_query);

                self::SendNewPassword($email);

            }
            return true;
        } else return false;
    }


    public static function SendNewPassword($email) {
        $to = "somebody@example.com";
        $subject = "Відновити пароль";
        $txt = "Посилання на відновлення паролю https://?controller=establishment&action=update_password&email=' + $email ";
        $headers = "From: webmaster@example.com" . "\r\n" .
            "CC: somebodyelse@example.com";

        mail($to,$subject,$txt,$headers);
    }

    public static function GetUserID($email) {
        $db_connection = Db::getInstance();
        $select_query = "SELECT * FROM users where email = '$email' LIMIT 1";
        $result = pg_query($db_connection, $select_query);

        if (!empty($result)) {
            while ($row = pg_fetch_row($result)) {
                $id = $row[0];
            }
            return $id;
        } else
            return false;
    }

    public static function ChangePassword($id, $password) {
        $db_connection = Db::getInstance();
        $insert_query = "UPDATE users SET password = '$password' WHERE id = '$id';";
        $result = pg_query($db_connection, $insert_query);
        if ($result)
            return true;
        else
            return false;
    }

    public static function Register($login, $email, $password, $role_id) {

        $db_connection = Db::getInstance();
        //$encrypt_pass= md5($password);
        $create_date = date('Y-m-d');

        $is_dublicate_login = self::CheckLoginDublicate($login);
        $is_dublicate_email = self::CheckEmailDublicate($email);

        if ($is_dublicate_login == false && $is_dublicate_email == false)
        {
            $insert_query = "insert into users(id, login, password, email, create_date) values(DEFAULT, '$login', '$password', '$email', '$create_date')  RETURNING id";
            $result = pg_query($db_connection, $insert_query);
            $is_executed = User::CheckQueryError($result);
            if ($is_executed == true) {
                $last_insert_row = pg_fetch_row($result);
                $user_id = $last_insert_row[0];

                $is_setted = User::SetUserRole($user_id, $role_id);
                if ($is_setted == true)
                    return true;
                else
                    return false;
            }
        }
        else if ($is_dublicate_login == true && $is_dublicate_email == true)
            return "dublicate_login_email";
        else if ($is_dublicate_email == true)
            return "dublicate_email";
        else if ($is_dublicate_login == true)
            return "dublicate_login";

    }

    public static function Login($login, $password) {
        $db_connection = Db::getInstance();
        $select_query = "SELECT * FROM users where login = '$login' AND password = '$password' ";
        $result = pg_query($db_connection, $select_query);

        if (!empty($result))
        {
            while ($row = pg_fetch_row($result)) {
                $privUser = new PrivilegedUser();
                //id
                $privUser->id = $row[0];
                //login
                $privUser->login = $row[1];
                //password
                $privUser->password = $row[2];
                //email
                $privUser->email = $row[3];
                $privUser->getRoles($row[0]);
                return $privUser;
            }
        } else {
            return null;
        }
    }

    public static function SetUserRole($user_id, $role_id) {
        $db_connection = Db::getInstance();
        $insert_query = "INSERT INTO user_role(user_id, role_id, id) VALUES($user_id, $role_id, DEFAULT)";
        $result = pg_query($db_connection, $insert_query);
        if ($result)
            return true;
        else
            return false;
    }

    public static function CheckQueryError($query_res) {

        if (!$query_res) {
            pg_result_error($query_res);
            return false;
        } else return true;

    }

    public static function CheckLoginDublicate($login) {
        $db_connection = Db::getInstance();
        $select_query = "select * from users where login = '$login'";
        $result = pg_query($db_connection, $select_query);
        $rowNum = pg_num_rows($result);
        if ($rowNum != 0)
            return true;
        else
            return false;
    }

    public static function CheckEmailDublicate($email) {

        $db_connection = Db::getInstance();
        $select_query = "select * from users where email = '$email'";
        $result = pg_query($db_connection, $select_query);
        $rowNum = pg_num_rows($result);
        if ($rowNum != 0)
            return true;
        else
            return false;

    }


    /*public static function CreateUser($email, $pass, $login) {
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");
        //$db_connection = Db::getInstance();

        $email = pg_escape_string($email);
        $pass = pg_escape_string($pass);
        $login = pg_escape_string($login);
        $date = date('Y-m-d');

        //$encrypt_pass= md5($pass);
        $insert_query = "INSERT INTO users VALUES(DEFAULT,  '$login', '$pass', '$email', '$date')";
        pg_query($db_connection, $insert_query);
    }*/

}
?>