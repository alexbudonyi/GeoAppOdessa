<?php



    require_once($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/models/establishment.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/models/crawler.php");

class establishment_controller {
    
    //---------------work with map
    public function index() {
      
      require_once("views/establishment/index.php");
    }

    public function add_category($cat_name, $user_id) {

        if ($user_id == 1)
            Establishment::AddCategory($cat_name);
        else
            Establishment::AddUncheckCategory($cat_name);
        //echo "hello2";
    }

    public function add_direction($dir_name, $user_id) {
        if ($user_id == 1)
            Establishment::AddDirection($dir_name);
        else
            Establishment::AddUncheckDirection($dir_name);
        //echo "hello2";
    }

    public function get_categories() {
         $result = Establishment::GetCategoriesList();
         echo json_encode($result);
    } 

    public function get_filtered($cats_id, $dirs_ids) {
      
      //$cats_id = json_decode($cats_id, true);
      //$dirs_ids = json_decode($dirs_ids, true);
   
      $result = Establishment::getFilteredPoi($cats_id, $dirs_ids);
      return $result;
    }

    public function create_poi($data) {
         /*AddUncheckPoi($coordinates, $name, $cat_id, $description, $url, $photo, $email, $directions,
                                         $addresses, $phones, $user_id, $create_date) {
         */

          $data = json_decode($data, true);

          foreach($data as $item){
            foreach($item as $i){
              $cat_id = $i['cat_id'];
              //$dir_id = $i['dir_id'];
              $url = $i['url'];
              $name = $i['name'];
              $description = $i['description'];
              $longitude = $i['longitude'];
              $latitude = $i['latitude'];
                //echo $i['photo'];
              $photoEncoded = str_replace("data:image/jpeg;base64,","", $i['photo']);
              $photo = self::getPhoto($photoEncoded);
              //echo $photo;

              //get address by lat and long
              $address[] = Establishment::GetAddress($longitude, $latitude);

              $directions = $i['directions'];

              $coordinates = $longitude . "," . $latitude;
              array_push($address, $longitude, $latitude);
            }
        }

        session_start();
        $user_id = $_SESSION["user_id"];

        $result = Establishment::AddUncheckPoi($coordinates, $name, $cat_id, $description, $url, $photo, '', $directions,
                                         $address, '', $user_id, '2018-02-01');
    }

    public static function getPhoto($image) {

        $file_data = base64_decode($image);
        //echo $file_data;
        $convertedFile = pg_escape_bytea($file_data);
        return $convertedFile;
    }


    public function update_poi($id, $coodinates, $name, $cat_id, $description,
        $website, $directions, $addresses, $phones) {

        $directions = json_decode($directions);
        $addresses = json_decode($addresses);
        $phones = json_decode($phones);
        //echo $coodinates;
        $result = Establishment::EditPoi($id, $coodinates, $name, $cat_id, $description, $website, '', '', $directions, $addresses,
            $phones, '');

    }

    public function update_uncheckpoi($id, $coodinates, $name, $cat_id, $description,
                               $website, $directions, $addresses, $phones) {
        //echo "hello2";
        $directions = json_decode($directions);
        $addresses = json_decode($addresses);
        $phones = json_decode($phones);
        //echo $coodinates;
        $result = Establishment::EditUncheckPoi($id, $coodinates, $name, $cat_id, $description, $website, '', '', $directions, $addresses,
            $phones, '');

    }
    public function my_pois() {
        //$details = Establishment::GetPoiDetails($_GET['id']);
        $user_id = $_SESSION["user_id"];
        $pois = Establishment::GetUserPoi($user_id);
        require_once("views/establishment/my_pois.php");
    }

    public function get_directions() {
         $result = Establishment::GetDirectionsList();
         echo json_encode($result);
    }

    public static function get_filtered_poi($filter_val){
        echo Establishment::get_filtered_poi($filter_val);
    } //end function getFilteredPOI

    public static function search_poi($search_pattern){
        return establishment::search_poi($search_pattern);
    }

    //----begin. get details for poi in map in json format 
    public static function get_poi_details($id) {
         $result = Establishment::GetPoiDetails($id);
         echo json_encode($result);
    }
    //----end. get details for poi in map in json format 

    //---------------end. work with map


    //-------------work with poi catalogue
    
    //----begin. get details for poi in catalogue as array of poi details
    public function get_details() {
      $details = Establishment::GetPoiDetails($_GET['id']);
      
      require_once("views/establishment/get_details.php");
    }
    //----end. get details for poi in catalogue as array of poi details

    public function edit_poi() {
      $details = Establishment::GetPoiDetails($_GET['id']);
      
      require_once("views/establishment/edit_poi.php");
    }

    public function edit_uncheckpoi() {
        $details = Establishment::GetUncheckPoiDetails($_GET['id']);

        require_once("views/establishment/edit_uncheckpoi.php");
    }

    public function accept_uncheck_cat($id) {
        $details = Establishment::AcceptUncheckCat($id);
    }

    public function accept_uncheck_dir($id) {
        $details = Establishment::AcceptUncheckDir($id);
    }

    public function delete_uncheck_cat_dir($id) {
        Establishment::DeleteUncheckCatDir($id);
    }

    public function create_uncheck_poi() {
      
      require_once("views/establishment/create_uncheck_poi.php");
    }

    public function add_uncheck_poi($name, $cat_id, $descr, $url, $email, $dirs_id, $addresses, $phones) {

        //echo $_SESSION['user_id'];
        session_start();
        $user_id = $_SESSION["user_id"];
        $result = Establishment::AddUncheckPoi('', $name, $cat_id, $descr, $url, '', $email, $dirs_id, $addresses, $phones, $user_id, '2018-02-09');
        //echo json_encode($poiList);
    }

    public function get_uncheck_details() {
      $details = Establishment::GetUncheckPoiDetails($_GET['id']);
      
      require_once("views/establishment/get_uncheck_details.php");
    }
    //--------end. uncheck

    public function poi_catalogue() {  
    
      require_once("views/establishment/poi_catalogue.php");
    }
    
    public function get_poi_catalogue() {
         $poiList = Establishment::GetPoiList();
         echo json_encode($poiList);
    }

    public function delete_poi($id) {
         $result = Establishment::DeletePoi($id);
         //echo json_encode($poiList);
    }

    public function delete_category($id) {
        //echo "hello2";
        Establishment::DeleteCategory($id);
    }

    public function delete_direction($id) {
        //echo "hello2";
        Establishment::DeleteDirection($id);
    }

    public function edit_category($id, $cat_name) {
        //echo "hello2";
        Establishment::EditCategory($id, $cat_name);
    }

    public function edit_direction($id, $dir_name) {
        //echo "hello2";
        Establishment::EditDirection($id, $dir_name);
    }

    //------------end. work with catalogue

    //-------------work with uncheck poi catalogue
    public function uncheck_poi_cat() {  
      //$poiList = Establishment::GetUncheckPoiList();
      require_once("views/establishment/uncheck_poi_cat.php");
    }

    public function get_uncheck_poi_cat() {  
      $poiList = Establishment::GetUncheckPoiList();
      echo json_encode($poiList);
      //require_once("views/establishment/get_uncheck_poi_cat.php");
    }

    public function uncheck_cat_dir() {
        //$poiList = Establishment::GetUncheckPoiList();
        require_once("views/establishment/uncheck_cat_dir.php");
    }

    public function get_uncheck_cat_dir() {
        $list = Establishment::GetUncheckCatDirList();
        echo json_encode($list);
        //require_once("views/establishment/get_uncheck_poi_cat.php");
    }

    public function accept_uncheck_poi($id) {
      Establishment::AcceptUncheckPoi($id);
      //echo json_encode($poiList);
    }

    public function delete_uncheck_poi($id) {
      $result = Establishment::DeleteUncheckPoi($id);
      //echo json_encode($poiList);
    }

    public function parse(){
      
      Parser::run_parser();
      //echo json_encode($testing);
    }

    public static function delete_uncheck_pois(){
      //$parser1 = new Parser();
      //$parser1->run_parser();
      Establishment::ClearUnchPCat();
    }
    
    //--------------------------end. work with uncheck
    public function categories() {
      
      require_once("views/establishment/categories.php");
    }
    
    public function directions() {
      
      require_once("views/establishment/directions.php");
   }

    public function get_filtered_list($cat, $dirs) {
        $poiList = Establishment::GetFilteredList($cat, $dirs);
        echo json_encode($poiList);
        //echo "hello";
    }


    public function create_user($email, $password, $login) {
         Establishment::CreateUser($email, $password, $login);
         
    } 

}

//-------------------call functions by ajax
    //-------робота із мапою
    if (isset($_POST['get_filtered_poi'])) {    
        echo establishment_controller::get_filtered_poi($_POST['get_filtered_poi']);
    } elseif (isset($_POST['get_categories'])) {
        echo establishment_controller::get_categories();
    } elseif (isset($_POST['get_directions'])) {
        echo establishment_controller::get_directions();
    } elseif (isset($_POST['search_poi'])) {
        echo establishment_controller::search_poi($_POST['search_poi']);
    } elseif (isset($_POST['get_poi_details'])) {
        echo establishment_controller::get_poi_details($_POST['get_poi_details']);
    } elseif (isset($_POST['create_poi'])) {
        establishment_controller::create_poi($_POST['create_poi']);
        echo "Мітка відправлена на модерацію";
    } elseif (isset($_POST['update_poi'])) {
        echo establishment_controller::update_poi($_POST['id'], $_POST['coordinates'], $_POST['name'], $_POST['cat_id'], $_POST['description'],
            $_POST['website'], $_POST['dirs'], $_POST['addresses'], $_POST['phones']);
        //echo json_decode($_POST['dirs']);
    } elseif (isset($_POST['update_uncheckpoi'])) {
        echo establishment_controller::update_uncheckpoi($_POST['id'], $_POST['coordinates'], $_POST['name'], $_POST['cat_id'], $_POST['description'],
            $_POST['website'], $_POST['dirs'], $_POST['addresses'], $_POST['phones']);
        //echo json_decode($_POST['dirs']);
    } elseif (isset($_POST['get_filtered_cat'])) {
        echo establishment_controller::get_filtered($_POST['get_filtered_cat'], $_POST['get_filtered_dir']);
    }
    //-------кінець. робота із мапою 

    //-------робота із каталогом точок
      elseif (isset($_POST['get_poi_catalogue'])) {
        echo establishment_controller::get_poi_catalogue();
    } elseif (isset($_POST['delete_poi'])) {
        establishment_controller::delete_poi($_POST['delete_poi']);
    } elseif (isset($_POST['add_uncheck_poi'])) {
        echo establishment_controller::add_uncheck_poi($_POST['name'], $_POST['cat_id'], $_POST['description'], $_POST['website'], $_POST['email'], $_POST['dirs_id'], $_POST['address'], $_POST['phone']);
    } elseif (isset($_POST['get_filtered_list'])) {
        echo establishment_controller::get_filtered_list($_POST['cat'], $_POST['dirs']);
    } elseif (isset($_POST['add_category'])) {
        echo establishment_controller::add_category($_POST['add_category'], $_POST['user_id']);
        //echo "hello";
    } elseif (isset($_POST['add_direction'])) {
        echo establishment_controller::add_direction($_POST['add_direction'], $_POST['user_id']);
        //echo "hello";
    } elseif (isset($_POST['delete_category'])) {
        establishment_controller::delete_category($_POST['delete_category']);
    } elseif (isset($_POST['edit_category'])) {
        establishment_controller::edit_category($_POST['edit_cat_id'], $_POST['edit_cat_name']);
    } elseif (isset($_POST['delete_direction'])) {
        establishment_controller::delete_direction($_POST['delete_direction']);
    } elseif (isset($_POST['edit_direction'])) {
        establishment_controller::edit_direction($_POST['edit_dir_id'], $_POST['edit_dir_name']);
    }

    //-------кінець. робота із каталогом

    elseif (isset($_POST['get_uncheck_cat_dir'])) {
        echo establishment_controller::get_uncheck_cat_dir();
    }

    //-------робота із не зареєстрованими точками
     elseif (isset($_POST['get_uncheck_poi_cat'])) {
        echo establishment_controller::get_uncheck_poi_cat();
     }
     elseif (isset($_POST['accept_uncheck_cat_dir'])) {
         if ($_POST['data_type_id'] == "0")
            echo establishment_controller::accept_uncheck_cat($_POST['accept_uncheck_cat_dir']);
         else if ($_POST['data_type_id'] == "1")
             echo establishment_controller::accept_uncheck_dir($_POST['accept_uncheck_cat_dir']);
    }
     elseif (isset($_POST['delete_uncheck_cat_dir'])) {
        establishment_controller::delete_uncheck_cat_dir($_POST['delete_uncheck_cat_dir']);
    }
     elseif (isset($_POST['accept_uncheck_poi'])) {
        establishment_controller::accept_uncheck_poi($_POST['accept_uncheck_poi']);
    }
     elseif (isset($_POST['delete_uncheck_poi'])) {
        establishment_controller::delete_uncheck_poi($_POST['delete_uncheck_poi']);
    }
     elseif (isset($_POST['edit_uncheck_poi'])) {
        establishment_controller::edit_uncheck_poi($_POST['edit_uncheck_poi']);
    }
     elseif (isset($_POST['parse'])) {
        //echo establishment_controller::accept_poi($_POST['accept_poi']); 
        //echo "ok";

        establishment_controller::parse();
         return;
        //return $testing;
        
        //echo "ok";
    }
     elseif (isset($_POST['delete_uncheck_pois'])) {
        establishment_controller::delete_uncheck_pois();
    } 
     //-------кінець. робота із не зареєстрованими точками

     elseif (isset($_POST['create_user'])) {
        //echo establishment_controller::accept_poi($_POST['delete_uncheck_pois']); 
        //echo "ok";
        establishment_controller::create_user($_POST['email'], $_POST['password'], $_POST['login']);
    }

?>