<html>	
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Сервис логистики</title>
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.0.min.js"></script>	
		<!-- Latest compiled and minified CSS -->
		 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		 <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<!--<script type="text/javascript" src="../vendor/javascript/driver_show.js"></script>	-->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<!--datetimepicker-->
		<link rel="stylesheet" type="text/css" href="../vendor/css/components/jquery.datetimepicker.css"/ >
		<script src="../vendor/javascript/components/jquery.datetimepicker.full.min.js" ></script>	 
	</head>
	<body>
	<div class="container">
		<nav class="navbar navbar-default">
			<ul class="nav navbar-nav">
			<?php
				if(isset($_SESSION['roles'])){
					if(in_array('manager',$_SESSION['roles'])){?>
						<li><a href="/main.php">Карта</a></li>
	        			<li><a href="/routes">Маршруты</a></li>
	        			<li><a href="/points">Точки маршрутов</a></li>
	        			<li><a href="/drivers/rating.php">Рейтинг водителей</a></li>

					<?php }
					if(in_array('admin', $_SESSION['roles'])){?>
						<li><a href="/admin/users.php">Пользователи</a></li>
					<?}?>
				<?}?>

	        </ul>
	        <ul class="nav navbar-nav navbar-right">
	         <?php if (isset($_SESSION['user_id'])){?>
	         <li><a href="/contact.php">Обновить контакты</a></li>
        	 <li><a href="/signout.php"><?=$_SESSION['last_name']." ".$_SESSION['first_name'] ?> (Выйти) </a></li>
        	 <?} else {?>
        	 	<li><a href="/signup.php">Регистрация</a></li>
        	 	<li><a href="/signin.php">Войти </a></li>
        	 <?}?>
        	</ul>
	     </nav>
	     <?php if(isset($_SESSION['error']) && $_SESSION['error']!==''){?>
		     <div>
		     <p class="bg-danger"><?=$_SESSION['error'] ?></p>
		 	</div>
		 <?} 
		 	if(isset($_SESSION['success']) && $_SESSION['success']!==''){?>
		     <div>
		     <p class="bg-success"><?=$_SESSION['success'] ?></p>
		 	</div>
		 <?}
		 unset($_SESSION['error']);
		 unset($_SESSION['success']);
		?>