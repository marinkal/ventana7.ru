<?php
	function field($label,$attributes,$error = ""){
		$result="<div class='form-group'>
			<label for='$attributes[name]'>$label</label>
			<input class='form-control'";
		foreach ($attributes as $key => $value) {
			$result.=" $key='$value'";
		};
		$result.=">
		<p class='bg-danger' id='".$attributes['id']."_err'>$error</p>
		</div>";
		return $result;
	}
	function datetime_picker($label,$attributes){
		$result = "<div class='form-group'>
				<label for='$attributes[name]'>$label</label>
                <div class='input-group date'>
                    <input class='form-control'";
        foreach ($attributes as $key => $value) {
			$result.=" $key='$value'";
		};
        $result.="/>
        			<span class='input-group-addon'>
                        <span class='glyphicon glyphicon-calendar'></span>
                    </span>
                </div>
            </div>";
         return $result;
	}
	function select($label,$attributes,$options,$selected){
		$result="<div class='form-group'>
			<label for='$attributes[name]'>$label</label>
			<select class='form-control'";
			foreach ($attributes as $key => $value) {
			$result.=" $key='$value'";
		};
		$result.=">";
		foreach ($options as $value) {
			$result.=
				"<option value=".$value['id'];
				 if( $value['id']==$selected)$result.=' selected ';
				 $result.=">$value[name]</option>";
		}
		$result.="
			</select>
			</div>";
		return $result;


	}

	function checkbox($label,$attributes,$checked=''){
		$result = "<div class='form-group'>
   		<div class='checkbox'>
	  		<label>
	    		<input type='checkbox'";
	    foreach ($attributes as $key => $value) {
	    	$result.=" $key='$value'";
	    };
	   	$result.=" $checked>
	  		 $label
	  		</label>
    	</div>
  		</div>";
  		return $result;
	}
	function input_hidden($name,$id,$value){
		return "<input type='hidden' name='$name' value='$value' id='$id' />";
	}
	function button($title,$attributes){
		$result = "<button";
		foreach($attributes as $key => $value){
			$result.=" $key='$value'";
		}
		$result.=">$title</button>";
		return $result;
	}

	function submit_button($title,$name,$class="",$value=-1){
		return "<button type='submit' class='btn $class' name='$name' id='$name' value='$value'>$title</button>";
	}

	function textarea($label,$attributes,$text,$error){
		$result="<div class='form-group'>
		<label for='$attributes[name]'>$label</label>
			<textarea class='form-control'";
		foreach ($attributes as $key => $value) {
			$result.=" $key='$value'";
		};
		$result.=">$text</textarea>
		<p class='bg-danger' id='".$attributes['id']."_err'>$error</p>
		</div>";
		return $result;
	}
	//дальше общее функции

	function make_days($count){
		$days = [];
		for($i=0;$i<$count;$i++) {
			$days[] = ['id' => $i, 'name' => $i];
		}
		return $days;
	}

	function rdate($date_str){
		return preg_replace('/^(\d\d\d\d)-(\d\d)-(\d\d)(.*)/',"$3.$2.$1$4",$date_str);
	}

	function rtime($time_str){
		if(preg_match('/(\d) day/', $time_str,$matches)){
			if($matches[1]>1 && $matches[1]<5) $replace = "дня"; 
				else if($matches[1]==1)  $replace ="день"; 
					else $replace ="дней";
			return preg_replace('/(.*)days?(.*)/', "$1$replace$2", $time_str);
		}
		return $time_str;
	}

	function show_info($md_info,$elements){
		$result.="<hr/>"				
		;
		foreach ($elements as $key => $value) {
			$result.="<div class='row'>"
			;
			$result.="	<div class='col-md-$md_info'>{$key}:</div>";
			$result.="	<div><b>{$value}</b></div>";
			$result.="</div>
			<hr/>"
			;

			}
			

		return $result;
	}

	function simple_table($headers,$rows,$columns){
		$result = '
		<table border=1>
			<thead>
				<tr>';
		foreach ($headers as $header) {
			$result.="
					<th>$header</th>";
		}
		$result.='
				</tr>
			<thead>
			<tbody>
			';
		foreach ($rows as $row) {
			$result.='		<tr>';	
			foreach ($columns as $column) {
				$result.="
						<td>".rdate($row[$column])."</td>";
			}
			$result.="
					</tr>
			";
		}
		$result.="	</tbody>
		</table>
		";
		return $result;
	}

	function a_show($page,$id,$name){
		$result="<a href='/";
		$result.="$page\/show.php?id=$id'>$name</a>";
		return $result;
	}

	function htable($elements){
		$columns = '';
		$recods = '';
		foreach ($elements as $column => $record ){
			$columns.="<th>$column</th>
					";
			$records.="<td>$record</td>
					";

		}
		return "
			<table class='table-bordered'>
				<thead>
					<tr>
						$columns
					</tr>
				</thead>
				<tbody>
					<tr>
						$records
					</tr>
				</tbody>
			</table>"
			
	;}




?>
