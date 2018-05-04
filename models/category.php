<?php
	class category {
		public $id;
		public $eng_name;
		public $ukr_name;
	
		public function __construct($id, $eng_name, $ukr_name) {
			$this->id = $id;
			$this->eng_name = $eng_name;
			$this->ukr_name = $ukr_name;
			
		}

		public static function get_categories() { 

  			$cats = array();

  			$db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

  			$sql = "SELECT * FROM categories";
  			# Try query or error
  			$result = pg_query($db_connection, $sql);
  			if (!$result) {
    			exit;
  			}

  			while ($row = pg_fetch_row($result)) {
     			$cats[] = $row ;
  			}
  
  			//header("Content-type: application/json");  
  
  			echo json_encode($cats);

  			$db_connection = NULL;
		}
	}

?>