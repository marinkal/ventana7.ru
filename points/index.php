<?php
		require_once('../common/authorize.php');
		require_once('../common/app_config.php');
		if(!isset($_SESSION['user_id'])) header('Location: ../signin.php');
		if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403.Доступ запрещен');
		$_SESSION['error'] = '';
		$routes_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/routes.sql");
		
		$_SESSION['error'];
		if(isset($_POST['remove_point'])) {
			if(count($routes_repo->getRoutesByPoint($_POST['remove_point']))==0){
				$routes_repo->removePoint($_POST['remove_point']);
			}else 
		{
			$_SESSION['error'] = 'Невозможно удалить точку. Точка используется в маршрутах';
		} }
		$points = $routes_repo->getPoints();
		include('../common/headers.php');
	?>

			 	<table class = "table">
						<h1>Точки:</h1>
						<form method="GET" action="../point/new.php">
							<?=button('Создать новую',['type'=>'submit','class'=>'btn btn-primary'])?>
						</form>
						<form method="POST">
						<?php foreach ($points as $point) { 
								preg_match('/([\d]+(\.[\d]+)?),([\d]+(\.[\d]+)?)/', $point['coordinate'],$matches);
							 ?>
							<tr>
							<td><a href="../point/show.php?id=<?=$point[id]?>"><?=$point["name"]?></a></td>
							<td><?=$matches[1]?></td>
							<td><?=$matches[3]?></td>
							<td> 
								<a class="btn btn-success" href="../point/edit.php?id=<?=$point[id]?>">Переименовать</a>
								<form method="POST" name="remove_point_form" style="display:inline">
									<button type="submit"  class='btn btn-danger' name='remove_point' value="<?=$point[id]?>">Удалить</button>
								</form>		
							</td>
							</tr>
						</form>	
						<?}?>
				</table>  
			
		</div>		
	</body>
</html>