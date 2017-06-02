<?php
	require('app_config.php');
	session_start();
	$error_message = '';
  	if(!isset($_SESSION['user_id'])){
		if(isset($_POST['username']) && isset($_POST['password'])){
			$users_repo = new Nulpunkt\Yesql\Repository($conn, $_SERVER['DOCUMENT_ROOT']."/sql/users.sql");
			$user = $users_repo->getUserByUsername(trim($_POST['username']));
			if(count($user)==1) {
				$user = $user[0];
				$password = $user['password'];
				if (password_verify($_POST['password'],$password )) {
					$_SESSION['user_id'] = $user['id'];
					$_SESSION['username'] = $user['username'];
					$_SESSION['first_name'] = $user['first_name'];
					$_SESSION['last_name'] = $user['last_name'];
					$roles = $users_repo->getUserRoles($_SESSION['user_id']);
					$_SESSION['roles'] = array_map(function($el){return $el['name'];},$roles);

				} else $error_message = "Неверный пароль";

			} else  $error_message = "Пользователь ".$_POST['username']." не найден";
				
				
			}
			if($error_message!='')  $_SESSION['error'] = $error_message;
			


		
		}

			
?>