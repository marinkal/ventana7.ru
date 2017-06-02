<?php
		require_once('../common/authorize.php');
		require_once('../common/app_config.php');
		if(!isset($_SESSION['user_id']))header('location: /signin.php');
		if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403. Доступ запрещён');
		$_SESSION['error'] = '';
		$id=$_GET['id'];
		$routes_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/routes.sql");
		$point = $routes_repo->getPoint($id);
		if(count($point)==0) die('Такой страницы не существует');
		if(isset($_POST['remove_point'])) {
			if(count($routes_repo->getRoutesByPoint($_POST['remove_point']) )==0){

				$routes_repo->removePoint($_POST['remove_point']);
				header('location: /points');
			}else 
		{
			$_SESSION['error'] = 'Невозможно удалить точку. Точка используется в маршрутах';
		} }
		$routes = $routes_repo->getRoutesByPoint($id);
		$coordinate = preg_replace('/\((.*)\)/', '[$1]',$point[0]['coordinate']);
		include('../common/headers.php');
?>
		<form  class="form-inline" method = "POST">
				<div class="form-group">
					<h1>Точка: <?=$point[0]['name']?></h1>
					<button class="btn btn-danger" type="submit" name="remove_point" value="<?=$id?>">Удалить точку</button>
				</div> 
		</form>

		<div class="row">
			<div class="col-md-6">
				<p>На карте:</p>
				<div id="map" style="width: 600px; height: 400px"></div>
			</div>
			<div class="col-md-1"></div>
			<div class="col-md-5">
				<p>Используется в маршрутах: </p>
				<ul class="list-group">
				<?php foreach ($routes as $route) { ?>
					<li class="list-group-item"><a href="/route/show.php?id=<?=$route[id]?>"><?=$route['name'] ?></a></li>
				<?php } ?>
				</ul>
			</div>
		</div>

	
	
		</div>
		<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
	<script type="text/javascript">
				ymaps.ready(init);
		    var myMap;
		    var myPlacemark;

    		function init(){     
		    myMap = new ymaps.Map("map", {
		            center: <?=$coordinate?>,
		            zoom: 16
		        })
		    myPlacemark = new ymaps.Placemark(<?=$coordinate?>, {
	            hintContent: "<?=$point[0]['name']?>",
	            iconCaption: "<?=$point[0]['name']?>"
        		}, {
            		preset: 'islands#blueDotIconWithCaption'
        		});
		      myMap.geoObjects.add(myPlacemark);
		};
	</script>
	</body>
</html>