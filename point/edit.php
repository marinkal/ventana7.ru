<?php
	require_once('../common/authorize.php');
	require_once('../common/app_config.php');
	if(!isset($_SESSION['user_id']))header('location: /signin.php');
	if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403. Доступ запрещён');
	
	$point_id = $_GET["id"];
	$points_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/routes.sql");
	$point = $points_repo -> getPoint($point_id);
	if(count($point)==0) die('Такой страницы не существует');
	$errors['name'] = '';
	if(isset($_POST['button1']) && $_POST['button1']==1){
		$errors = [];
		$new_name = trim($_POST['name']);
		if($point[0]['name']!=$new_name){
			if($new_name=="") $errors['name'] = 'Наименование маршрута не должно быть пустым';
				else {
					$check = $points_repo->getPointByName($new_name);
					if(count($check)!=0) $errors['name'] = 'Уже существует точка с таким наименованием. Придумайте другое';
						else{
							$points_repo->updatePointName($new_name,$point_id);
							$errors['name'] = '';
							header('location: /points');
						}
			}
		} else
		header('location: /points');
	}
	include('../common/headers.php');
?>
	<h1>Переименовать маршрут "<?=$point[0]['name']?>"</h1>
		<form method="POST" id="edit_point_form">
			<?=input_hidden('button1','button1',0) ?>
			<?=field("Наименование:",['type'=>'text','name'=>'name','id'=>'name','value'=>$_POST['name']],$errors['name'])?>
			<button type="Submit" name="edit_point" class="btn btn-primary">Сохранить</button>
		</form>
		</div>
	<script src="../assets/js/point/edit.js" type="text/javascript"></script>
	</body>
</html>