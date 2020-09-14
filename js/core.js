$(document).ready(function(){
	$('#btnForTaskAdd').on('click', function(){
		$('#AddEditTaskID').val(0);
		$('#AddEditTaskType').val('add');
	});
	
	$('#btnForUserAdd').on('click', function(){
		$('#AddEditUserID').val(0);
		$('#AddEditUserType').val('add');
	});
});

$.fn.formValidation=function(ID){
	var validate=true;
	
	$('#'+ID).children().each(function(){
		if($(this).attr('required')){
			$(this).css({border:'1px solid #0f0'});
			if(!$(this).val().length || $(this).val()=='NULL') {
				$(this).css({border:'1px solid #f00'});
				validate=false;
			}
		}
	});
	
	return validate;
}

$.fn.AddEditTask=function(ID){
	$('#'+ID+'Error').hide();
	if($.fn.formValidation(ID)){   
		var cID					=	$('#'+ID+'ID');
		var cType				=	$('#'+ID+'Type');
		var cName				=	$('#'+ID+'Name');
		var cUser				=	$('#'+ID+'User');
		var cStatus				=	$('#'+ID+'Status');
		var cDescription		=	$('#'+ID+'Description');
		var cIblock_ID			=	$('#'+ID+'Iblock_Id');
		var cUsers_Iblock_ID	=	$('#'+ID+'Users_Iblock_ID');

		var URL		=	'ajax.php';
		var DATA	=	
			'proc='				+'tasks'+
			'&iblock_id='		+cIblock_ID.val()+
			'&users_iblock_id='	+cUsers_Iblock_ID.val()+
			'&id='				+cID.val()+
			'&type='			+cType.val()+
			'&name='			+cName.val()+
			'&user='			+cUser.val()+
			'&status='			+cStatus.val()+
			'&description='		+cDescription.val();

		$.ajax({
			url:		URL,
			type:		"POST",
			data:		DATA,
			success:	function(response){
				var DATAs=jQuery.parseJSON(response);
				if(DATAs['error']){
					$('#'+ID+'Error').show();
					$('#'+ID+'Error').html(DATAs['error']);
					alert(DATAs['error']);
				}else if(DATAs['TASKS']&&DATAs['IBLOCK_ID']){
					$('#'+ID+'Close').trigger('click');
					if($('#'+ID+'Table').length){
						$('#'+ID+'Table tbody').empty();
						$.fn.parseResponseForTasks(ID, DATAs);
					}
				}
			}
		});	
	}
}

$.fn.parseResponseForTasks=function(ID, DATAs){
	for(var a=0; a<DATAs['TASKS'].length; a++){
		var INDEX		=	0;
		var USERS		=	'';
		var USERS_IDS	=	'';
		
		$.each(DATAs['TASKS'][a]['RESPONSIBLE'], function(KEY, VALUE){
			if(INDEX>0){
				USERS+=', ';
				USERS_IDS+=',';
			}
			
			USERS+=VALUE;
			USERS_IDS+=KEY;
			INDEX=INDEX+1;
		});
							
		var STATUSES={};
		$.each(DATAs['STATUSES'], function(KEY, VALUE){
			var SELECTED="N";
			if(KEY==DATAs['TASKS'][a]['STATUS']){
				SELECTED="Y";
			}
								
			STATUSES[KEY]={
				ID			:	KEY,
				NAME		:	VALUE.NAME,
				SELECTED	:	SELECTED
			};
		});
							
		$.fn.AddTaskRow({
			ID			:	ID+'Table',
			IBLOCK_ID	:	DATAs['IBLOCK_ID'],
			ROW			:	{
				ID			:	DATAs['TASKS'][a]['ID'],
				NAME		:	DATAs['TASKS'][a]['NAME'],
				USERS		:	USERS,
				USERS_IDS	:	USERS_IDS,
				STATUSEs	:	STATUSES,
				DESCRIPTION	:	DATAs['TASKS'][a]['PREVIEW_TEXT'],
			}
		});
	}
}

$.fn.AddTaskRow=function(PARAMs){
	if($('#'+PARAMs['ID']).length){
		var STATUS='';
		var selectStatus='<select onchange="$(\'#row_'+PARAMs['ROW']['ID']+'\').attr(\'data-status\', $(this).val()); $.fn.setVariableFoTask(\'AddEditTask\', \'row_'+PARAMs['ROW']['ID']+'\', \'edit\'); $.fn.AddEditTask(\'AddEditTask\');">';
		
		$.each(PARAMs['ROW']['STATUSEs'], function(){
			var selected='';
			if(this.SELECTED=='Y'){
				STATUS=this.ID;
				selected='selected="selected"';
			}
			
			selectStatus+='<option value="'+this.ID+'" '+selected+'>'+this.NAME+'</option>';
		});
		selectStatus+='</select>'
		
		var CHILD=
			'<tr>'+
				'<td '+
					'id="row_'+PARAMs['ROW']['ID']+'" '+
					'data-id="'+PARAMs['ROW']['ID']+'" '+
					'data-name="'+PARAMs['ROW']['NAME']+'" '+
					'data-status="'+STATUS+'" '+
					'data-users="'+PARAMs['ROW']['USERS_IDS']+'" '+
					'data-description="'+PARAMs['ROW']['DESCRIPTION']+'">'+
					PARAMs['ROW']['ID']+
				'</td>'+
				'<td>'+PARAMs['ROW']['NAME']+'</td>'+
				'<td>'+PARAMs['ROW']['USERS']+'</td>'+
				'<td>'+selectStatus+'</td>'+
				'<td><a href="#" onclick="$.fn.setVariableFoTask(\'AddEditTask\', \'row_'+PARAMs['ROW']['ID']+'\', \'edit\');" data-toggle="modal" data-target="#modalForTasks">Редактировать</a> / <a href="javascript:void(0);" onclick="$.fn.setVariableFoTask(\'AddEditTask\', \'row_'+PARAMs['ROW']['ID']+'\', \'delete\'); $.fn.AddEditTask(\'AddEditTask\');">Удалить</a></td>'+
			'</tr>';
		
		$('#'+PARAMs['ID']+' tbody').append(CHILD);
	}
}

