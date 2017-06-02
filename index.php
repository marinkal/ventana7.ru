<?php 
	require_once($_SERVER['DOCUMENT_ROOT'].'/common/authorize.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/common/app_config.php');
	if(!isset($_SESSION['user_id'])) header('location: signin.php');
		else
			if(in_array('manager',$_SESSION['roles'])) header('location: main.php');
				else
					if(in_array('admin', $_SESSION['roles'])) header('location: admin/users.php');
	if(!in_array('driver',  $_SESSION['roles'])) die('Ошибка 403. Доступ запрещён');
	$drivers_repo = new Nulpunkt\Yesql\Repository($conn, "sql/drivers.sql");
	$requests_repo = new Nulpunkt\Yesql\Repository($conn, "sql/requests.sql");
	$routes_repo = new Nulpunkt\Yesql\Repository($conn, "sql/routes.sql");
	$id = $_SESSION['user_id'];

	//Ближайший по времени назначенный маршрут, то есть маршрут, дата которого больше чем сейчас + 10 часов



	if(isset($_POST['next_button'])){
			echo "dfdf";
			//Определяем следующую точку
			//Выяснем какая это точка: последняя или нет
			//Если обычная: обновляем facts
			//Конечная: обновляем facts а затем меняем статус на complete
			$current_request = $drivers_repo->whereIsDriversNow(0,$id);
			$current_request = $current_request[0];
			//print_r($current_request); echo "oo<br>";
			$next_points=$requests_repo->getNextPointInRoute($current_request['expected_time'],$current_request['request_id']);
			$next_point = $next_points[0];
			$now = date_create_from_format('Y-m-d H:i:s',date("Y-m-d H:i:s"));
			$date=date_create_from_format('Y-m-d H:i:s',$current_request['start_time']); 
			$diff = date_diff($now,$date)->format('%d day %h:%i:%s');
			$requests_repo->updateFactTime($diff,$next_point['rp_id'],$next_point['request_id']);
			//print_r(count($next_points));
			if(count($next_points)==1) $requests_repo->updateStatus('complete',$next_point['request_id']);
		}
	$current_request = $drivers_repo->whereIsDriversNow(0,$id);
			if(count($current_request)==0){
			$plan_request_points = [];
			$next_route = $requests_repo->getNextRoute($id,date('Y-m-d H:i:s'));
			if(count($next_route) == 1){
			$request_id = $next_route[0]['request_id'];
			$plan_request_points = $requests_repo->planRequest($id,$request_id);
			if(isset($_POST['start_button'])){
				$date = date_create_from_format('Y-m-d H:i:s',$next_route[0]['start_time']);  
				$now = date_create_from_format('Y-m-d H:i:s',date("Y-m-d H:i:s"));
				$diff = date_diff($now,$date)->format('%d day %h:%i:%s');
				$rp_id = $requests_repo->getStartPoint($request_id)[0]['rp_id'];
				$requests_repo->updateStatus('process',$request_id);
				$requests_repo->updateFactTime($diff,$rp_id,$request_id);
				header('Refresh: 0;URL=');
			}

		}	
	
	}	else {
			$flag = 1;
			$current_request = $current_request[0];
			$route = $routes_repo->getRoute($current_request['route_id'])[0];
			//print_r($current_request);

		
	}


	
	include('/common/headers.php');
	if ($flag==1){ ?>
			<form method="POST">
			<?=show_info(3,['Маршрут' => $route['name'],
				'Последняя контрольная точка' => $current_request['name'],
				'Действия' =>submit_button('Следующая контрольная точка','next_button','btn-primary')]);?>;
			
			<div id='map'></div>
			</form>


		<?php } else {
			 if(count($next_route)==1){?>

			 <form method="POST">

			<?=show_info(3,['Ближайший маршрут' => $next_route[0]['rname'],
					'Время старта' => rdate($next_route[0]['start_time']),
					'План' => simple_table(['Точка','Плановое время'],
						$plan_request_points,
						['pname','plan_time']),
						'Действия' => submit_button('Приступить','start_button','btn-success',$next_route[0]['request_id'])
					]);
			}
			?>
			</form>
			<?php
		}
		?>
	
		</div>
	<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		ymaps.ready(init);
		function init() {
		    var geolocation = ymaps.geolocation,
		        myMap = new ymaps.Map('map', {
		            center: [55, 34],
		            zoom: 10
		        }, {
		            searchControlProvider: 'yandex#search'
		        })
    geolocation.get({
        provider: 'yandex',
        mapStateAutoApply: true
    }).then(function (result) {
        console.log('Первый метод:');
        console.log(result.geoObjects);
    });

    geolocation.get({
        provider: 'browser',
        mapStateAutoApply: true
    }).then(function (result) {
        console.log('Второй метод:');
        console.log(result.geoObjects);
    });
}

	})
	</script>>
	</body>
</html>