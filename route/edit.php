<?php
	require_once('../common/authorize.php');
	require_once('../common/app_config.php');
	if(!isset($_SESSION['user_id']))header('location: /signin.php');
	if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403. Доступ запрещён');
	
	$route_id = $_GET["id"];
	$routes_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/routes.sql");
	$route = $routes_repo -> getRoute($route_id);
	if(count($route)==0) die('Такой страницы не существует');
	$errors['name'] = '';
	if(isset($_POST['button1']) && $_POST['button1']==1){
		$errors = [];
		$new_name = trim($_POST['name']);
		if($route[0]['name']!=$new_name){
			if($new_name=="") $errors['name'] = 'Наименование маршрута не должно быть пустым';
				else {
					$check = $routes_repo->getRouteByName($new_name);
					if(count($check)!=0) $errors['name'] = 'Уже существует маршрут с таким наименованием. Придумайте другое';
						else{
							$routes_repo->updateRouteName($new_name,$route_id);
							$errors['name'] = '';
							header('location: /routes');
						}
			}
		} else
		header('location: /routes');
	}
	include('../common/headers.php');
?>
	<h1>Переименовать маршрут "<?=$route[0]['name']?>"</h1>
		<form method="POST" id="edit_route_form">
			<?=input_hidden('button1','button1',0) ?>
			<?=field("Наименование:",['type'=>'text','name'=>'name','id'=>'name','value'=>$_POST['name']],$errors['name'])?>
			<button type="Submit" name="add_route" class="btn btn-primary">Сохранить</button>
		</form>
		</div>
	<script>
	$(document).ready(function(e){
		$('#edit_route_form').submit(function(e){
			e.preventDefault();
			$('#button').val(0);
			$('.bg-danger').empty();
			var errors = {};
			if($('#name').val().trim().length<1) errors['name'] = 'Наименование маршрута не должно быть пустым';
			if(Object.keys(errors).length!==0){
				for(key in errors){
						$('#'+key+'_err').text(errors[key]);
				}
			} else {
				$('#button1').val(1);
				this.submit();
			}

		});
	});
	</script>
	</body>
</html>