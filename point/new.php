<?php
		require_once('../common/authorize.php');
		require_once('../common/app_config.php');
		if(!isset($_SESSION['user_id']))header('location: /signin.php');
		if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403. Доступ запрещён');
		$_SESSION['error'] = '';
		if(isset($_POST['name'])){
			$errors = [];
			$_POST['name'] = trim($_POST['name']);
			$_POST['x'] = trim($_POST['x']);
			$_POST['y'] = trim($_POST['y']);
			$routes_repo =  new Nulpunkt\Yesql\Repository($conn, "../sql/routes.sql");
			if(strlen($_POST['name'])==0) $errors['name'] = 'Наименование точки не должно быть пустым';
				else
			{
				$point = $routes_repo->getPointByName($_POST['name']);
				if(count($point)!==0) $errors['name'] = 'Уже существует точка с таким названием. Координаты: '.$point[0]['coordinate'].'. Придумайте другое';
				if(!preg_match('/^-?\d{1,3}\.\d+$/', $_POST['x'])) $errors['x'] = 'Неверный формат широты'; 
					else if($_POST['x']*1.0<-90 || $_POST['x']*1.0>90 ) $errors['x'] = 'Широта бывает от -90 до 90 градусов';
				if(!preg_match('/^-?\d{1,3}\.\d+$/', $_POST['y'])) $errors['y'] = 'Неверный формат долготы'; 
					else if($_POST['y']*1.0<-180 || $_POST['y']*1.0>180 ) $errors['y'] = 'Долгота бывает от -180 до 180 градусов';
				if(count($errors)==0){
					$errors = ['name'=>'', 'x'=>'','y'=>'' ];
					$routes_repo->createPoint($_POST['name'],"(".$_POST['x'].",".$_POST['y'].")");
					header('Location: /points');

				}
			}

			
		}
		include('../common/headers.php');
	?>
		<h1>Создание новой точки</h1>
			<form method="POST" id="create_point">
				<?=field("Наименование:",['type'=>'text','name'=>'name','id'=>'name', 'value' => $_POST['name']], $errors['name'] ) ?>
				<div class="row">
					<div class="col-md-7">	
		      			<p>На карте:</p>
						<div id="map" style="width: 600px; height: 400px"></div>
					</div>
					<div class="col-md-5">
						<p>Координаты:</p>
						<?=field("Широта:",['type'=>'input','name'=>'x','id'=>'x', 'value' => $_POST['x']], $errors['x']) ?>
						<?=field("Долгота:",['type'=>'input','name'=>'y','id'=>'y','value'=>$_POST['y']], $errors['y']) ?>
						<input type="submit" name="save_point" value="Сохранить" class="btn btn-primary btn-lg" />
					</div>

		       		
				</div>
				

			</form>
	
	</div>
	<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
	<script type="text/javascript" src="../assets/js/point/new.js"></script>


	</body>
<html>