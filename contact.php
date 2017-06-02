<?php
	require_once('/common/app_config.php');
	require_once('/common/authorize.php');

	if(!isset($_SESSION['user_id']))header('location: /signin.php');
	unset($_SESSION['success']);
	unset($_SESSION['error']);
	$errors = ['email'=>'','phone'=>''];
	$id = $_SESSION['user_id'];
	$users_repo = new Nulpunkt\Yesql\Repository($conn, "sql/users.sql");
	
	if(isset($_POST['button1']) && $_POST['button1']==1){
		$errors = [];
		if(trim($_POST['email'])!==$user['email'] || trim($_POST['phone'])!==$user['phone']){
		if(!preg_match('/^[\w|\-|_|.]+@[A-Z|a-z]+\.[A-z|a-z]{2,3}$/',$_POST['email'])) $errors["email"]="Неверный формат email";
		if(!preg_match('/^8[\d]{10}$/',$_POST['phone'])) $errors["phone"] = "Введите номер в 11-значном формате 8XXXXXXXXXX";
		if(count($errors)==0)
			$users_repo->updateContacts(trim($_POST['email']),trim($_POST["phone"]),$id);
			$errors = ['email'=>'','phone'=>''];
		    $_SESSION['success'] = 'Изменения сохранены';
		} else $_SESSION['success'] = 'Изменения сохранены';
	}  
	$user = $users_repo->getUser($id);
	if(count($user)==0)die("Такой страницы не существует"); else $user = $user[0];

		include('/common/headers.php');



?>
			<h1>Обновить контактную информацию</h1>
			<form type="submit" method="POST" id="update_contacts_form">
				<?=input_hidden('button1','button1',0) ?>
				<?=field("Ваш email:",['type'=>'email','name'=>'email','id'=>'email','value'=>$user['email']],$errors['email'])?>
				<?=field("Ваш телефон:",['type'=>'text','name'=>'phone','id'=>'phone','value'=>$user['phone']],$errors['phone'])?>
				<button type="submit" class="btn btn-primary" value=0 >Сохранить изменения</button>
			</form>
		</div>
		<script type="text/javascript" src="assets/js/contact.js">	</script>
	</body>
</html>