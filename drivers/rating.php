<?php	
		require_once('../common/authorize.php');
	    require_once('../common/app_config.php');
	    if(!isset($_SESSION['user_id'])) header('Location: ../signin.php'); 
	    if(!in_array('manager', $_SESSION['roles'])) die('Ошибка 403. Доступ запрещен');
	    $drivers_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/drivers.sql");
	    $ratings = $drivers_repo->rating();
	    include('../common/headers.php');
?>
<h1>Рейтинг водителей</h1>
<table class="table">
	<thead>
		<tr>
			<td>№</td>
			<td>ФИО</td>
			<td>Среднее время опоздания</td>
		</tr>
	</thead>
	<tbody>
		<?php 
			$i=1;
			foreach ($ratings as $rating) {
				
			 ?>
			<tr>
			<td><?=$i++?></td>
			<td><?=$rating["fio"]?></td>
			<td><?=preg_replace('/(\d+:\d{2}:\d{2})(\.\d+)?/','$1', $rating["avg_time"])?></td>
			</tr>
		<?php } ?>
	</tbody>

</table>
</div>
</body>
</html>