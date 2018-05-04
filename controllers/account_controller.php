<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 27.02.2018
 * Time: 21:56
 */

    require_once($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/models/user.php");
    //account_controller::login_userTEST('system', '2222');
    //account_controller::login_user('admin', '1111');
   // $val = $_SESSION["roles"]["user"];

    class account_controller
    {
 /*       public function test() {
            in_array("admin", $_SESSION["roles"], true);
    //echo "hello";
        }
*/

        public function recover_password()
        {

            require_once('views/account/recover_password.php');
        }


        public function register()
        {

            require_once('views/account/register.php');
        }

        public function register_user($userInfo)
        {
            //echo "gello2";
            $userInfo = json_decode($userInfo, true);
            foreach($userInfo as $itemInfo) {
                $login = $itemInfo['login'];
                $email = $itemInfo['email'];
                $password = $itemInfo['password'];
                $role_id = $itemInfo['role_id'];
            }
            //echo $password . " " . $email;
            echo User::Register($login, $email, $password, $role_id);
        }

        /*public static function login_userTEST($login, $password)
        {

            $privUser = User::Login($login, $password);
            //$privUser = User::Login('system', '2222');
            $_SESSION['login'] = $privUser->login;
            $_SESSION['user_id'] = $privUser->id;
        }*/

        public function login()
        {
            require_once('views/account/login.php');
        }

        //public static function login_user($login, $password)
        public static function login_user($userInfo)
        {
            $userInfo = json_decode($userInfo, true);

            foreach($userInfo as $itemInfo) {
                $login = $itemInfo['login'];
                $password = $itemInfo['password'];
            }

            session_abort();
            session_start();
            $privUser = User::Login($login, $password);
            if ($privUser == null)
                return false;

            //$privUser = User::Login('system', '2222');
            $_SESSION['logged_in'] = true;
            $_SESSION['login'] = $privUser->login;
            $_SESSION['user_id'] = $privUser->id;
            $_SESSION['roles'] = $privUser->roles;

            return true;
        }

        public static function logout()
        {
            session_start();
            unset($_SESSION);
            session_destroy();
            session_write_close();
            die;

            return true;
        }

        public static function sendNewPass($email)
        {
            return User::SendNewPass($email);
        }

        public static function update_password()
        {
            $id = User::GetUserID($_GET['email']);

            require_once("views/account/update_password.php");
        }

        public static function changePassword($userInfo)
        {
            $userInfo = json_decode($userInfo, true);

            foreach($userInfo as $itemInfo) {
                $id = $itemInfo['id'];
                $password = $itemInfo['password'];
            }

            return User::ChangePassword($id, $password);
        }

        public function error()
        {
            require_once('views/account/error.php');
        }
    }

    //-------робота із мапою
    if (isset($_POST['register'])) {
        //echo "hello";
        echo account_controller::register_user($_POST['register']);
    } elseif (isset($_POST['login'])) {
        //echo "hello";
        echo account_controller::login_user($_POST['login']);
    } elseif (isset($_POST['logout'])) {
        //echo "hello";
        echo account_controller::logout();
    } elseif (isset($_POST['sendNewPass'])) {
        //echo "hello";
        echo account_controller::sendNewPass($_POST['sendNewPass'] );
    } elseif (isset($_POST['changePassword'])) {
        //echo "hello";
        echo account_controller::changePassword($_POST['changePassword'] );
    }

?>