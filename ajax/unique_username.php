	<?php
	    require_once('../common/app_config.php');
		$users_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/users.sql");
		if(isset($_REQUEST['username']) && trim($_REQUEST['username']!="")){
			$user = $users_repo->getUserByUsername($_REQUEST['username']);
			echo count($user);
		} else
		die('Такой страницы не существует');
	?>

