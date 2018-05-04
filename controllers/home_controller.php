<?php
  class HomeController {
    public function home() {
      $first_name = 'Jon';
      $last_name  = 'Snow';
      require_once('views/home/home.php');
    }

    public function about() {
     
      require_once('views/home/about.php');
    }

    public function error() {
      require_once('views/home/error.php');
    }
  }
?>