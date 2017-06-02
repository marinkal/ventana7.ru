<?php
	require_once('../../common/app_config.php');
	require_once('../../common/authorize.php');
	
	if(!isset($_SESSION['user_id']))header('location: /signin.php');
	if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403. Доступ запрещён');
	$rp_id = $_GET['rp_id'];
	$routes_repo = new Nulpunkt\Yesql\Repository($conn, "../../sql/routes.sql");
	$rps = $routes_repo->getRoutePoint($rp_id);
	if(count($rps)==0)die('Такой страницы не существует');
	$rp = $rps[0];
	if($rp['expected_time']='00:00:00') die('Такой страницы не существует');

	if(isset($_POST['button1']) && $_POST['button1']==1){
		  $errors = [];
		  $total_time = $_POST['days'].' day '.$_POST['expected_time'];
		  $prev_points = $routes_repo->getPrevPoint($rp['route_id'],$rp['expected_time']);
		  $next_points = $routes_repo->getNextPoint($rp['route_id'],$rp['expected_time']);
		  $diff1 = -1;
		  $diff2 = 1;
		  if(count($prev_points)!=0){
		  	$diff1 = $routes_repo->compareIntervals($prev_points[0]['expected_time'],$total_time)[0]['diff'];
		  }
		//  print_r($prev_points);
		  if(count($next_points)!=0){
		 	$diff2 = $routes_repo->compareIntervals($next_points[0]['expected_time'],$total_time)[0]['diff'];
		  }  	
		//  echo $diff1; echo " ".$diff2;
		  $start = "";
		  	$end = "";
		  if($diff1>=0) $start = "от ".$prev_points[0]['expected_time']." ";
		  if($diff2<=0) $end = "до ".$next_points[0]['expected_time'];
		  	if(strlen($start)!=0 || strlen($end)!=0) $errors['expected_time'] = "Введите время в интервале $start $end ";

		  	if(count($errors)==0){
		  		$routes_repo->updateTime($total_time,$rp_id);
		  		$rps = $routes_repo->getRoutePoint($rp_id);
		  		$rp = $rps[0];
		  		$errors['expected_time'] = '';
		  		header('location: ../show.php?id='.$rp['route_id']);
		  	} 
		
		
		}
	$days = make_days(9);
	$route = $routes_repo->getRoute($rp['route_id'])[0];
	$point = $routes_repo->getPoint($rp['point_id'])[0];

	include('../../common/headers.php');

?>
		<?=show_info(1,[
			'Точка' => $point['name'],
			'Маршрут' => $route['name'],
		])?>
	
		<form  method = "POST" id="change_time_form">
					<?=input_hidden('button1','button1',0) ?>
					<?=select('Ожидаемое время прибытия (дни):',['name'=>'days','id'=>'days'],$days,$rp['days'])?>
					<?=field("Ожидаемое время прибытия (часы:минуты):",['type'=>'time',
						'name'=>'expected_time','id'=>'expected_time','value'=>$rp['time']], $errors['expected_time'])?>
					<?=button('Сохранить изменения',['type'=>'submit','class'=>'btn btn-primary'])?>
		</form>

		</div>
	<script type="text/javascript">
		$(document).ready(function(){
				$('#change_time_form').submit(function(e){
					e.preventDefault();
					$("button1").val(0);
					$('.bg-danger').empty();
				var errors = {};
				if(!/([01][\d]|2[0-3])\:[0-5][\d](\:[0-5][\d])?$/.test($('#expected_time').val().trim())) errors['expected_time'] = 'Введите время в формате чч:мм';
				if(Object.keys(errors).length==0){
					$('#button1').val(1);
					this.submit();

				}
				else{
					for(key in errors){
							$('#'+key+'_err').text(errors[key]);
					}
				}	
				});
			})
	</script>
	</body>
</html>