<?php
		require_once('../common/app_config.php');
		require_once('../common/authorize.php');

		$request_id = $_GET['request_id'];
		$rp_id = $_GET['rp_id'];
		if(!isset($_SESSION['user_id']))header('location: /signin.php');
		if(!in_array('manager', $_SESSION['roles'])) die ('Ошибка 403. Доступ запрещён');
		$requests_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/requests.sql");
		$request = $requests_repo->getRequest($request_id)[0];
		if(count($request)==0)die("Такой страницы не существует");
		$rp = $requests_repo->getRP($rp_id);
		if(count($rp)==0)die("Такой страницы не существует");
		if(isset($_POST['save'])){
			if(isset($_POST['is_good_reason'])){
				$is_good_reason = 1;
				$comment = trim($_POST['comment'])==""?NULL:$_POST['comment'];
			} else{
				$is_good_reason = '0';
				$comment = NULL;
			}
			$requests_repo->saveReason($is_good_reason,$comment,$rp_id,$request_id);
			header("location: show.php?id=$request_id");
		}
		$facts = $requests_repo->getRequestInfo($request_id);
		print_r($facts);
		$point = array_shift(array_filter($facts, function($el) use($rp_id){
			return $el['rpid']==$rp_id;}));	
		include('../common/headers.php');
		print_r($key_point);

?>
		<form method="POST">
			<?=show_info(1,[
				'заявка №' => $request['id'],
				'Точка' => $point['name'],
				'Водитель' => $request['driver_id'],
				'Прибытие' => htable(['План'=>rdate($point['plan_time']),
										'Факт'=>rdate($point['fact_time']),
										'Опоздание'=>$point['late']!='00:00:00'?$point['late']:'—'])

			])?>
			<?=checkbox('Опоздание по уважительной причине',['id'=>'is_good_reason','name' => 'is_good_reason'],
				$point['is_good_reason']==1?'checked':'')?>
			<?=textarea('Причина опоздания:',['id'=>'comment','name'=>'comment'],$point['comment'],$_POST['error'])?>
			<button type="submit" class="btn btn-primary" name="save">Записать</button>
		</form>

		</div>
	</body>
</html>