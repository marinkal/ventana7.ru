<?php
		require_once('../common/authorize.php');
		require_once('../common/app_config.php');
		if(!isset($_SESSION['user_id'])) header('Location: ../signin.php');
		if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403.Доступ запрещен');
		$routes_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/routes.sql");
		$requests_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/requests.sql");
		$_SESSION['error'] = '';
		if(isset($_POST['remove_route'])) {
			if(count($requests_repo->getActiveRequests($_POST["remove_route"]))!=s0){
				$_SESSION['error'] = 'Маршрут нельзя удалить. Он используется в выполнных или текущих заявках';

			} else 	{
				$requests_repo->removeRequestsByStatus($_POST['remove_route'],'request');
				$routes_repo->removePointsOfRoute($_POST["remove_route"]);
				$routes_repo->removeRoute($_POST["remove_route"]);
				header('location: ../routes');
			}
		}
		 
		$routes = $routes_repo->getRoutes();
		include('../common/headers.php');
	?>		
			 
						<h1>Маршруты:</h1>
						<!--<form method="GET" name="edit_route_form"  action="../route/edit.php"></form>-->
						</form>
						<form method="GET" action="../route/new.php">
							<?=button('Создать новый',['type'=>'submit','class'=>'btn btn-primary'])?>
						</form>

						<table class = "table">
						
						<?php foreach($routes as $route) { ?>
							<tr>
							<td><a href ="../route/show.php?id=<?=$route[id]?>"  ><?=$route["name"]?></a></td>
							<td>
								<a href="../route/edit.php?id=<?=$route[id]?>" class="btn btn-success">Переименовать</a>
								<form method="POST" name="remove_route_form" style="display:inline">
									<button type="submit"  class='btn btn-danger' name='remove_route' value="<?=$route[id]?>">Удалить</button>
								</form>								
							</td>
							<tr>
						
						<?}?>
				</table>  
			
		</div>		
	</body>
</html>