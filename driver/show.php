<?php	
		require_once('../common/authorize.php');
	    require_once('../common/app_config.php');
	    require_once('../common/maps.php');
	    if(!isset($_SESSION['user_id'])) header('Location: ../signin.php'); 
	    if(!in_array('manager', $_SESSION['roles'])) die('Ошибка 403. Доступ запрещен');
	    $manager_id = $_SESSION['user_id'];
	    $id = $_GET['id'];    
		$routes_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/routes.sql");
		$users_repo =  new Nulpunkt\Yesql\Repository($conn, "../sql/users.sql");
		$requests_repo =  new Nulpunkt\Yesql\Repository($conn, "../sql/requests.sql");
		$drivers_repo =  new Nulpunkt\Yesql\Repository($conn, "../sql/drivers.sql");
		$check = $users_repo->isUserInRole($id,'driver');
		if(count($check)==0) die('Такой страницы не существует');

		if (isset($_POST["button1"]) && $_POST['button1']==1){	
			$errors = [];
			if(!preg_match('/^[\d]{4}\/[\d]{2}\/[\d]{2} [\d]{2}:[\d]{2}$/', $_POST['datetimepicker1'])) $errors['datetimepicker1'] = 'Выберите дату и время в календаре или введите в формате гггг/мм/дд чч:мм';
			if(count($errors)==0){
				$old_requests = $requests_repo->isCanCreate($_POST['datetimepicker1'],$id);
				if(count($old_requests)==0){			
					$request_id = $requests_repo->createRequestNew($_POST["route"],$_POST['datetimepicker1'],$manager_id,$id);
					$route_points = $routes_repo->getPointsOfRoute($_POST["route"]);
					foreach ($route_points as $route_point) {
						$requests_repo->createFacts($route_point['rp_id'],$request_id,$route_point['expected_time'],NULL,NULL,NULL);
					}
					$errors['datetimepicker1'] = '';
				}
				else $errors['datetimepicker1'] = 'Невозможно назначить маршрут. Водитель выполняет другой маршрут в это время либо меньше десяти часов с планового окончания предыдущего маршрута';
				
			}
		} else
		if (isset($_POST["remove"])){
			$requests_repo->removeFacts($_POST["remove"]);
			$requests_repo->removeRequest($_POST["remove"]);
		}
		$routes = $routes_repo->getValidateRoutes();
		$assigned_routes = $requests_repo->assignedRoutes($id);
		$completed_routes = $requests_repo->completedRoutes($id);
		$users = $users_repo->getUser($id);
		$fio = $users[0]['last_name'].' '.$users[0]['first_name'].' '.$users[0]['middle_name'];
		$email = $users[0]['email'];
		$phone = $users[0]['phone'];
		$map_driver = $drivers_repo->whereIsDriversNow(0,$users[0]['id']);
		$map_route =  $routes_repo->getPointsOfRoute($map_driver[0]['route_id']);
		$show_route = array_map(function($el){
			$el['coordinate'] = explode(",",preg_replace('/\((.+)\)/', "$1", $el['coordinate']));
			return $el;
		;},$map_route);
		$show_driver['coordinate'] = explode(",",preg_replace('/\((.+)\)/', "$1", $map_driver[0]['coordinate']));
		$show_driver['hintContent'] = "<html><a href='../point/show.php?id=".$map_driver[0]['pid']."'>".$map_driver[0]['name']."</a></html>";
        $show_driver['iconCaption'] = $map_driver[0]["fio"];
        $show_driver['balloonContentBody'] = "<html><a href='../point/show.php?id=".$map_driver[0]['pid']."'>".$map_driver[0]['name']."</a></html>";
		include('../common/headers.php');
	?>


				<h1><?=$fio?></h1>
				<?=show_info(1,[ 
					'email' => $email,
					'phone' => $phone
				])?>
				</form>
				
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href='#assign'>Назначенные маршруты</a></li>
						<li><a data-toggle="tab" href="#passed">Пройденные маршруты</a></li>
						<li><a data-toggle="tab" href="#current_route">Текущее местоположение</a></li>
					</ul>
					<div class="tab-content">	
						<div id="assign" class="tab-pane fade in active">	
						<form method="POST" id="assign_route" name="assign_route">	
							<?=input_hidden('button1','button1',0) ?>
							<?=select("Выберите маршрут:",['type'=>'text','name'=>'route','id'=>'route'],$routes,'')?>
							<?=field("Дата и время",['type' => 'text', 'id'=>'datetimepicker1','name'=>'datetimepicker1','value'=>$_POST['datetimepicker1']], $errors['datetimepicker1']) ?>		
							<button type="submit" class="btn btn-primary"  name="add" id="add" value="add">Добавить</button>
						</form>
		
							<table class="table">
								<thead>
									<th>Маршрут</th>
									<th>Время старта</th>
									<th>Время финиша</th>
									<th>Статус</th>
								</thead>
								<tbody>
									<? foreach ($assigned_routes as $assigned_route) {?>
										<tr>
											<td><?= $assigned_route['name'] ?></td>
											<td><?= rdate($assigned_route['start_time'])?></td>
											<td><?= rdate($assigned_route['end_time'])?></td>
											<td>
												<?= $assigned_route['status'] ?>
												<?php if($assigned_route['status']=='request' ){ ?>
													<form method="POST" id="cancel_request_form" name="cancel_request_form" style="display:inline">
														<button type="submit" class="btn btn-primary btn-sm btn-danger" name="remove" id="remove" value="<?=$assigned_route[id]?>">Отмена</button>
													</form> 
												<?}?>
											</td>

										</tr>
									<? } ?>
								</tbody>
							</table>
						</div>
						  <div id="passed" class="tab-pane fade">	
							<table class="table">
								<thead>
									<th>Маршрут</th>
									<th>Время старта</th>
									<th>Время финиша</th>
									<th>Статус</th>
								</thead>
								<tbody>
									<? foreach ($completed_routes as $completed_route) {?>
										<tr>
											<td><?= $completed_route['name'] ?></td>
											<td><?= rdate($completed_route['start_time']) ?></td>
											<td><?= rdate($completed_route['end_time'] )?></td>
											<td><?= $completed_route['status'] ?></td>
										</tr>
									<? } ?>
								</tbody>
							</table>
						</div>
						<div id="current_route" class="tab-pane fade">
								<div class="row">
			 						<div id="map" style="width: 100%; height: 60%"></div>
								</div>
								
    							
						</div>
					</div>
	
		</div>
	<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
	<script src="../assets/js/maps/maps.js" type="text/javascript"></script>
	<script>
		$(document).ready(function(){    
			$('#datetimepicker1').datetimepicker();
			$('#assign_route').submit(function(e){
				e.preventDefault();
				$('.bg-danger').empty();
				$('#button1').val('0');
				var errors = {};
				if(!/^[\d]{4}\/[\d]{2}\/[\d]{2} [\d]{2}:[\d]{2}$/.test($('#datetimepicker1').val().trim())) errors['datetimepicker1'] = 'Выберите дату и время в календаре или введите в формате гггг/мм/дд чч:мм';
				if(Object.keys(errors).length==0){
					$('#button1').val('1');
					this.submit();
				}	else {
					for(key in errors){
						$('#'+key+'_err').text(errors[key]);
					}
				}

			});
			<?php if(count($map_driver)!=0){ ?>
				$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  					var target = $(e.target).attr("href"); // activated tab
  					 if(target=='#current_route') {
 					 	$('#map').empty();
 					 	make_map(<?=json_encode([$show_driver])?>,<?=json_encode([$show_route])?>,true);;
 					 }
				});
			
			<?php ;} ?>
		});
		
	</script>
	</body>
<html>