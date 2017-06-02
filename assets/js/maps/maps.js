   

 function make_map(show_points,show_routes, mapStateAutoApply = false){
		
			ymaps.ready(init);
		    var myMap;
		    var myPlacemark;
		    function init(){  
		        var presets = [
		        	'islands#blueStretchyIcon',
		        	'islands#redStretchyIcon',
		        	'islands#darkOrangeStretchyIcon',
		        	'islands#nightStretchyIcon',
		        	'islands#darkBlueStretchyIcon',
		        	'islands#pinkStretchyIcon',
		        	'islands#grayStretchyIcon',
		        	'islands#brownStretchyIcon',
		        	'islands#darkGreenStretchyIcon',
		        	'islands#violetStretchyIcon',
		        	'islands#blackStretchyIcon',
		        	'islands#yellowStretchyIcon',
		        	'islands#greenStretchyIcon',
		        	'islands#orangeStretchyIcon',
		        	'islands#lightBlueStretchyIcon',
		        	'islands#oliveStretchyIcon'
		        ];
		        var colors = [
		        	'#0000ff',
		        	'#ff0000',
		        	'#ff8c00',
		        	'#191970',
		        	'#00008b',
		        	'#ffc0cb',
		        	'#bebebe',
		        	'#a52a2a',
		        	'#006040',
		        	'#ee82ee',
		        	'#000000',
		        	'#ffff00',
		        	'#00ff00',
		        	'#ffa500',
		        	'#add8e6',
		        	'#808000'


		        ]
		        var p_length = presets.length;
		        var c_length = colors.length;  
			    myMap = new ymaps.Map("map", {
			            center: [0,0],
			            zoom: 0
			    });
		    	var preset_count = 0;
		    	var color_count = 0;
		    	for(var index in show_routes){
		     		var show_route = show_routes[index];
		 		    var map_route = [];
		 		    
		    		for(var key in show_route){
		    			map_route.push(show_route[key]['coordinate']);
		    		}
		 		   	ymaps.route(
		     			map_route,
		     			{
		     				 mapStateAutoApply: mapStateAutoApply
		     			}
					).then(
					    function (route) {
					    	route.getPaths().options.set({
					        strokeColor: colors[color_count],
					        opacity: 0.6
					     });
					    myMap.geoObjects.add(route);
					        
					       var points = route.getWayPoints();
					        points.options.set('preset', presets[preset_count]);	      
					        preset_count = (preset_count + 1) % p_length;
					        color_count = (color_count + 1) % c_length;

					    },
					    function (error) {
					        alert("Возникла ошибка: " + error.message);
					    }
					)

		    	}

		    	for(var index in show_points){
			    	var show_point = show_points[index];
			    	myPlacemark = new ymaps.Placemark(show_point['coordinate'], {
		            hintContent: show_point['hintContent'],
		            iconCaption: show_point['iconCaption'],
		            balloonContentBody: show_point['balloonContentBody']

	        		}, {
	            		preset: 'islands#greenDotIconWithCaption'
	        		});
	        		 myMap.geoObjects.add(myPlacemark);
		    	}
		    	var result = ymaps.geoQuery(myMap.geoObjects);

				if (result.isReady()) {
				   		result.then(function () {
					    result.applyBoundsToMap(myMap,{
			   			checkZoomRange: true,
			   			preciseZoom: true,
			   			zoomMargin: [300,0,0,0]
			   			});
			        // Обработка данных.
				    });
				   };


			 		myMap.setBounds(myMap.geoObjects.getBounds());
			    

				};   
		    }