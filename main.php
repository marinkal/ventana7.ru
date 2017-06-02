<?php
		require_once('common/app_config.php');
		require_once('common/authorize.php');
		require_once('common/maps.php');
		if(!isset($_SESSION['user_id']))header('location: /signin.php');
		if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403. Доступ запрещён');
		$id = $_GET['id'];
		$users_repo = new Nulpunkt\Yesql\Repository($conn, "sql/users.sql");
		$drivers = $users_repo->getDrivers();
		$routes_repo = new Nulpunkt\Yesql\Repository($conn, "sql/routes.sql");
		$routes = $routes_repo->getRoutes();
		$drivers_repo = new Nulpunkt\Yesql\Repository($conn, "sql/drivers.sql");
		$requests_repo = new Nulpunkt\Yesql\Repository($conn, "sql/requests.sql");
		//$map_routes1 = $drivers_repo->getActiveRoutesByManager($_SESSION['user_id']);

		//$map_routes = $routes_repo->getRoutesFullInfo();
		$map_routes = $memcache->get('getRoutesFullInfo');
		$map_drivers = $drivers_repo->whereIsDriversNow($_SESSION['user_id'],0);
		$show_routes = [];
		foreach ($map_routes as $map_route) {
			$new_el = [];
		    $new_el['coordinate'] = explode(",",preg_replace('/\((.+)\)/', "$1", $map_route['coordinate']));
			$show_routes[$map_route['route_id']][] = $new_el;
			//$show_routes[$map_route['route_id']][] = explode(",",preg_replace('/\((.+)\)/', "$1", $map_route['coordinate']));
		}
		//print_r($show_routes);
		$show_drivers = [];
		$pids = []; //тут храним точки, это нужно чтобы показать всех водителей
		foreach ($map_drivers as $el) {
			$new_el = [];
			if(!in_array($el['pid'], $pids)){
				$pids[] = $el['pid'];
				$new_el['hintContent'] = "<a href='../point/show.php?id=$el[pid]'>$el[name]</a>";
        		$new_el['iconCaption'] = $el["fio"];
        		$new_el['balloonContentBody'] = "<a href='../driver/show.php?id=$el[uid]'>$el[fio]</a>";
        		$new_el['coordinate']= explode(",",preg_replace('/\((.+)\)/','$1', $el['coordinate']));
        		$show_drivers[] = $new_el;
			}
			else {
				$index = array_search($el['pid'], $pids);
				$show_drivers[$index]['balloonContentBody'] .="<br/><a href='../driver/show.php?id=$el[uid]'>$el[fio]</a>";
				$show_drivers[$index]['iconCaption'] .= "<br/>".$el["fio"];
			}
			
		}


        $complete_requsts = $requests_repo->getCompleteRequestsFullInfo();
		include('common/headers.php');
	
	?>
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href='#map_tab'>Карта</a></li>
				<li><a data-toggle="tab" href="#drivers_tab">Водители</a></li>
				<li><a data-toggle="tab" href="#routes_tab">Маршруты</a></li>
				<li><a data-toggle="tab" href="#complete_requests_tab">Выполненные заявки</a></li>
			</ul>
			<div class="tab-content">
			<div id="map_tab"  class="tab-pane fade in active">
			 	<div id="map" style="width:100%; height:84%"></div>
			</div>
		
				<div id="drivers_tab"  class="tab-pane fade">
						<ul class="list-group">
						<?php foreach ($drivers as $driver) { ?>
							<li class="list-group-item"><a href ="driver/show.php?id=<?=$driver[id]?>"><?=$driver["last_name"]." ".$driver["first_name"]." ".$driver["middle_name"]?></a></li>
								
						<?}?>
					</ul>
				</div>
				<div id="routes_tab"  class="tab-pane fade"> 
					<a class="btn btn-primary" href="/route/new.php">Создать новый</a>
					<ul class="list-group">
					<?php foreach ($routes as $route) { ?>								
								<li class="list-group-item"><a href ="route/show.php?id=<?=$route[id]?>"><?=$route["name"]?></a></li>
					<?}?>
					</ul>

			    </div>
			    <div id="complete_requests_tab" class="tab-pane fade">
			    	<div id = "show_complete_requests">
			    		<form name="omplete_requests_form" method="POST">
			    		<table class="table" border=1>
			    			<thead>
			    				<tr>
			    					<th>№ заявки:
			    						<input type="text" id="req_num" value=""/>
			    					</th>
			    					<th>
			    						ФИО водителя:
			    						<select id="fio_filter">
			    						  <option value=''>-------</option>
			    						  <?php foreach ($drivers as $driver) {?>
			    						  	<option value='<?="$driver[last_name] $driver[first_name] $driver[middle_name]"?>'><?="$driver[last_name] $driver[first_name] $driver[middle_name]"?></option>
			    						 <?  } ?>
			    						</select>
			    					</th>
			    					<th>Стартовое время</th>
			    					<th>Фактическое время финиша</th>
			    					<th>Плановое время финиша</th>
			    					<th>Опоздание</th>
			    					<th>Действия</th>
			    				</tr>
			    			</thead>
			    			<tbody>
			    				<?php foreach ($complete_requsts as $complete_requst) { ?>
			    					<tr>
			    						<td class="req_num"><?=$complete_requst['request_id'] ?></td>
			    						<td class="fio"><?=$complete_requst['fio'] ?></td>
			    						<td><?=rdate($complete_requst['start_time']) ?></td>
			    						<td><?=rdate($complete_requst['end_time']) ?></td>
			    						<td><?=rdate($complete_requst['plan_time'])?></td>
			    						<td><?=$complete_requst['late']!='00:00:00'?rtime($complete_requst['late']):'—'?></td>
			    					    <td><a class="btn btn-success" href="/request/show.php?id=<?=$complete_requst['request_id']?>"> Просмотр</a></td>	
			    					</tr>
			    				<?}?>
			    			
			    			
			    			</tbody>
			    		</table>
			    		</form>
			    	</div>
				</div>
		
			</div>
	
		</div>
		<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
		<script src="assets/js/maps/maps.js" type="text/javascript"></script>
		<script>make_map(<?=json_encode($show_drivers) ?>,<?=json_encode($show_routes)?>);</script>
    	<script src="assets/js/main.js" type="text/javascript"></script>
	</body>
		</html>