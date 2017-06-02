<?php
	require_once('../common/app_config.php');
	require_once('../common/authorize.php');
	if(!isset($_SESSION['user_id'])) header('location: signin.php'); else
		if(!in_array('admin', $_SESSION['roles'])) die('Ошибка 403.Доступ запрещён');
	
	$users_repo = new Nulpunkt\Yesql\Repository($conn, "../sql/users.sql");
	$users=$users_repo ->getUsers();
	include('../common/headers.php');
?>
	<h1>Список пользователей системы</h1>
	<form method="GET" action="new_user.php">
		<button type="submit" class="btn btn-primary" value="">Новый пользователь</button>
	</form>
	<table class="table">
		<thead>
			<tr>
				<th> ФИО </th>
				<th> Имя пользователя </th>
				<th> email </th>
				<th> Телефон </th>
				<th> Роли пользователя </th>
				<th> </th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($users as $user) {?>
				<tr>
					<td><?=$user['last_name'].' '.$user['first_name'].' '.$user['middle_name'] ?></td>
					<td><?=$user['username'] ?></td>
					<td><?=$user['email'] ?></td>
					<td><?=$user['phone'] ?></td>
					<td><?=$user['roles'] ?></td>
					<td></td>
				</tr>

			<?php ;} ?>
		</tbody>
	</table>
	</div>
	</body>
</html>