$(document).ready(function(){

		$("#form_signup").submit(function(e){
		    e.preventDefault();
		    $('#button1').val('0');
			$('.bg-danger').empty();
			var errors = {};
			if(!/^[A-Z|a-z][A-Z|a-z|0-9]+$/.test($('#username').val().trim())){
				errors["username"] = "Имя пользователя может содержать только цифры и буква латинского алфавита. Первый символ только буква";
			} else {
				$.ajax({url: 'ajax/unique_username.php',
					data: "username="+$('#username').val().trim(),
					async: false,
					success: function(result){
					
						if(result==1){
							errors["username"] = 'Имя пользователя ' + $('#username').val().trim() + ' уже занято. Придумайте другое';
						}
					}
				});
			}
			
			if(!/^[\w|\-|_|.]+@[A-Z|a-z]+\.[A-z|a-z]{2,3}$/.test($('#email').val().trim())) errors['email'] = 'Неверный формат email';
			if($('#first_name').val().trim().length<2) errors['first_name'] = "Имя должно содержать как минимум два символа";
			if($('#last_name').val().trim().length<2) errors["last_name"] = "Фамилия должна содержать как минимум два символа";
			if($('#middle_name').val().trim()!=="")
				if($('#middle_name').val().trim().length<4) errors["middle_name"] = "Отчество должно содержать как минимум 4 символа";
			if(!/^8[\d]{10}$/.test($('#phone').val().trim())) errors['phone'] = "Введите номер в 11-значном формате 8XXXXXXXXXX";
			if($('#password').val().trim().length<6)errors["password"] = "Пароль должен содержать как минимум шесть символов"; else
			if($("#password").val().trim()!==$("#password_confirm").val().trim()) errors["password_confirm"] = "Пароли не совпадают"; else	
			if($('#password').val().trim()==$('#username').val().trim() || $('#password').val().trim()==$('#phone').val().trim() || 
				$('#password').val().trim()==$('#email').val().trim()) errors['password'] = 'Пароль не должен совпадать с email, именем пользователя или номером телефона';
			if(Object.keys(errors).length === 0){
				$('#button1').val('1');
				this.submit();

			}
			else{
				for(key in errors){
						$('#'+key+'_err').text(errors[key]);
				}
			}										
		});
	});