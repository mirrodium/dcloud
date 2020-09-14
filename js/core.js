$(document).ready(function(){
	$('#btnForTaskAdd').on('click', function(){
		$('#AddEditTaskID').val(0);
		$('#AddEditTaskType').val('add');
	});
	
	
});

$.fn.formValidation=function(ID){
	var validate=true;
	
	$('#'+ID).children().each(function(){
		if($(this).attr('required')){
			$(this).css({border:'1px solid #0f0'});
			if(!$(this).val().length) {
				$(this).css({border:'1px solid #f00'});
				validate=false;
			}
		}
	});
	
	return validate;
}

$.fn.AddEditTask=function(ID){
	if($.fn.formValidation(ID)){   
		
	}
}