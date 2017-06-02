<?php
		require_once('../common/authorize.php');
		require_once('../common/app_config.php');
		require_once('../common/maps.php');
		if(!isset($_SESSION['user_id']))header('location: /signin.php');
		if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403. Доступ запрещён');
		$_SESSION['error'] = '';
		$route_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/routes.sql");
		$request_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/requests.sql");
		$id = $_GET['id'];
		$check = $route_repo->getRoute($id);
		$days = make_days(9);
	
	//	$_POST['days'] = 0;
 		if(count($check)==0) die('Такой страницы не существует');
		if($_POST["remove_route"]){
			//Нет в заявках?
			if(count($request_repo->getActiveRequests($_POST["remove_route"]))!=0){
				$_SESSION['error'] = 'Маршрут нельзя удалить. Он используется в выполнных или текущих заявках';
			}
				else
			{
				//Удаляем заявки в статусе request
				$request_repo->removeRequestsByStatus($_POST['remove_route'],'request');
				$route_repo->removePointsOfRoute($_POST["remove_route"]);
				$route_repo->removeRoute($_POST["remove_route"]);
				header('location: ../routes');
			}
		} 
		else 
		if(isset($_POST["expected_time"])){
			if(count($request_repo->getActiveRequests($_POST['add_point_to_route']))!==0){
				$_SESSION['error'] = 'Нельзя менять список точек маршрута. Он используется в выполнных или текущих заявках';
			} else {
			  $total_time = $_POST['days'].' day '.$_POST['expected_time'];
			
			  $lastPointInRoute =  $route_repo->getLastPointInRoute($_POST['add_point_to_route'])[0]; 
			  if(preg_match('/^\d\d:\d\d:\d\d$/',$lastPointInRoute['expected_time'])){
			  		$lastPointInRoute['expected_time'] = '0 day '.$lastPointInRoute['expected_time'];
			  }
			  if($total_time>$lastPointInRoute['expected_time']
			  		&& $_POST["point_id"]!=$lastPointInRoute['point_id']){
			  	$route_repo->addPointToRoute($id,$_POST['point_id'],$total_time);
			  } else echo "1. Ожидаемое время точки должно быть больше предшедствующих";
			  	 
			  }
		}
		  
		 else
		if(isset($_POST['remove_point_from_route'])) {
			if(count($request_repo->getActiveRequests($id))!==0){
				$_SESSION['error'] = 'Нельзя менять список точек маршрута. Он используется в выполнных или текущих заявках';
			}

		    else{
				$route_repo->removePointFromRoute($_POST['remove_point_from_route'],$id);
				$route_repo->removeRoute($_POST['remove_point_from_route']);
			}
		}
		
		$route = $route_repo->getRoute($id);
		$points = $route_repo->getPointsOfRoute($id);
		$pointsOutOfRoute = $route_repo->getPointsOfRouteWithoutLast($id);
		$show_route = [];
		foreach ($points as $point) {
			$show_route[]['coordinate'] = explode(",",preg_replace('/\((.*)\)/', '$1', $point['coordinate']));
		}
		
		$show_points = array_map(function($el){
			$new_el['coordinate'] = explode(",",preg_replace('/\((.*)\)/', '$1', $el['coordinate']));
			$new_el['hintContent'] = "<html><a href='../point/show.php?id=$el[id]'>$el[name]</a></html>";
        	$new_el['iconCaption'] = $point["name"];
        	$new_el['balloonContentBody'] = "<a href='../point/show.php?id=$el[id]'>$el[name]</a>";
        	return $new_el;
		},$points);	
		include('../common/headers.php');
	?>
			<form  class="form-inline" method = "POST">
				<div class="form-group">
					<h1>Маршрут: <?=$route[0]['name']?></h1>
					<button class="btn btn-danger" type="submit" name="remove_route" value="<?=$id?>">Удалить маршрут</button>
				</div> 
			
			</form>
			<ul class="nav nav-tabs" id="myTabs">
				<li class="active"><a data-toggle="tab" href='#route_tab'>Общее</a></li>
				<li ><a data-toggle="tab" href="#map_tab">Карта</a></li>
			</ul>
			<div class="tab-content">
				<div id="route_tab"  class="tab-pane fade in active">
					<p>Контрольные точки</p>
					<form class="form" method="POST">
					<table class="table table-stripped">
							<thead>
								<th></th>
								<th>Широта</th>
								<th>Долгота</th>
								<th>Время</th>
								<th></th>
							</thead>
							<tbody>
								<?php 
									
									foreach ($points as $point) { 
										preg_match('/([\d]+\.[\d]+),([\d]+\.[\d]+)/', $point['coordinate'],$matches);
									?>
									<tr>
										<td><?=$point['name']?></td>
										<td><?=$matches[1]?></td>
										<td><?=$matches[2]?></td>
										<td>
											<?=rtime($point['expected_time'])?>
											<?php if($point['expected_time']!='00:00:00'){?>
												<a href='point/edit.php?rp_id=<?=$point['rp_id']?>'>(изменить)</a>
											<?}?>

										</td>
										<td>
										<?php if($point['expected_time']!='00:00:00'){?>
											
												<?=button('X',['type'=>'submit','class'=>'btn btn-danger','name'=>'remove_point_from_route','value'=>$point['id']]) ?>
										<?}?>
										</td>
									</tr>
								<?}?>
							<tbody>
					</table>
				    </form>
					<form  method = "POST">
							<?=select("Точка:",['name'=>'point_id','id'=>'point_id'],$pointsOutOfRoute,0)?>
							<?=select('Ожидаемое время прибытия (дни):',['name'=>'days','id'=>'days'],$days,$_POST['days'])?>
							<?=field("Ожидаемое время прибытия (часы:минуты):",['type'=>'time','name'=>'expected_time','id'=>'expected_time'])?>
							<?=button('Добавить к маршруту',['type'=>'submit','class'=>'btn btn-primary','name'=>'add_point_to_route','value'=>$id])?>
					</form>
				</div>
			
				<div id="map_tab"  class="tab-pane fade">
				 	<div id="map" style="width:100%; height:84%"></div>
				</div>
			</div>
		</div>	
		<?php if(count($points)>1){?>
			<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    		<script src="../assets/js/maps/maps.js" type="text/javascript"></script>
    		<script type="text/javascript">
    		$(document).ready(function () {
    			make_map(<?=json_encode($show_points)?>,<?=json_encode([$show_route])?>);;
		  		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  					var target = $(e.target).attr("href"); // activated tab
   					 if(target=='#map_tab') {
   					 	$('#map').empty();
   					 	//myMap.container.fitToViewport();
   					 	make_map(<?=json_encode($show_points)?>,<?=json_encode([$show_route])?>);;
   					 }
				});

			})
			  
			
			
    			
    		</script>



    	
    	<?}?>
	</body>
</html>