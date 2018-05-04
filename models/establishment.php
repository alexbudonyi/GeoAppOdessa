<?php
//require_once ("connection_n.php");
require_once ($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/connection.php");
require_once($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/models/crawler.php");

Establishment::GetUncheckPoiDetails(11095);
Establishment::GetUncheckPoiList();

class Establishment
{
    public $id;
    public $coordinates;
    public $name;
    public $cat_id;
    public $description;
    public $email;
    public $photo;
    public $url;

    public $create_date;
    public $user_id;

    public $directions;
    public $phones;
    public $addresses;


    public function __construct() {}

    /*public static function CreateUser($email, $pass, $login) {
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");
        //$db_connection = Db::getInstance();

        $email = pg_escape_string($email);
        $pass = pg_escape_string($pass);
        $login = pg_escape_string($login);
        $date = date('Y-m-d');

        $encrypt_pass= md5($pass);
        $insert_query = "INSERT INTO users VALUES(DEFAULT,  '$login', '$encrypt_pass', '$email', '$date')";
        pg_query($db_connection, $insert_query);
    }*/

    //get address from longitude, langitude
    public static function GetAddress($lat, $lon) {
        $key = "953c5128a60dd9";
        //$lat = 46.800059;
        //$lon = 30.124512;

        //$lat = 46.43753681543447;
        //$lon = 30.75517290364827;

        $key_e = urlencode($key);

        $url = "https://eu1.locationiq.org/v1/reverse.php?key=" . $key . "&lat=" . $lat . "&lon=" . $lon . "&format=json";
        $address = '';
        $json = @file_get_contents($url);
        if ($json == '[]' || $json == null)
            return $address;
        else {

            $obj = json_decode($json);
            //echo $address . $obj->address->state . " ";
            if ($obj->address != '') {
                if (isset($obj->address->state))
                    $address = $address . $obj->address->state . " ";

                if (isset($obj->address->city))
                    $address = $address . $obj->address->city . " ";

                if (isset($obj->address->town))
                    $address = $address . $obj->address->town . " ";

                if (isset($obj->address->road))
                    $address = $address . $obj->address->road . " ";

                if (isset($obj->address->house_number))
                    $address = $address . $obj->address->house_number . " ";
            }
        }

        return $address;
    }


    //----------------------------------------------------BEGIN. WORK WITH HANDBOOKS
    public static function AddCategory($name){

        //$db_connection = Db::getInstance();
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        $insert_query = "insert into categories values(DEFAULT, '$name', 1)";
        $result = pg_query($db_connection, $insert_query);
        $is_executed = Establishment::CheckQueryError($result);
        if ($is_executed == true)
            return true;
        else
            return false;
    }

    public static function AddDirection($name){
        //$db_connection = Db::getInstance();
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        $insert_query = "insert into directions values(DEFAULT, '$name')";
        $result = pg_query($db_connection, $insert_query);
        $is_executed = Establishment::CheckQueryError($result);
        if ($is_executed == true)
            return true;
        else
            return false;
    }

    public static function AddUncheckCategory($name){

        //$db_connection = Db::getInstance();
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        $insert_query = "insert into uncheck_cat_dir values(DEFAULT, '$name', 0)";
        $result = pg_query($db_connection, $insert_query);
        $is_executed = Establishment::CheckQueryError($result);
        if ($is_executed == true)
            return true;
        else
            return false;
    }

    public static function AddUncheckDirection($name){
        //$db_connection = Db::getInstance();
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        $insert_query = "insert into uncheck_cat_dir values(DEFAULT, '$name', 1)";
        $result = pg_query($db_connection, $insert_query);
        $is_executed = Establishment::CheckQueryError($result);
        if ($is_executed == true)
            return true;
        else
            return false;
    }

    //-----------------------------------CREATING POI. ADDING TO TABLES(them 4) ALL INFO ABOUT POI
    public static function CheckQueryError($query_res) {

        if (!$query_res) {
            pg_result_error($query_res);
            return false;
        } else return true;

    }

    public static function CheckOnClone($db_connection, $name) {
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");
        $select_query = "
        SELECT id, name FROM poi_catalogue WHERE LOWER('$name') = LOWER(name)
        UNION
        SELECT id, name FROM uncheck_poi_catalogue WHERE LOWER('$name') = LOWER(name)";

        $result = pg_query($db_connection, $select_query);
        $clones_arr = pg_fetch_row($result);

        if ($clones_arr == true)
            return true;
        else
            return false;
        /*while ($row = pg_fetch_row($result)) {
        }*/

    }

    public static function CreatePoi($coordinates, $name, $cat_id, $description, $url, $photo, $email, $directions,
                                     $addresses, $phones, $user_id, $create_date) {

        $db_connection = Db::getInstance();

        $name = pg_escape_string($name);
        $description = pg_escape_string($description);
        $email = pg_escape_string($email);
        $url = pg_escape_string($url);

        $coords = explode(",", $coordinates);
        $lon = $coords[0];
        $lat = $coords[1];

        $is_clone = Establishment::CheckOnClone($db_connection, $name);

        if ($is_clone == false) {
            $insert_query = "insert into poi_catalogue(id, coordinates, name, cat_id, description, url, photo, email, user_id, create_date) 
values (DEFAULT, public.ST_GeomFromText('POINT($lon $lat)', 4326), '$name', '$cat_id', '$description', '$url', null, '$email', '$user_id', null) RETURNING id";

            $result = pg_query($db_connection, $insert_query);
            $is_executed = Establishment::CheckQueryError($result);
            if ($is_executed == true) {
                $last_insert_row = pg_fetch_row($result);
                $id = $last_insert_row[0];
                //add poi directions
                Establishment::AddPoiDirection($db_connection, $id, $directions);
                //add poi phones
                Establishment::AddPoiPhone($db_connection, $id, $phones);
                Establishment::AddPoiAddresses($db_connection, $id, $addresses);
            } else
                return "Трапилась помилка =(";
        }

        return true;
    }

    public static function AddPoiDirection($db_connection, $poi_id, $directions) {
        foreach($directions as $dir)
        {
            //$data = "(DEFAULT,'".$poi_id.",'".$dir."')";
            $insert_query = "INSERT INTO poi_directions(id, poi_id, dir_id) VALUES (DEFAULT, '$poi_id', '$dir')";
            $result = pg_query($db_connection, $insert_query);
            if (!$result) {
                pg_result_error($result);
                echo "Напрям не додано!";
            } else echo "Напрям додано!";
        }
    }

    public static function AddPoiPhone($db_connection, $poi_id, $phones) {
        foreach($phones as $phone)
        {
            //$data = "(DEFAULT,'".$poi_id.",'".$dir."')";
            $insert_query = "INSERT INTO poi_phones(id, poi_id, number) VALUES (DEFAULT, '$poi_id', '$phone')";
            $result = pg_query($db_connection, $insert_query);
            if (!$result) {
                pg_result_error($result);
                return "Телефон не додано";
            } else "Телефон додано";
        }

        return true;
    }

    public static function AddPoiAddresses($db_connection, $poi_id, $addresses) {
        foreach($addresses as $address)
        {
            $insert_query = "INSERT INTO poi_addresses(id, poi_id, address) VALUES (DEFAULT, '$poi_id', '$address')";
            $result = pg_query($db_connection, $insert_query);
            if (!$result) {
                pg_result_error($result);
                return false;
            } else "Телефон додано";
        }

        return true;
    }
    //------------------------------END. ADDING POI INFO
    //--------------------------------------------------
    //-----------------------------------------------------END. SUMMERY

    //----------------------------------------------------
    //-----------------------------------------------------BEGIN. DELETE POI, DIRECTIONS, ADDRESSES, PHONES
    //-----------------------------BEGIN. DELETE POI


    public static function DeletePoi($poi_id) {
        $db_connection = Db::getInstance();
        $delete_query = "DELETE FROM poi_catalogue WHERE id = '$poi_id'";
        $result = pg_query($db_connection, $delete_query);
        if (!$result) {
            pg_result_error($result);
            return false;
        } else "Мітку видалено";
    }
    //-----------------------------END. DELETE POI

    //-----------------------------BEGIN. DELETE DIRECTION
    public static function DeletePoiDirection($dir_id) {
        $db_connection = Db::getInstance();
        $delete_query = "DELETE FROM poi_directions WHERE id = '$dir_id'";
        $result = pg_query($db_connection, $delete_query);
        if (!$result) {
            pg_result_error($result);
            return false;
        } else "Напрям точки видалено";
    }
    //-----------------------------END. DELETE DIRECTION

    //-----------------------------BEGIN. DELETE ADDRESS
    public static function DeletePoiAddress($addr_id) {
        $db_connection = Db::getInstance();
        $delete_query = "DELETE FROM poi_addresses WHERE id = '$addr_id'";
        $result = pg_query($db_connection, $delete_query);
        if (!$result) {
            pg_result_error($result);
            return false;
        } else "Адресу точки видалено";
    }
    //-----------------------------END. DELETE ADDRESS

    //-----------------------------BEGIN. DE0LETE PHONE
    public static function DeletePoiPhone($phone_id) {
        $db_connection = Db::getInstance();
        $delete_query = "DELETE FROM poi_phones WHERE id = '$phone_id'";
        $result = pg_query($db_connection, $delete_query);
        if (!$result) {
            pg_result_error($result);
            return false;
        } else "Телефон точки видалено";
    }
    //-----------------------------END. DELETE PHONE
    //-----------------------------BEGIN. DELETE CATEGORY
    public static function DeleteCategory($cat_id) {
        $db_connection = Db::getInstance();

        if ($cat_id == 8)
            return;
        else {
            $update_query = "UPDATE poi_catalogue SET cat_id = 8 WHERE cat_id = $cat_id";
            $result = pg_query($db_connection, $update_query);

            $delete_query = "DELETE FROM categories WHERE id = $cat_id";
            $result = pg_query($db_connection, $delete_query);
            if (!$result) {
                pg_result_error($result);
                return false;
            } else "Категорію видалено";
        }
    }
    //-----------------------------END. DELETE CATEGORY

    //-----------------------------BEGIN. DELETE DIRECTION
    public static function DeleteDirection($dir_id) {
        $db_connection = Db::getInstance();

        if ($dir_id == 6)
            return;
        else {
            $update_query = "DELETE FROM poi_directions WHERE dir_id = $dir_id ";
            $result = pg_query($db_connection, $update_query);

            $delete_query = "DELETE FROM directions WHERE id = $dir_id";
            $result = pg_query($db_connection, $delete_query);
            if (!$result) {
                pg_result_error($result);
                return false;
            } else "Напрям видалено";
        }
    }
    //-----------------------------END. DELETE DIRECTION

    //-----------------------------------------------------END. DELETE POI, DIRECTIONS, ADDRESSES, PHONES

    //-----------------------------------------------------BEGIN. SUMMERY
    //-----------------------------BEGIN. GET POI SUMMERY
    //-----------------------------BEGIN. GET POI SUMMERY
    public static function GetPoiList() {

        $db_connection = Db::getInstance();

        /*$select_query =
            "SELECT poi_catalogue.id, public.ST_AsGeoJSON(public.ST_ASTEXT(public.ST_TRANSFORM(poi_catalogue.coordinates, 4326))),
poi_catalogue.name, poi_catalogue.user_id, poi_catalogue.cat_id,
categories.id, categories.ukr_name, users.id, users.login
            FROM poi_catalogue
              LEFT JOIN categories ON poi_catalogue.cat_id = categories.id
              LEFT JOIN users ON poi_catalogue.user_id = users.id";
*/
        /*      $select_query = "SELECT poi_catalogue.id, public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)),
       poi_catalogue.name, poi_catalogue.user_id, poi_catalogue.cat_id, poi_catalogue.url,
       categories.id, categories.ukr_name, users.id, users.login
                   FROM poi_catalogue
                     LEFT JOIN categories ON poi_catalogue.cat_id = categories.id
                     LEFT JOIN users ON poi_catalogue.user_id = users.id";
                     */
        $select_query = "SELECT poi_catalogue.id, public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)),
 poi_catalogue.name, poi_catalogue.user_id, poi_catalogue.url,  
 users.id, users.login 
             FROM poi_catalogue
               
               LEFT JOIN users ON poi_catalogue.user_id = users.id";

        $result = pg_query($db_connection, $select_query);
        $is_executed = Establishment::CheckQueryError($result);

        $list = array();

        if ($is_executed == true)
        {
            while ($row = pg_fetch_row($result)) {
                $id = $row[0];
                $coordinates = $row[1];
                $name = $row[2];
                //$user_id = $row[3];
                $url = $row[4];

                //$cat_name = $row[6];
                $user_id = $row[5];
                $user_login = $row[6];

                $addresses = Establishment::GetPoiAddress($id);

                $list[] = array(
                    'id' => $id,
                    //'coordinates' => json_decode($coordinates, true),
                    'name' => $name,
                    'url' => $url,
                    // 'cat_id' => $cat_id,
                    //'cat_name' => $cat_name,
                    // 'user_id' => $user_id,
                    'user_login' => $user_login
                    // 'addresses' => $addresses
                );
            }
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($list),
                "iTotalDisplayRecords" => count($list),
                "aaData"=>$list);
        }

        return $results;
    }
    //-----------------------------END. GET POI SUMMERY

    public static function GetFilteredList($cats_ids, $dirs_ids) {
        $db_connection = Db::getInstance();

        if ( ($cats_ids != "") && ($dirs_ids != "") ) {
            //$cats_ids = json_decode(stripslashes($cats_ids));
            //$dirs_ids_str = implode("', '", $dirs_ids);

            $sql = "SELECT DISTINCT on (poi_catalogue.id) public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)) AS geojson, 
poi_catalogue.id, poi_catalogue.name, poi_catalogue.url, users.login, users.id, poi_catalogue.user_id, poi_catalogue.cat_id, poi_directions.poi_id, poi_directions.dir_id 
                FROM poi_catalogue, poi_directions, users 
                WHERE poi_catalogue.id = poi_directions.poi_id 
                  AND poi_directions.dir_id in ($dirs_ids) 
                  AND poi_catalogue.cat_id in ($cats_ids)
                  AND poi_catalogue.user_id = users.id
                  AND poi_catalogue.coordinates IS NOT NULL ORDER BY poi_catalogue.id ";
        }
        elseif ($cats_ids != "" && $dirs_ids == "") {
            //$cat_id = json_decode(stripslashes($cat_id));

            $sql = "SELECT DISTINCT on (poi_catalogue.id) public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)) AS geojson, poi_catalogue.id,
poi_catalogue.name, poi_catalogue.url, users.login, users.id, poi_catalogue.user_id, poi_catalogue.cat_id
                FROM poi_catalogue, users
                WHERE poi_catalogue.cat_id in ($cats_ids)
                  AND poi_catalogue.user_id = users.id
                  AND poi_catalogue.coordinates IS NOT NULL ORDER BY poi_catalogue.id ";
        }
        elseif ($cats_ids == null && $dirs_ids != null) {
            //$dirs_ids_arr = json_decode(stripslashes($dirs_ids));
            //$dirs_ids_str = implode("', '", $dirs_ids);

            $sql = "SELECT DISTINCT on (poi_catalogue.id) public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)) AS geojson, poi_catalogue.id,
poi_catalogue.name, poi_catalogue.url, users.login, users.id, poi_catalogue.user_id, poi_directions.poi_id, poi_directions.dir_id 
                FROM poi_catalogue, poi_directions, users 
                WHERE poi_catalogue.id = poi_directions.poi_id 
                  AND poi_directions.dir_id in ($dirs_ids)  
                  AND poi_catalogue.user_id = users.id
                  AND poi_catalogue.coordinates IS NOT NULL ORDER BY poi_catalogue.id ";
        }
        else
            $sql = "SELECT public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)) AS geojson, poi_catalogue.id, poi_catalogue.name, poi_catalogue.url, users.login, users.id, poi_catalogue.user_id
 FROM poi_catalogue, users WHERE user_id = users.id AND coordinates is not null ";

        $result = pg_query($db_connection, $sql);
        $is_executed = Establishment::CheckQueryError($result);

        $list = array();

        if ($is_executed == true)
        {
            while ($row = pg_fetch_row($result)) {
                $id = $row[1];
                //$coordinates = $row[1];
                $name = $row[2];
                //$user_id = $row[3];
                $url = $row[3];

                //$cat_name = $row[6];
                //$user_id = $row[5];
                $user_login = $row[4];

                //$addresses = Establishment::GetPoiAddress($id);

                $list[] = array(
                    'id' => $id,
                    //'coordinates' => json_decode($coordinates, true),
                    'name' => $name,
                    'url' => $url,
                    // 'cat_id' => $cat_id,
                    //'cat_name' => $cat_name,
                    // 'user_id' => $user_id,
                    'user_login' => $user_login
                    // 'addresses' => $addresses
                );
            }
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($list),
                "iTotalDisplayRecords" => count($list),
                "aaData"=>$list);
        }

        return $results;
    }

    //-----------------------------BEGIN. UPDATE POI
    public static function EditPoi($id, $coordinates, $name, $cat_id, $description, $url, $photo, $email, $directions,
                                   $addresses, $phones, $user_id) {

        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        if ($name != '') {
            $name = pg_escape_string($name);
            $insert_query = "UPDATE poi_catalogue SET name = '$name' WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);

        }
        if ($cat_id != '') {
            $cat_id = pg_escape_string($cat_id);
            $insert_query = "UPDATE poi_catalogue SET cat_id = '$cat_id' WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);

        }
        if ($description != '') {
            $description = pg_escape_string($description);
            $insert_query = "UPDATE poi_catalogue SET description = '$description' WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);
        } else if ($description == '') {
            $delete_query = "UPDATE poi_catalogue SET description = '' WHERE id = '$id'";
            $delete_result = pg_query($db_connection, $delete_query);
        }

        if ($email != null) {
            $email = pg_escape_string($email);
            $insert_query = "UPDATE poi_catalogue SET email = '$email' WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);
        }else if ($email == '') {
            $delete_query = "UPDATE poi_catalogue SET email = '' WHERE id = '$id'";
            $delete_result = pg_query($db_connection, $delete_query);
        }
        if ($url != null) {
            $url = pg_escape_string($url);
            $insert_query = "UPDATE poi_catalogue SET url = '$url' WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);
        }else if ($url == '') {
            $delete_query = "UPDATE poi_catalogue SET url = '' WHERE id = '$id'";
            $delete_result = pg_query($db_connection, $delete_query);
        }

        if ($phones != null) {
            $delete_query = "DELETE FROM poi_phones WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $delete_query);
            //echo $phones[0];
            foreach ($phones as $phone) {
                //echo $phone;
                $phone = pg_escape_string($phone);
                $insert_query = "INSERT INTO poi_phones VALUES (DEFAULT, '$id', '$phone')";
                $insert_result = pg_query($db_connection, $insert_query);
            }
        } else if ($phones == ''){
            $insert_query = "DELETE FROM poi_phones WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $insert_query);
        }

        if ($addresses != null) {
            $delete_query = "DELETE FROM poi_addresses WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $delete_query);

            foreach ($addresses as $address) {
                $address = pg_escape_string($address);
                    $insert_query = "INSERT INTO poi_addresses VALUES (DEFAULT, '$id', '$address')";
                    $insert_result = pg_query($db_connection, $insert_query);

            }
        } else if ($addresses == '')
        {
            $insert_query = "DELETE FROM poi_addresses WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $insert_query);
        }

        if ($directions != null) {

            $insert_query = "DELETE FROM poi_directions WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $insert_query);

            foreach ($directions as $dir) {
                //echo $dir;
                $dir = pg_escape_string($dir);
                    $insert_query = "INSERT INTO poi_directions VALUES (DEFAULT, '$id', '$dir')";
                    $insert_result = pg_query($db_connection, $insert_query);

            }
        } else if ($directions == '') {
            $insert_query = "DELETE FROM poi_directions WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $insert_query);
        }
        if ($coordinates != null) {
            echo $coordinates;
            $coords = explode(",", $coordinates);
            $lon = $coords[1];
            $lat = $coords[0];

            $address = Establishment::GetAddress($lon, $lat);
            //echo $address;
            //echo $id;
            $address = pg_escape_string($address);
            //echo $address;

            $insert_qr_addr = "INSERT INTO poi_addresses(id, poi_id, address) VALUES(DEFAULT, '$id', '$address') ";
            $result = pg_query($db_connection, $insert_qr_addr);

            $insert_query = "UPDATE poi_catalogue SET coordinates = public. ST_SetSRID(public.ST_MakePoint($lon, $lat), 4326) WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);
        }

        /* elseif ($addresses != null) {
                    foreach ($addresses as $address) {
                        $address = pg_escape_string($address);
                        $insert_query = "UPDATE poi_phones SET address = '$address' WHERE id = '$id'";
                        $result = pg_query($db_connection, $insert_query);
                    }
                }*/

}

    public static function EditUncheckPoi($id, $coordinates, $name, $cat_id, $description, $url, $photo, $email, $directions,
                                   $addresses, $phones, $user_id) {

        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        if ($name != '') {
            $name = pg_escape_string($name);
            $insert_query = "UPDATE uncheck_poi_catalogue SET name = '$name' WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);

        }
        if ($cat_id != '') {
            $cat_id = pg_escape_string($cat_id);
            $insert_query = "UPDATE uncheck_poi_catalogue SET cat_id = '$cat_id' WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);

        }
        if ($description != '') {
            $description = pg_escape_string($description);
            $insert_query = "UPDATE uncheck_poi_catalogue SET description = '$description' WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);
        } else if ($description == '') {
            $delete_query = "UPDATE uncheck_poi_catalogue SET description = '' WHERE id = '$id'";
            $delete_result = pg_query($db_connection, $delete_query);
        }

        if ($email != null) {
            $email = pg_escape_string($email);
            $insert_query = "UPDATE uncheck_poi_catalogue SET email = '$email' WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);
        }else if ($email == '') {
            $delete_query = "UPDATE uncheck_poi_catalogue SET email = '' WHERE id = '$id'";
            $delete_result = pg_query($db_connection, $delete_query);
        }
        if ($url != null) {
            $url = pg_escape_string($url);
            $insert_query = "UPDATE uncheck_poi_catalogue SET url = '$url' WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);
        }else if ($url == '') {
            $delete_query = "UPDATE uncheck_poi_catalogue SET url = '' WHERE id = '$id'";
            $delete_result = pg_query($db_connection, $delete_query);
        }

        if ($phones != null) {
            $delete_query = "DELETE FROM uncheck_poi_phones WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $delete_query);
            //echo $phones[0];
            foreach ($phones as $phone) {
                //echo $phone;
                $phone = pg_escape_string($phone);
                $insert_query = "INSERT INTO uncheck_poi_phones VALUES (DEFAULT, '$id', '$phone')";
                $insert_result = pg_query($db_connection, $insert_query);
            }
        } else if ($phones == ''){
            $insert_query = "DELETE FROM uncheck_poi_phones WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $insert_query);
        }

        if ($addresses != null) {
            $delete_query = "DELETE FROM uncheck_poi_addresses WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $delete_query);

            foreach ($addresses as $address) {
                $address = pg_escape_string($address);
                $insert_query = "INSERT INTO uncheck_poi_addresses VALUES (DEFAULT, '$id', '$address')";
                $insert_result = pg_query($db_connection, $insert_query);

            }
        } else if ($addresses == '')
        {
            $insert_query = "DELETE FROM uncheck_poi_addresses WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $insert_query);
        }

        if ($directions != null) {

            $insert_query = "DELETE FROM uncheck_poi_directions WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $insert_query);

            foreach ($directions as $dir) {
                //echo $dir;
                $dir = pg_escape_string($dir);
                $insert_query = "INSERT INTO uncheck_poi_directions VALUES (DEFAULT, '$id', '$dir')";
                $insert_result = pg_query($db_connection, $insert_query);

            }
        } else if ($directions == '') {
            $insert_query = "DELETE FROM uncheck_poi_directions WHERE poi_id = '$id'";
            $delete_result = pg_query($db_connection, $insert_query);
        }
        if ($coordinates != null) {
            echo $coordinates;
            $coords = explode(",", $coordinates);
            $lon = $coords[1];
            $lat = $coords[0];

            $address = Establishment::GetAddress($lon, $lat);
            //echo $address;
            //echo $id;
            $address = pg_escape_string($address);
            //echo $address;

            $insert_qr_addr = "INSERT INTO uncheck_poi_addresses(id, poi_id, address) VALUES(DEFAULT, '$id', '$address') ";
            $result = pg_query($db_connection, $insert_qr_addr);

            $insert_query = "UPDATE uncheck_poi_catalogue SET coordinates = public. ST_SetSRID(public.ST_MakePoint($lon, $lat), 4326) WHERE id = '$id'";
            $result = pg_query($db_connection, $insert_query);
        }

        /* elseif ($addresses != null) {
                    foreach ($addresses as $address) {
                        $address = pg_escape_string($address);
                        $insert_query = "UPDATE poi_phones SET address = '$address' WHERE id = '$id'";
                        $result = pg_query($db_connection, $insert_query);
                    }
                }*/

    }

    public static function EditPhone($id, $phone) {
        $db_connection = Db::getInstance();
        $insert_query = "UPDATE poi_phones SET number = '$phone' WHERE id = '$id'";
        $result = pg_query($db_connection, $insert_query);
    }

    public static function EditAddress($id, $address) {
        $db_connection = Db::getInstance();
        $insert_query = "UPDATE poi_addresses SET address = '$address' WHERE id = '$id'";
        $result = pg_query($db_connection, $insert_query);
    }

    /*public static function  EditDirectionPoi($id, $direction) {
        $db_connection = Db::getInstance();
        $insert_query = "UPDATE poi_directions SET direction = '$direction' WHERE id = '$id'";
        $result = pg_query($db_connection, $insert_query);
    }*/

    //----------------edit catalogues of categories, directions
    public static function EditCategory($id, $category) {
        //echo "hello3";
        $db_connection = Db::getInstance();
        $insert_query = "UPDATE categories SET ukr_name = '$category' WHERE id = '$id'";
        $result = pg_query($db_connection, $insert_query);
    }

    //edit catalogue direcion. not POI
    public static function EditDirection($id, $direction) {
        //echo "hello3";
        $db_connection = Db::getInstance();
        $insert_query = "UPDATE directions SET ukr_name = '$direction' WHERE id = '$id'";
        $result = pg_query($db_connection, $insert_query);
    }
    //-----------------------------END. UPDATE POI

    //-----------------------------BEGIN. GET POI DETAILS
    public static function GetPoiDetails($id) {
        $db_connection = Db::getInstance();
        $select_query = "select pc.id, public.ST_AsGeoJSON(public.ST_ASTEXT(pc.coordinates)),
 pc.name, pc.description, pc.cat_id, categories.id, categories.ukr_name, 
 pc.url, pc.create_date,
 user_id, users.id, users.login, pc.photo
 FROM poi_catalogue as pc
 	LEFT JOIN users ON user_id = users.id
 	LEFT JOIN categories ON cat_id = categories.id
 WHERE pc.id = $id";

        $result = pg_query($db_connection, $select_query);
        $is_executed = Establishment::CheckQueryError($result);
        $list = array();

        if ($is_executed == true)
        {
            while ($row = pg_fetch_row($result)) {
                $id = $row[0];
                $coordinates = $row[1];
                $name = $row[2];
                $description = $row[3];
                $cat_id = $row[4];
                //categories.id = poi_catalogue.cat_id because of poi_catalogue.cat_id - foreign key, so do not put it to var
                $cat_name = $row[6];
                $url = $row[7];
                $create_date = $row[8];
                $user_login = $row[11];
                $photoDecoded = pg_unescape_bytea($row[12]);
                $photoEncoded = "data:image/jpeg;base64," . base64_encode($photoDecoded);
                $photo = $photoEncoded;

                $directions = Establishment::GetPoiDirections($id);
                $phones = Establishment::GetPoiPhones($id);
                $addresses = Establishment::GetPoiAddress($id);

                $poiDetails = array(
                    'id' => $id,
                    'coordinates' => $coordinates,
                    'name' => $name,
                    'description' => $description,
                    'cat_id' => $cat_id,
                    'cat_name' => $cat_name,
                    'url' => $url,
                    'user_login' => $user_login,
                    'create_date' => $create_date,
                    'photo' => $photo,
                    'directions' => $directions,
                    'phones' => $phones,
                    'addresses' => $addresses
                );

                array_push($list, $poiDetails);

            }
        }
        return $list;

    }

    public static function GetUncheckPoiDetails($id) {
        $db_connection = Db::getInstance();
        $select_query = "select upc.id, public.ST_AsGeoJSON(public.ST_ASTEXT(upc.coordinates)),
 upc.name, upc.description, upc.cat_id, categories.id, categories.ukr_name, 
 upc.url, upc.create_date,
 user_id, users.id, users.login, upc.photo 
 FROM uncheck_poi_catalogue as upc
 	LEFT JOIN users ON user_id = users.id
 	LEFT JOIN categories ON cat_id = categories.id
 WHERE upc.id = $id";

        $result = pg_query($db_connection, $select_query);
        $is_executed = Establishment::CheckQueryError($result);
        $list = array();

        if ($is_executed == true)
        {
            while ($row = pg_fetch_row($result)) {
                $id = $row[0];
                $coordinates = $row[1];
                $name = $row[2];
                $description = $row[3];
                $cat_id = $row[4];
                //categories.id = poi_catalogue.cat_id because of poi_catalogue.cat_id - foreign key, so do not put it to var
                $cat_name = $row[6];
                $url = $row[7];
                $create_date = $row[8];
                $user_login = $row[11];
                $photoDecoded = pg_unescape_bytea($row[12]);
                $photoEncoded = "data:image/jpeg;base64," . base64_encode($photoDecoded);
                $photo = $photoEncoded;

                $directions = Establishment::GetUncheckPoiDirections($id);
                $phones = Establishment::GetUncheckPoiPhones($id);
                $addresses = Establishment::GetUncheckPoiAddress($id);

                $poiDetails = array(
                    'id' => $id,
                    'coordinates' => $coordinates,
                    'name' => $name,
                    'description' => $description,
                    'cat_id' => $cat_id,
                    'cat_name' => $cat_name,
                    'url' => $url,
                    'user_login' => $user_login,
                    'create_date' => $create_date,
                    'photo' => $photo,
                    'directions' => $directions,
                    'phones' => $phones,
                    'addresses' => $addresses
                );

                array_push($list, $poiDetails);

            }
        }
        return $list;

    }

    public static function GetPoiDirections($poi_id) {
        $db_connection = Db::getInstance();
        $select_query = "select poi_directions.poi_id, poi_directions.dir_id, directions.id, directions.ukr_name 
 From poi_directions, directions Where poi_directions.dir_id = directions.id AND poi_directions.poi_id = $poi_id";
        $result = pg_query($db_connection, $select_query);
        $directions = array();
        while ($row = pg_fetch_row($result)) {
            $directions[] = $row[3];
        }

        return $directions;
    }

    public static function GetUncheckCatDirList() {
        $db_connection = Db::getInstance();
        $select_query = "select id, name, CASE data_type
      WHEN 0 THEN 'категорія'
      WHEN '1' THEN 'напрям'
      ELSE 'невідомо'
   END AS data_type, data_type as data_type_id from uncheck_cat_dir";
        $result = pg_query($db_connection, $select_query);
        $list = [];

        while($row = pg_fetch_row($result)) {

            $item = array (
                'id' => $row[0],
                'name' => $row[1],
                'data_type' => $row[2],
                'data_type_id' => $row[3],
            );
            array_push($list, $item);
        }

        return $list;
    }

    public static function GetUncheckPoiDirections($poi_id) {
        $db_connection = Db::getInstance();
        $select_query = "select uncheck_poi_directions.poi_id, uncheck_poi_directions.dir_id, directions.id, directions.ukr_name 
 From uncheck_poi_directions, directions Where uncheck_poi_directions.dir_id = directions.id AND uncheck_poi_directions.poi_id = $poi_id";
        $result = pg_query($db_connection, $select_query);
        $directions = array();
        while ($row = pg_fetch_row($result)) {
            $directions[] = $row[3];
        }

        return $directions;
    }
    public static function GetPoiPhones($id) {
        $db_connection = Db::getInstance();
        $select_query = "select poi_id, number from poi_phones where poi_id = $id";
        $result = pg_query($db_connection, $select_query);
        $phones = array();
        while ($row = pg_fetch_row($result)) {
            $phones[] = $row[1];
        }

        return $phones;
    }

    public static function GetUncheckPoiPhones($id) {
        $db_connection = Db::getInstance();
        $select_query = "select poi_id, number from uncheck_poi_phones where poi_id = $id";
        $result = pg_query($db_connection, $select_query);
        $phones = array();
        while ($row = pg_fetch_row($result)) {
            $phones[] = $row[1];
        }

        return $phones;
    }

    public static function GetPoiAddress($poi_id) {
        $db_connection = Db::getInstance();
        $select_query = "select poi_id, address from poi_addresses where poi_id = $poi_id";
        $result = pg_query($db_connection, $select_query);
        $addresses = array();
        while ($row = pg_fetch_row($result)) {
            $addresses[] = $row[1];
        }

        return $addresses;
    }

    public static function GetUncheckPoiAddress($poi_id) {
        $db_connection = Db::getInstance();
        $select_query = "select poi_id, address from uncheck_poi_addresses where poi_id = $poi_id";
        $result = pg_query($db_connection, $select_query);
        $addresses = array();
        while ($row = pg_fetch_row($result)) {
            $addresses[] = $row[1];
        }

        return $addresses;
    }

    public static function GetUserPoi($user_id) {
        $db_connection = Db::getInstance();
        $select_query = "select poi_catalogue.id, public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)),
 poi_catalogue.name, poi_catalogue.cat_id, poi_catalogue.user_id, categories.id, categories.ukr_name, poi_catalogue.create_date 
 FROM poi_catalogue, categories
 WHERE poi_catalogue.cat_id = categories.id AND poi_catalogue.user_id = '$user_id' ";

        $result = pg_query($db_connection, $select_query);
        $is_executed = Establishment::CheckQueryError($result);

        $list = array(
            'poi'  => array()
        );

        if ($is_executed == true) {
            while ($row = pg_fetch_row($result)) {
                $id = $row[0];
                $coordinates = $row[1];
                $name = $row[2];
                $cat_id = $row[3];
                //categories.id = poi_catalogue.cat_id because of poi_catalogue.cat_id - foreign key, so do not put it to var
                $cat_name = $row[6];
                $create_date = $row[7];

                $addresses = Establishment::GetPoiAddress($id);
                $poiSum = array(
                    'id' => $id,
                    'coordinates' => json_decode($coordinates, true),
                    'name' => $name,
                    'cat_id' => $cat_id,
                    'cat_name' => $cat_name,
                    'create_date' => $create_date,
                    'addresses' => $addresses
                );
                # Add feature arrays to feature collection array
                array_push($list['poi'], $poiSum);
            }
        }

        return $list;
    }

    public static function GetCategoriesList() {
        $db_connection = Db::getInstance();
        $select_query = "select * from categories";

        $result = pg_query($db_connection, $select_query);
        $is_executed = Establishment::CheckQueryError($result);

        $list = [];

        while($row = pg_fetch_row($result)) {

            $item = array (
                'id' => $row[0],
                'name' => $row[1]
            );
            array_push($list, $item);
        }

        return $list;
    }

    public static function GetDirectionsList() {
        $db_connection = Db::getInstance();
        $select_query = "select * from directions ";

        $result = pg_query($db_connection, $select_query);
        $is_executed = Establishment::CheckQueryError($result);

        $list = [];

        while($row = pg_fetch_row($result)) {

            $item = array (
                'id' => $row[0],
                'name' => $row[1]
            );
            array_push($list, $item);
        }

        return $list;
    }

    public static function FindPoi($name) {
        $db_connection = Db::getInstance();
        $select_query = "SELECT poi_catalogue.id, poi_catalogue.name, poi_catalogue.user_id, poi_catalogue.cat_id, 
 categories.id, categories.ukr_name, users.id, users.login 
             FROM poi_catalogue
               LEFT JOIN categories ON poi_catalogue.cat_id = categories.id
               LEFT JOIN users ON poi_catalogue.user_id = users.id 
               WHERE position('$name' in poi_catalogue.name ) > 0";

        $result = pg_query($db_connection, $select_query);
        $is_executed = Establishment::CheckQueryError($result);
        $list = [];
        if ($is_executed == true) {
            while ($row = pg_fetch_row($result))
            {
                $id = $row[0];
                $name = $row[1];
                $cat_name = $row[5];
                $user_login = $row[7];
                $addresses = Establishment::GetPoiAddress($id);

                $poiSum = array(
                    'id' => $id,
                    'name' => $name,
                    'cat_id' => $cat_name,
                    'user_login' => $user_login,
                    'addresses' => $addresses

                );

                array_push($list, $poiSum);

            }
        }

        return $list;
    }

    //-----------------------------END. GET POI DETAILS

    //-----------------------------BEGIN. ADD UNCHECK POINT
    public static function AddUncheckPoi($coordinates, $name, $cat_id, $description, $url, $photo, $email, $directions,
                                         $adresses, $phones, $user_id, $create_date) {

        //$db_connection = Db::getInstance();
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        $name = pg_escape_string($name);
        $description = pg_escape_string($description);
        $email = pg_escape_string($email);
        $url = pg_escape_string($url);

        $is_clone = Establishment::CheckOnClone($db_connection, $name);
        if ($is_clone == true) {
            return;
        } else {
            if ($coordinates == "") {
                $insert_query = "insert into uncheck_poi_catalogue(id, coordinates, name, cat_id, description, url, photo, email, user_id, create_date) 
values (DEFAULT, DEFAULT, '$name', '$cat_id', '$description', '$url', '$photo', '$email', '$user_id', DEFAULT) RETURNING id";
            } else {
                $coords = explode(",", $coordinates);
                $lon = $coords[0];
                $lat = $coords[1];

                $insert_query = "INSERT INTO uncheck_poi_catalogue(id, coordinates, cat_id, url, name, description, photo, email, user_id, create_date)
  VALUES (DEFAULT, public. ST_SetSRID(public.ST_MakePoint($lon, $lat), 4326), 
 '$cat_id', '$url', '$name', '$description', '$photo', '', '$user_id', DEFAULT) RETURNING id";
            }

            $result = pg_query($db_connection, $insert_query);
            $is_executed = Establishment::CheckQueryError($result);
            if ($is_executed == true) {
                $last_insert_row = pg_fetch_row($result);
                $id = $last_insert_row[0];

                //add poi directions
                if ($directions != '')
                    Establishment::AddUncheckPoiDir($id, $directions);
                //add poi phones
                if ($phones != '')
                    Establishment::AddUncheckPoiPhone($id, $phones);
                if ($adresses != '')
                    Establishment::AddUncheckPoiAddr($id, $adresses);
            } else return "Трапилась помилка =(";
        }

        return true;
    }

    public static function AddUncheckPoiDir($poi_id, $directions) {
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        foreach($directions as $dir) {
            $insert_query = "insert into uncheck_poi_directions(id, poi_id, dir_id) values(DEFAULT, '$poi_id', '$dir')";
            $result = pg_query($db_connection, $insert_query);
            $is_executed = Establishment::CheckQueryError($result);
            if ($is_executed != true)
                /*echo "Напрям додано до не зареєстрованих";
            else*/
                echo "Сталася помилка =(";
        }


    }

    public static function AddUncheckPoiPhone($id, $phone)
    {
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");
        if (!(is_array($phone)) ) {
            $insert_query = "insert into uncheck_poi_phones(id, poi_id, number) values(DEFAULT, '$id', '$phone')";
            $result = pg_query($db_connection, $insert_query);
            $is_executed = Establishment::CheckQueryError($result);
            if ($is_executed == true)
                return;
            else
                echo "Сталася помилка =(";
        }
        foreach ($phone as $ph) {
            $insert_query = "insert into uncheck_poi_phones values(DEFAULT, '$id', '$ph')";
            $result = pg_query($db_connection, $insert_query);
            $is_executed = Establishment::CheckQueryError($result);
            if ($is_executed != true)
                /*echo "Телефон додано до не зареєстрованих";
            else*/
                echo "Сталася помилка =(";
        }
    }

    public static function AddUncheckPoiAddr($id, $address)
    {
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");
        if (!(is_array($address)) ) {
            $insert_query = "insert into uncheck_poi_addresses(id, poi_id, address) values(DEFAULT, '$id', '$address')";
            $result = pg_query($db_connection, $insert_query);
            $is_executed = Establishment::CheckQueryError($result);
            if ($is_executed == true)
                return;
            else
                echo "Сталася помилка =(";
        }
        elseif (is_array($address)) {
            foreach ($address as $addr) {
                $addr = pg_escape_string($addr);
                $insert_query = "insert into uncheck_poi_addresses(id, poi_id, address) values(DEFAULT, '$id', '$addr')";
                $result = pg_query($db_connection, $insert_query);
                $is_executed = Establishment::CheckQueryError($result);
                if ($is_executed == true)
                    return;
                else
                    echo "Сталася помилка =(";
            }
        } else return;

    }


    public static function DeleteUncheckPoi($poi_id) {
        $db_connection = Db::getInstance();
        $delete_query = "DELETE FROM uncheck_poi_catalogue WHERE id = '$poi_id'";
        $result = pg_query($db_connection, $delete_query);
        if (!$result) {
            pg_result_error($result);
            return false;
        } else
            echo "Мітку видалено із не зареєстрованих";
    }

    public static function DeleteUncheckCatDir($id) {
        $db_connection = Db::getInstance();
        $delete_query = "DELETE FROM uncheck_cat_dir WHERE id = '$id'";
        $result = pg_query($db_connection, $delete_query);
        if (!$result) {
            pg_result_error($result);
            return false;
        } else
            echo "Мітку видалено із не зареєстрованих";
    }

    //-----------------------------END. ADD UNCHECK POINT
    public static function AcceptUncheckCat($id) {
        $db_connection = Db::getInstance();

        $select_query = "INSERT INTO categories(ukr_name)
SELECT name FROM uncheck_cat_dir WHERE id = '$id' ";
        $result = pg_query($db_connection, $select_query);

        $delete_query = "DELETE FROM uncheck_cat_dir WHERE id = '$id' ";
        $result = pg_query($db_connection, $delete_query);

    }

    public static function AcceptUncheckDir($id) {
        $db_connection = Db::getInstance();

        $select_query = "INSERT INTO directions(ukr_name)
SELECT name FROM uncheck_cat_dir WHERE id = '$id' ";
        $result = pg_query($db_connection, $select_query);

        $delete_query = "DELETE FROM uncheck_cat_dir WHERE id = '$id' ";
        $result = pg_query($db_connection, $delete_query);

    }
    //-----------------------------BEGIN. ACCEPT POI
    public static function AcceptUncheckPoi($id) {
        $db_connection = Db::getInstance();

        $select_query = "SELECT * FROM uncheck_poi_catalogue WHERE id = '$id'";
        $result = pg_query($db_connection, $select_query);
        $row = pg_fetch_row($result);
        if ($row) {
            $name = $row[4];
            $is_clone = Establishment::CheckOnClone($db_connection, $name);

            if ($is_clone == false) {
                $select_query = "INSERT INTO poi_catalogue(coordinates, name, cat_id, description, url, photo, email, user_id, create_date)
SELECT coordinates, name, cat_id, description, url, photo, email, user_id, create_date FROM uncheck_poi_catalogue WHERE id = '$id' RETURNING id";

                $result = pg_query($db_connection, $select_query);
                if (!$result) {
                    pg_result_error($result);
                    return false;
                }
                $last_insert_row = pg_fetch_row($result);
                $last_id = $last_insert_row[0];

                $select_query = "INSERT INTO poi_directions(poi_id, dir_id)
SELECT '$last_id' as poi_id, dir_id FROM uncheck_poi_directions WHERE poi_id = '$id'";
                $result3 = pg_query($db_connection, $select_query);

                if (!$result3) {
                    //pg_result_error($result3);
                    //return false;
                }

                $select_query = "INSERT INTO poi_phones(poi_id, number)
SELECT '$last_id' as poi_id, number FROM uncheck_poi_phones WHERE poi_id = '$id'";
                $result4 = pg_query($db_connection, $select_query);
                if (!$result4) {
                    //pg_result_error($result3);
                    //return false;
                }

                $select_query = "INSERT INTO poi_addresses(poi_id, address)
SELECT '$last_id' as poi_id, address FROM uncheck_poi_addresses WHERE poi_id = '$id'";
                $result5 = pg_query($db_connection, $select_query);
                if (!$result5) {
                    //pg_result_error($result3);
                    //return false;
                }

                $delete_query = "DELETE FROM uncheck_poi_catalogue WHERE id = '$id'";
                $result6 = pg_query($db_connection, $delete_query);
                if (!$result6) {
                    pg_result_error($result6);
                    return false;
                }
            }
        }

    }
    //-----------------------------END. ACCEPT POI
    //-------------------------------------------------
    //-----------------------------------------------------END. SUMMERY
    //----------------------------------------------------WORK WITH HANDBOOKS

    //-----------------------------------------------------BEGIN. CRAWLER HELPER
    //------------------------------BEGIN. CRAWLER HELPER
    public static function AddPoi($coordinates, $name, $cat_id, $dirs_id, $url, $description, $email, $phones, $photo, $user_id, $create_date) {
        //$is_created = '';

        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");
        //$db_connection = Db::getInstance();

        $name = pg_escape_string($name);
        $is_clone = Establishment::CheckOnClone($db_connection, $name);

        if ($is_clone == true)
            return '2';
        else {
            $description = pg_escape_string($description);
            $url = pg_escape_string($url);
            $description = pg_escape_string($description);
        //    $email = pg_escape_string($email);

     /*       $sql = "INSERT INTO uncheck_poi_catalogue(id, coordinates, cat_id, url, name, description, photo, user_id, create_date, email)
 VALUES (DEFAULT, DEFAULT, '8', 'url', '$name', '$description', '', '2', '2018-01-01', 'email')";
*/
            if ($coordinates == '') {
                $sql = "INSERT INTO uncheck_poi_catalogue(id, coordinates, cat_id, url, name, description, photo, user_id, create_date, email) 
 VALUES (DEFAULT, DEFAULT, '8', 'url', '$name', '$description', '', '2', '2018-01-01', 'email')";
            } else {
                $coords = explode(",", $coordinates);
                $lat = $coords[0];
                $lon = $coords[1];

                $sql = "INSERT INTO uncheck_poi_catalogue(id, coordinates, cat_id, url, name, description, photo, user_id, create_date, email)
  VALUES (DEFAULT, public. ST_SetSRID(public.ST_MakePoint($lon, $lat), 4326), 
 '8', 'url', '$name', '$description', '', '2', '2018-01-01', 'email')";
            }

            $result = pg_send_query($db_connection, $sql);
            if (!$result) {
                //pg_result_error($result);

                //return "Мітка НЕ додана!";
                return 0;
            } else
            {

                //return "Мітку додано!";
                return 1;
            }
        }


        return 0;
    }

    public static function get_category_id($cat_name) {
        //$db_connection = Db::getInstance();
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        $sql = "SELECT id FROM categories WHERE ukr_name = '$cat_name'";

        $result = pg_query($db_connection, $sql);
        if (!$result) {
            //echo 'An SQL error occured.\n';
            exit;
        } //else echo "Категорію знайдено!";

        $row = pg_fetch_row($result);
        $id = $row[0];


        return $id;
    }

    public static function GetUncheckPoiList(){
        //$db_connection = Db::getInstance();
        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        //$sql = "SELECT id, public.ST_AsGeoJSON(public.ST_ASTEXT(public.ST_TRANSFORM(uncheck_pois.coordinates, 4326))) AS coordinates, name, cat_id, description, url, photo, create_date FROM uncheck_pois";

        $select_query = "SELECT id, public.ST_AsGeoJSON(public.ST_ASTEXT(uncheck_poi_catalogue.coordinates)) AS coordinates, name, cat_id, description, create_date, photo FROM uncheck_poi_catalogue";

        $result = pg_query($db_connection, $select_query);
        $is_executed = Establishment::CheckQueryError($result);


        $list = array();

        if ($is_executed == true)
        {
            while ($row = pg_fetch_row($result)) {
                $id = $row[0];
                $coordinates = $row[1];
                $name = $row[2];
                $cat_id = $row[3];
                //$user_id = $row[4];
                $description = $row[4];
                $create_date = $row[5];
                $photo = $row[6];

                $list[] = array(
                    'id' => $id,
                    // 'coordinates' => json_decode($coordinates, true),
                    'name' => $name,
                    'cat_id' => $cat_id,
                    // 'cat_name' => $cat_name,
                    // 'user_id' => $user_id,
                    // 'user_login' => $user_login
                    'create_date' => $create_date
                );
            }

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($list),
                "iTotalDisplayRecords" => count($list),
                "aaData"=>$list);
        }


        return $results;
    }


    //------------------------------END. CRAWLER HELPER
    //-----------------------------------------------------
    public static function get_filtered_poi($filter_val){
        $db_connection = Db::getInstance();

        $geojson = array(
            'type'      => 'FeatureCollection',
            'features'  => array()
        );

        if ($filter_val == 'all') {
            $str = array(3, 4, 5, 6, 7, 8 );
            $arr = implode("', '", $str);
        } else {
            $str = json_decode(stripslashes($filter_val));
            $arr = implode("', '", $str);
        }

        /*$sql = "SELECT public.ST_AsGeoJSON(public.ST_ASTEXT(public.ST_TRANSFORM(planet_osm_point.way, 4326))) AS geojson, osm_id, name FROM planet_osm_point";*/

        /*$sql = "SELECT public.ST_AsGeoJSON(public.ST_ASTEXT(uncheck_poi_catalogue.coordinates)) AS geojson, id, name FROM uncheck_poi_catalogue WHERE coordinates is not null";
        */
        $sql = "SELECT public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)) AS geojson, id, name FROM poi_catalogue WHERE coordinates is not null";


        $result = pg_query($db_connection, $sql);
        if (!$result) {
            //echo 'An SQL error occured.\n';
            exit;
        }

        while ($row = pg_fetch_row($result)) {
            $properties = $row;
            $feature = array(
                'type' => 'Feature',
                'geometry' => json_decode($row[0], true),
                'properties' => $properties
            );
            # Add feature arrays to feature collection array
            array_push($geojson['features'], $feature);
        }

        //header("Content-type: application/json");

        return json_encode($geojson);

    }

    //<summery> get poi by category id and directions id </summery>
    //<var = $cat_id> id of category that will be searched</var>
    //<var = $dirs_ids> array of categories`s id that will be searched</var>
    public static function getFilteredPoi($cats_ids, $dirs_ids){

        //$cat_id = json_encode($cat_id);
        // $dirs_ids = json_encode($dirs_ids);

        //$cat_id = json_decode(stripslashes($cats_id));
        //$dirs_ids = json_decode($dirs_ids);

        $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

        $geojson = array(
            'type'      => 'FeatureCollection',
            'features'  => array()
        );

        /*
        if ($cat_id != '' && $dirs_ids != '') {
            $cat_id = json_decode(stripslashes($cat_id));
            $dirs_ids_arr = json_decode(stripslashes($dirs_ids));
            $dirs_ids_str = implode("', '", $dirs_ids_arr);
        } elseif ( $cat_id != '' && )
        {
            $cat_id = json_decode(stripslashes($cat_id));
        }*/

        $sql = '';

        if ( ($cats_ids != "") && ($dirs_ids != "") ) {
            //$cats_ids = json_decode(stripslashes($cats_ids));
            //$dirs_ids_str = implode("', '", $dirs_ids);

            $sql = "SELECT DISTINCT on (poi_catalogue.id) public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)) AS geojson, 
poi_catalogue.id, poi_catalogue.name, poi_catalogue.cat_id, poi_directions.poi_id, poi_directions.dir_id 
                FROM poi_catalogue, poi_directions 
                WHERE poi_catalogue.id = poi_directions.poi_id 
                  AND poi_directions.dir_id in ($dirs_ids) 
                  AND poi_catalogue.cat_id in ($cats_ids)
                  AND poi_catalogue.coordinates IS NOT NULL ORDER BY poi_catalogue.id ";
        }
        elseif ($cats_ids != "" && $dirs_ids == "") {
            //$cat_id = json_decode(stripslashes($cat_id));

            $sql = "SELECT DISTINCT on (poi_catalogue.id) public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)) AS geojson, poi_catalogue.id,
poi_catalogue.name, poi_catalogue.cat_id
                FROM poi_catalogue
                WHERE poi_catalogue.cat_id in ($cats_ids)
                  AND poi_catalogue.coordinates IS NOT NULL ORDER BY poi_catalogue.id ";
        }
        elseif ($cats_ids == null && $dirs_ids != null) {
            //$dirs_ids_arr = json_decode(stripslashes($dirs_ids));
            //$dirs_ids_str = implode("', '", $dirs_ids);

            $sql = "SELECT DISTINCT on (poi_catalogue.id) public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)) AS geojson, poi_catalogue.id,
poi_catalogue.name, poi_directions.poi_id, poi_directions.dir_id 
                FROM poi_catalogue, poi_directions 
                WHERE poi_catalogue.id = poi_directions.poi_id 
                  AND poi_directions.dir_id in ($dirs_ids)  
                  AND poi_catalogue.coordinates IS NOT NULL ORDER BY poi_catalogue.id ";
        } 
        else
            $sql = "SELECT public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)) AS geojson, id, name FROM poi_catalogue WHERE coordinates is not null ";
        

        $result = pg_query($db_connection, $sql);
        if (!$result) {
            //echo 'An SQL error occured.\n';
            //exit;
            return;
        }

        while ($row = pg_fetch_row($result)) {
            $properties = $row;
            $feature = array(
                'type' => 'Feature',
                'geometry' => json_decode($row[0], true),
                'properties' => $properties
            );
            # Add feature arrays to feature collection array
            array_push($geojson['features'], $feature);
        }

        //header("Content-type: application/json");

        return json_encode($geojson);

    }


    public static function get_catalogue(){
        $db_connection = Db::getInstance();

        $sql = "SELECT id, public.ST_AsGeoJSON(public.ST_ASTEXT(public.ST_TRANSFORM(pois_full_info.coordinates, 4326))) AS coordinates, name, cat_id, dir_id, description, url, phone_number, photo FROM pois_full_info";

        $result = pg_query($db_connection, $sql);
        if (!$result) {
            //echo 'An SQL error occured.\n';
            exit;
        }

        $pois = [];

        while ($row = pg_fetch_row($result)) {
            $pois[] = new establishment($row[0], $row[1], $row[2],
                $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], '');
        }

        //$db_connection = NULL;
        return $pois;
    }

    public static function ClearUnchPCat() {
        $db_connection = Db::getInstance();

        $sql = "Delete From uncheck_poi_catalogue";

        $result = pg_query($db_connection, $sql);
    }

    public static function search_poi($pattern)
    {

        $db_connection = Db::getInstance();

        $sql = "SELECT public.ST_AsGeoJSON(public.ST_ASTEXT(poi_catalogue.coordinates)) AS geojson, id, LOWER(name) FROM poi_catalogue WHERE position(LOWER('$pattern') in lower(name)) > 0 AND coordinates IS NOT NULL";

        $result = pg_query($db_connection, $sql);
        if (!$result) {
            //echo 'An SQL error occured.\n';
            exit;
        }

        $geojson = array(
            'type'      => 'FeatureCollection',
            'features'  => array()
        );

        while ($row = pg_fetch_row($result)) {
            $properties = $row;
            $feature = array(
                'type' => 'Feature',
                'geometry' => json_decode($row[0], true),

                'properties' => $properties
            );
            # Add feature arrays to feature collection array
            array_push($geojson['features'], $feature);
        }

        $db_connection = NULL;
        return json_encode($geojson);
    }

}//class end

?>