$.fn.setVariableFoTask=function(MODAL, ID, TYPE){
	$('#'+MODAL+'Type').val(TYPE);
	$('#'+MODAL+'ID').val($('#'+ID).attr('data-id'));
	$('#'+MODAL+'Name').val($('#'+ID).attr('data-name'));
	
	$('#'+MODAL+'Status option').removeAttr("selected");
	$('#'+MODAL+'Status option[value=\''+$('#'+ID).attr('data-status')+'\']').attr("selected", "selected");
	
	$('#'+MODAL+'User option').removeAttr("selected");
	var USERs=$('#'+ID).attr('data-users').split(',');
	for(var a=0; a<USERs.length; a++){
		$('#'+MODAL+'User option[value=\''+USERs[a]+'\']').attr("selected", "selected");
	}
}

$.fn.AddUserRow=function(PARAMs){
	if($('#'+PARAMs['ID']).length){
		var CHILD=
			'<tr>'+
				'<td '+
					'id="row_'+PARAMs['ROW']['ID']+'" '+
					'data-id="'+PARAMs['ROW']['ID']+'" '+
					'data-name="'+PARAMs['ROW']['NAME']+'" '+
					'data-last-name="'+PARAMs['ROW']['LAST_NAME']+'" '+
					'data-post="'+PARAMs['ROW']['POST']+'">'+
					PARAMs['ROW']['ID']+
				'</td>'+
				'<td>'+PARAMs['ROW']['NAME']+'</td>'+
				'<td>'+PARAMs['ROW']['LAST_NAME']+'</td>'+
				'<td>'+PARAMs['ROW']['POST']+'</td>'+
				'<td><a href="#" onclick="$.fn.setVariableFoUser(\'AddEditUser\', \'row_'+PARAMs['ROW']['ID']+'\', \'edit\');" data-toggle="modal" data-target="#modalForUsers">Редактировать</a> / <a href="javascript:void(0);" onclick="$.fn.setVariableFoUser(\'AddEditUser\', \'row_'+PARAMs['ROW']['ID']+'\', \'delete\'); $.fn.AddEditUser(\'AddEditUser\');">Удалить</a></td>'+
			'</tr>';
		
		$('#'+PARAMs['ID']+' tbody').append(CHILD);
	}
}

$.fn.AddEditUser=function(ID){
	$('#'+ID+'Error').hide();
	if($.fn.formValidation(ID)){   
		var cID					=	$('#'+ID+'ID');
		var cType				=	$('#'+ID+'Type');
		var cName				=	$('#'+ID+'Name');
		var cLastName			=	$('#'+ID+'LastName');
		var cPost				=	$('#'+ID+'Post');
		var cIblock_ID			=	$('#'+ID+'Iblock_Id');
		var cTasks_Iblock_ID	=	$('#'+ID+'Tasks_Iblock_ID');

		var URL		=	'ajax.php';
		var DATA	=	
			'proc='				+'users'+
			'&iblock_id='		+cIblock_ID.val()+
			'&tasks_iblock_id='	+cTasks_Iblock_ID.val()+
			'&id='				+cID.val()+
			'&type='			+cType.val()+
			'&name='			+cName.val()+
			'&last_name='		+cLastName.val()+
			'&post='			+cPost.val();

		$.ajax({
			url:		URL,
			type:		"POST",
			data:		DATA,
			success:	function(response){
				var DATAs=jQuery.parseJSON(response);
				if(DATAs['error']){
					$('#'+ID+'Error').show();
					$('#'+ID+'Error').html(DATAs['error']);
					alert(DATAs['error']);
				}else if(DATAs['USERS']&&DATAs['IBLOCK_ID']){
					$('#'+ID+'Close').trigger('click');
					if($('#'+ID+'Table').length){
						$('#'+ID+'Table tbody').empty();
						$.fn.parseResponseForUsers(ID, DATAs);
					}
				}
			}
		});	
	}
}

$.fn.parseResponseForUsers=function(ID, DATAs){
	for(var a=0; a<DATAs['USERS'].length; a++){					
		$.fn.AddUserRow({
			ID			:	ID+'Table',
			IBLOCK_ID	:	DATAs['IBLOCK_ID'],
			ROW			:	{
				ID			:	DATAs['USERS'][a]['ID'],
				NAME		:	DATAs['USERS'][a]['NAME'],
				LAST_NAME	:	DATAs['USERS'][a]['LAST_NAME'],
				POST		:	DATAs['USERS'][a]['POST']
			}
		});
	}
}

$.fn.setVariableFoUser=function(MODAL, ID, TYPE){
	$('#'+MODAL+'Type').val(TYPE);
	$('#'+MODAL+'ID').val($('#'+ID).attr('data-id'));
	$('#'+MODAL+'Name').val($('#'+ID).attr('data-name'));
	$('#'+MODAL+'LastName').val($('#'+ID).attr('data-last-name'));
	$('#'+MODAL+'Post').val($('#'+ID).attr('data-post'));
}