<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 01.02.2018
 * Time: 23:10
 */

  class Db {
      private static $instance = NULL;

      private function __construct() {}

      private function __clone() {}

      public static function getInstance() {
          if (!isset(self::$instance)) {
              //$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
              self::$instance = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");

          }
          return self::$instance;
      }
  }
?>