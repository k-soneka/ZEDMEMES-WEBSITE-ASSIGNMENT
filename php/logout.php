<?php
session_start();
session_unset();
session_destroy();

header('Content-Type: application/json');
echo json_encode(["status"=> "logged_out"]);


//require_once 'config.php';

      //$_SESSION=array();

      //session_destroy();

      //if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest'){
        //echo json_encode(['success'=> true]);
      //}
   //else{
    //header("Location:index.php");
  //}
?>