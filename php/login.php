<?php
session_start();

require_once 'config.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $username=$conn->real_escape_string($_POST['username']);
  $password=$_POST['password'];

  $stmt=$conn->prepare("SELECT userID,username,password_hash FROM user WHERE username=?");
  $stmt->bind_param("s",$username);
  $stmt->execute();
  $result=$stmt->get_result();
    

  if( $result->num_rows===1){
    $user= $result->fetch_assoc();
    if($password_verify($password,$user[password_hash])){
      $_SESSION['user'] = $userID;
      $_SESSION['user_name'] = $username;
      echo json_encode(['success'=> true]);
    }else{
      echo json_encode(['success'=> false,'message'=>'Invalid credentials']);
    }

  }else{
    echo json_encode(['success'=> false,'message'=>'User not found']);
  
  }else{
    header("Location:index.php");
  }

}
?>