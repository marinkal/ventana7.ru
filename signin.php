<?php
	require_once('common/app_config.php');
	require_once('common/authorize.php');
	if(isset($_SESSION['user_id'])){
		if(in_array('manager', $_SESSION['roles'])) header('location: main.php');	else
		if(in_array('admin',$_SESSION['roles'])) header('location: admin/users.php'); else 
		if(in_array('driver',$_SESSION['roles'])) header('location: index.php');
	}
	include('common/headers.php');

?>
			
			<form method="POST">
				<div class="row">
					<div class="col-md-4"></div>
					<div class="col-md-4">
						<?= field("Логин:",['type'=>'text','name'=>'username','id'=>'username','value'=>$_POST["username"]]) ?>
						<?= field("Пароль:",['type'=>'password','name'=>'password','id'=>'password']) ?>
						<button type='submit' class="btn btn-primary" name="login">Войти</button>
					</div>
					<div class="col-md-4"></div>
				</div>
			</form>
		</div>
	</body>
</html>

