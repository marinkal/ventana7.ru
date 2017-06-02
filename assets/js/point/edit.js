	$(document).ready(function(e){
		$('#edit_point_form').submit(function(e){
			e.preventDefault();
			$('#button').val(0);
			$('.bg-danger').empty();
			var errors = {};
			if($('#name').val().trim().length<1) errors['name'] = 'Наименование точки не должно быть пустым';
			if(Object.keys(errors).length!==0){
				for(key in errors){
						$('#'+key+'_err').text(errors[key]);
				}
			} else {
				$('#button1').val(1);
				this.submit();
			}

		});
	});