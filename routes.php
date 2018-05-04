<?php
  function call($controller, $action) {
    require_once('controllers/' . $controller . '_controller.php');

    //switch controller
    switch($controller) {
      case 'home':
        $controller = new HomeController();
      break;
      case 'establishment':
        require_once('models/establishment.php');
        $controller = new establishment_controller();
      break;
      case 'account':
        require_once('models/privileged_user.php');
        $controller = new account_controller();
        break;
      case 'message':
        require_once('models/message.php');
        $controller = new message_controller();
      break;
      
    }

    $controller->{ $action }();
  }

  // we're adding an entry for the new controller and its actions
  $controllers = array('home' => ['home', 'about', 'error'],
                       'establishment' => ['index', 'poi_catalogue', 'get_details', 'get_uncheck_details',
                           'uncheck_poi_cat', 'create_uncheck_poi', 'categories', 'directions', 'edit_poi',
                           'my_pois', 'edit_uncheckpoi', 'uncheck_cat_dir', 'error'],
                       'message' => ['index', 'error'],
                       'account' => ['register', 'login', 'recover_password', 'update_password', 'error']);

  if (array_key_exists($controller, $controllers)) {
    if (in_array($action, $controllers[$controller])) {
      call($controller, $action);
    } else {
      call('home', 'error');
    }
  } else {
    call('home', 'error');
  }
?>