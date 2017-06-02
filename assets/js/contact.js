$(document).ready(function(){
				$('#update_contacts_form').submit(function(e){
					e.preventDefault();
					$("button1").val(0);
					$('.bg-danger').empty();
				var errors = {};
			
				if(!/^8[\d]{10}$/.test($('#phone').val().trim())) errors['phone'] = "Введите номер в 11-значном формате 8XXXXXXXXXX";
				if(Object.keys(errors).length==0){
					$('#button1').val(1);
					this.submit();

				}
				else{
					for(key in errors){
							$('#'+key+'_err').text(errors[key]);
					}
				}	
				});
			})