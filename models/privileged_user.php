<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 06.02.2018
 * Time: 23:04
 */
require_once ($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/connection.php");
require_once ($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/models/user.php");
require_once ($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/models/role.php");

class PrivilegedUser extends User
{
    public $roles;

    public function __construct() {
        parent::__construct();
    }

    // override User method
    public static function getByUsername($username) {
        $db_connection = Db::getInstance();

        $sql = "SELECT * FROM users WHERE login = '$username'";
        $result = pg_query($db_connection, $sql);
        //$res = pg_fetch_all($result);

        while($row = pg_fetch_row($result)) {
            $privUser = new PrivilegedUser();
            $privUser->user_id = $result[0]["id"];
            $privUser->username = $username;
            $privUser->password = $result[0]["password"];
            $privUser->email_addr = $result[0]["email"];
            $privUser->getRoles();
            return $privUser;
        }

    }

    // populate roles with their associated permissions
    public function getRoles($user_id) {
        $db_connection = Db::getInstance();

        $this->roles = array();
        $sql = "SELECT u_r.role_id, r.name FROM user_role as u_r 
                JOIN roles as r ON u_r.role_id = r.id
                WHERE u_r.user_id = '$user_id' ";
        $result = pg_query($db_connection, $sql);
        //$row = pg_fetch_result($result, 0, 0);

        while($row = pg_fetch_row($result)) {
            //$r = $row[0];
            $this->roles[$row[1]] = Role::getRolePerms($row[0]);
        }

        //$this->roles[$row["role_name"]] =
        //while($row = pg_fetch_row($result)) {
          //$this->roles[$row["role_name"]] = Role::getRolePerms($row["role_id"]);
        //}


    }

    // check if user has a specific privilege
    public function hasPrivilege($perm) {
        foreach ($this->roles as $role) {
            if ($role->hasPerm($perm)) {
                return true;
            }
        }
        return false;
    }
}

?>