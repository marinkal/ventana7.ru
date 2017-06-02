function tfilter(request_id, fio ){
				var trs = $('tbody>tr');
				$('tbody>tr').each(function(ind,tr){tr.style.display="";});	
				if(fio!='')$('tbody>tr>td.fio').each(function(ind,td){
							if(td.textContent!=fio){
								td.parentNode.style.display = "none";
							} //else console.log(tds[1].textContent);
					
				});
				if(request_id!='')$('tbody>tr>td.req_num').each(function(ind,td){
							if(td.textContent!=request_id){
								td.parentNode.style.display = "none";
							} //else console.log(tds[1].textContent);
					
				});					
			}
    	$(document).ready(function(){
			$('#fio_filter').change(function(){		
				tfilter($("#req_num").val().trim(),$('#fio_filter').val().trim());
			});
			$("#req_num").on('input', function(e){
				tfilter($("#req_num").val().trim(),$('#fio_filter').val().trim());
			;});
					//--А дальше карта			
			
		});