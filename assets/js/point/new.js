	$(document).ready(function(){
		ymaps.ready(init);
		    var myMap;
		    var myPlacemark;

    		function init(){     
		    myMap = new ymaps.Map("map", {
		            center: [57.09,65.32],
		            zoom: 5
		        });

		    	myMap.events.add('click', function (e) {
		    	myMap.geoObjects.remove(myPlacemark);
        		if (!myMap.balloon.isOpen()) {
           		 var coords = e.get('coords');
           		 $('#x').val(coords[0].toPrecision(6));
                 $('#y').val(coords[1].toPrecision(6));
		          myPlacemark = new ymaps.Placemark([coords[0].toPrecision(6),coords[1].toPrecision(6)], {
	              hintContent: $('#name').val().trim(),
	            	iconCaption: $('#name').val().trim()
        		}, {
            		preset: 'islands#blueDotIconWithCaption'
        		});
		      myMap.geoObjects.add(myPlacemark);
		    		};
		    	});


			};
			
		$('#create_point').submit(function(e){
			e.preventDefault();
			$('.bg-danger').empty();
			var errors = {};
			if($('#name').val().trim().length==0) errors['name'] = 'Наименование точки не должно быть пустым';
			if(!/^-?\d{1,3}\.\d+$/.test($('#x').val().trim())) errors['x'] = 'Неверный формат широты';
				else if(1.0*$('#x').val().trim()<-90 || 1.0*$('#x').val().trim()>90) errors['x'] = 'Широта бывает от -90 до 90 градусов';
			if(!/^-?\d{1,3}\.\d+$/.test($('#y').val().trim())) errors['y'] = 'Неверный формат долготы';
					else if(1.0*$('#y').val().trim()<-180|| 1.0*$('#y').val().trim()>180) errors['y'] = 'Долгота бывает от -180 до 180 градусов';
			if(Object.keys(errors).length==0){
				this.submit();
			}	else {
				for(key in errors){
						$('#'+key+'_err').text(errors[key]);
				}
			}
		})
	})
	