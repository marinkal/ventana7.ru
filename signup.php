<?php
	require_once('common/authorize.php');
   	require_once('common/app_config.php');
   	if(isset($_SESSION['user_id'])) {
   		if(in_array('manager', $_SESSION['roles'])) header('location: main.php');
   			else
   				if(in_array('driver', $_SESSION['roles'])) header('location: index.php');
   					else 
   						if(in_array('admin', $_SESSION['admin'])) header('location: admin/users.php');
   	}
	$errors = [];
	$users_repo = new Nulpunkt\Yesql\Repository($conn, $_SERVER['DOCUMENT_ROOT']."/sql/users.sql");
	if(isset($_POST['button1']) && $_POST['button1']=="1"){
	$_POST = array_map(function($element){return trim($element);},$_POST);
	if(!preg_match('/^[A-Z|a-z][A-Z|a-z|0-9]+$/',$_POST['username']))$errors["username"] = "Имя пользователя может содержать только цифры и буква латинского алфавита. Первый символ только буква";
		 else {
		    	$user = $users_repo->getUserByUsername($_POST['username']);
				if(count($user)!=0) $errors['username'] = 'Имя пользователя '.$_POST['username'].' уже занято. Придумайте другое';
		 };
		if(!preg_match('/^[\w|\-|_|.]+@[A-Z|a-z]+\.[A-z|a-z]{2,3}$/',$_POST['email'])) $errors["email"]="Неверный формат email";
		if(mb_strlen($_POST['first_name'],"UTF-8")<2) $errors["first_name"] = "Имя должно содержать как минимум два символа";
		if(mb_strlen($_POST['last_name'],"UTF-8")<2) $errors["last_name"] = "Фамилия должна содержать как минимум два символа";
		if($_POST["middle_name"]!==''){
			if(mb_strlen($_POST['middle_name'],"UTF-8")<4)  $errors['middle_name'] = "Отчество должно содержать как минимум 4 символа";
		};
			if(!preg_match('/^8[\d]{10}$/',$_POST['phone'])) $errors["phone"] = "Введите номер в 11-значном формате 8XXXXXXXXXX";
			if($_POST['password']!==$_POST["password_confirm"]) $errors["password_confirm"] = "Пароли не совпадают"; else	
			if($_POST['password']==$_POST['username']|| $_POST['password']==$_POST['phone'] || 
				$_POST['password']==$_POST['email']) $errors['password'] = 'Пароль не должен совпадать с email, именем пользователя или номером телефона';
							
			if(count($errors)==0){
				$errors = ['username' => '','password' => '', 'password_confirm' => '', 'email' => '', 'last_name' => '','first_name' => '', 'middle_name' => '', 'phone' => ''];
     			$id = $users_repo->createUser($_POST['username'],password_hash($_POST['password'],PASSWORD_DEFAULT),$_POST["email"],$_POST['last_name'],$_POST['first_name'],$_POST['middle_name'],$_POST['phone']);
   			 	$users_repo->addRoleToUser($id,1);
   			 	$_SESSION['user_id'] = $id;
   			 	$_SESSION['username'] = $_POST['username'];
   			 	$_SESSION['first_name'] = $_POST['first_name'];
   			 	$_SESSION['last_name'] = $_POST['last_name'];
   			 	$_SESSION['roles'] = ['driver'];
   			 	header('location: /');
			} 	
   			
		}
		include('common/headers.php');
	?>
			<form method="POST" id="form_signup" name="form_signup">
				<?=input_hidden('button1','button1',0) ?>
				<?=field("Имя пользователя*:",['type'=>'text','name'=>'username','id'=>'username','value'=>$_POST['username']],$errors['username']) ?>
				<?=field("Пароль*:",['type'=>'password','name'=>'password','id'=>'password'],$errors['password']) ?>
				<?=field("Подтверждение пароля*:",['type'=>'password','name'=>'password_confirm','id'=>'password_confirm'],$errors['password_confirm']) ?>
				<?=field("Email*:",['type'=>'email','name'=>'email','id'=>'email','value'=>$_POST['email']],$errors['email']) ?>
				<?=field("Фамилия*:",['type'=>'text','name'=>'last_name','id' => 'last_name','value'=>$_POST['last_name']],$errors['last_name']) ?>
				<?=field("Имя*:",['type'=>'text','name'=>'first_name','id' => 'first_name','value'=>$_POST['first_name']],$errors['first_name']) ?>
				<?=field("Отчество:",['type'=>'text','name'=>'middle_name','id' => 'middle_name','value'=>$_POST['middle_name']],$errors['middle_name']) ?>
				<?=field("Телефон*:",['type'=>'text','name'=>'phone','id' => 'phone','value'=>$_POST['phone']],$errors['phone']) ?>     			
     			<?=submit_button('Зарегистрироваться','sign_up','btn-primary btn-lg')?>
   	    	</form>				
		</div>
	<script src='assets/js/signup.js' type="text/javascript">	</script>
	</body>
</html>