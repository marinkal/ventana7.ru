<?php
	include $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";
	require_once("forms.php"); 
	date_default_timezone_set('Asia/Yekaterinburg');
	try{
		$host = 'localhost';
		$port = '5432';
		$dbname = 'ventana';
		$password = '';
		$user = "postgres";

		

		$conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname",$user,$password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//тестируем
		$memcache = new Memcache;
		$memcache->connect('localhost', 11211) or die ("Не могу подключиться");
		/*$memcache->set('int', 99);
		$memcache->set('string', 'a simple string');
		$memcache->set('array', array(11, 12));*/
		 $routes_repo = new Nulpunkt\Yesql\Repository($conn, $_SERVER['DOCUMENT_ROOT']."/sql/routes.sql");
		 $memcache->set('getRoutesFullInfo',$routes_repo->getRoutesFullInfo());
	}
	catch(PDOException $e){
		$user_error_message = "возникла проблема, связанная с подключением к базе данных, содержащей нужную информацию.";
		$system_error_message = $e->getMessage();
	echo $e->getMessage();;
}

?>