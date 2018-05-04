<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 06.02.2018
 * Time: 22:57
 */
require_once ($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/connection.php");

//Role::AddRole('new');
//Role::DeleteRole(3);

class Role
{
    protected $permissions;

    protected function __construct() {
        $this->permissions = array();
    }

    public function AddRole($name) {
        $db_connection = Db::getInstance();
        $insert_query = "INSERT INTO roles VALUES(DEFAULT, '$name')";
        $result = pg_query($db_connection, $insert_query);
        if ($result)
            return true;
        else
            return;
    }

    public function DeleteRole($id) {
        $db_connection = Db::getInstance();
        $delete_query = "DELETE FROM roles WHERE id = '$id'";
        $result = pg_query($db_connection, $delete_query);
        if ($result)
            return true;
        else
            return;
    }

    public static function AddRolePermission($role_id, $perm_id) {
        $db_connection = Db::getInstance();
        $insert_query = "INSERT INTO role_permissions(role_id, perm_id, id) VALUES('$role_id', '$perm_id', DEFAULT)";
        $result = pg_query($db_connection, $insert_query);
        if ($result)
            return true;
        else
            return;
    }

    // return a role object with associated permissions
    public static function getRolePerms($role_id) {
        $db_connection = Db::getInstance();

        $role = new Role();
        $sql = "SELECT t2.perm_desc FROM role_permissions as t1
                JOIN permissions as t2 ON t1.perm_id = t2.id
                WHERE t1.role_id = '$role_id'";
        $result = pg_query($db_connection, $sql);

        while($row = pg_fetch_row($result)) {
            $role->permissions[$row[0]] = true;
        }

        return $role;
    }

    // check if a permission is set
    public function hasPerm($permission) {
        return isset($this->permissions[$permission]);
    }


}

?>