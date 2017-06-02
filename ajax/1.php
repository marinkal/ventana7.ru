	<?php
	    require_once('../common/app_config.php');
		$id = $_GET['id'];
		$route_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/routes.sql");
		$routes = $route_repo->getValidateRoutes();
		$results = [];
		echo json_encode($routes);
	?>
