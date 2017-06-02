<?php
	function setCenter($show_routes){
		//$show_routes - это массив всех маршрутов которые надо отобразить на карте
		//Ключи в $show_routes какие угодно
		//Каждый элемент представляет собой массив строк вида [x,y], либо (x,y), допустимо другое, но x и y - обязательно числа с точкой,
			//разделенные запятой без пробела
		//Задача функции выбрать удачную точку для центра карты
		$x_left = 91.00;
		$x_right = -91.00;
		$y_top = 181;
		$y_bottom = -180;
		foreach ($show_routes as $show_route) {
		
			$array = array_map(function($el){
				preg_match('/([\d]+\.[\d]+),([\d]+\.[\d]+)/',$el,$matches);
				return [$matches[1],$matches[2]];

			},$show_route);

			$x_left = array_reduce($array, function($res,$el){return min($el[0],$res);},$x_left);
			$x_right = array_reduce($array, function($res,$el){return max($el[0],$res);},$x_right);
			$y_top = array_reduce($array, function($res,$el){return min($el[1],$res);},$y_top);
			$y_bottom = array_reduce($array, function($res,$el){return max($el[1],$res);},$y_bottom);
		};
	    $x = ($x_left+$x_right+2)/2;
	    $y = ($y_top+$y_bottom)/2;
		
		return [$x,$y];
	}
function convertCoordinate($el){
	//$el - ассоциативный массив
	//функция превращает $el['coordinate'] из (x,y) в [x,y]
	$el['coordinate'] = preg_replace('/\((.*)\)/',"[$1]",$el['coordinate']);
	 return $el;
}

?>