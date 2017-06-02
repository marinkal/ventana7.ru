<?php
	require_once('../common/authorize.php');
	require_once('../common/app_config.php');
	if(!isset($_SESSION['user_id']))header('location: /signin.php');
	if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403. Доступ запрещён');
	$routes_repo =  new Nulpunkt\Yesql\Repository($conn, "../sql/routes.sql");
	$routes = $routes_repo->getRoutes();
	$points = $routes_repo->getPoints();
	if(isset($_POST["name"])){
		$_POST['name'] = trim($_POST['name']);
		$errors = [];
		if(strlen($_POST['name'])==0) $errors['name'] = 'Название маршрута не должно быть пустым'; else {
				$route = $routes_repo->getRouteByName($_POST['name']);
				if(count($route)!==0) $errors['name'] = 'Уже существует маршрут с таким названием. Придумайте другое';
		}

		if(count($errors)==0){
			$id = $routes_repo->createRoute(trim($_POST['name']));
			$routes_repo->addPointToRoute($id,$_POST['start_point'],'00:00');
			header("Location:show.php?id=$id");
		}
	}
	include('../common/headers.php');
?>

		<h1>Создание нового маршрута</h1>
		<form action="new.php" method="POST" id="add_route_form">
			<?=field("Наименование:",['type'=>'text','name'=>'name','id'=>'name','value'=>$_POST['name']],$errors['name'])?>
			<?=select("Точка старта:",['name'=>'start_point','id'=>'start_point'],$points,0)?>
			<button type="Submit" name="add_route" class="btn btn-primary">Сохранить</button>
		</form>
	</div>
	<script>
	$(document).ready(function(e){
		$('#add_route_form').submit(function(e){
			e.preventDefault();
			$('.bg-danger').empty();
			var errors = {};
			if($('#name').val().trim().length<1) errors['name'] = 'Наименование маршрута не должно быть пустым';
			if(Object.keys(errors).length!==0){
				for(key in errors){
						$('#'+key+'_err').text(errors[key]);
				}
			} else {
				this.submit();
			}

		});
	});
	</script>
</body>