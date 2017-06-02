<?php
		require_once('../common/app_config.php');
		require_once('../common/authorize.php');

		$id = $_GET['id'];
		if(!isset($_SESSION['user_id']))header('location: /signin.php');
		if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403. Доступ запрещён');
		$requests_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/requests.sql");
		$request = $requests_repo->getRequest($id)[0];
		if(count($request)==0)die("Такой страницы не существует");
		$facts = $requests_repo->getRequestInfo($id);
		include('../common/headers.php');
?>
		<h1>Заявка № <?=$request['id']?></h1>
		<?=show_info(1,[
			'Статус' => $request['status'],
			'Менеджер' => $request['manager_fio'],
			'Водитель' => a_show('driver',$request['driver_id'],$request['driver_fio']),
			'Маршрут' => a_show('route',$request['route_id'],$request['name'])

		])?>
		<h2>Ход выполнения заявки</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Точка</th>
					<th>Плановое время</th>
					<th>Фактическое время</th>
					<th>Опоздание</th>
					<th>Причина</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($facts as $fact) {?>
					<tr>
						<td><?=$fact['name']?></td>
						<td><?=rdate($fact['plan_time'])?></td>
						<td><?=rdate($fact['fact_time'])?></td>
						<? if($fact['late']!='00:00:00'){?>
							<td><?=rtime($fact['late'])?></td>
							<td><?=$fact['comment']?>(<?=$fact['is_good_reason']==1?'уваж.':'не уваж.'?>)</td>
							<td><a href="reason.php?rp_id=<?=$fact['rpid']?>&request_id=<?=$fact['reqid']?>">Указать причину</a></td>
						<?} else {?>
						<td>—</td>
						<td></td>
						<td></td>
			
						<?}?>

						

					</tr>
				<?}?>

			</tbody>
		</table>
		
		</div>
	</body>
</html>