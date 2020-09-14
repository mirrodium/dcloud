<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

require_once('class.php');
use DCloud\TASKs\Main as DCloud;

if(!empty($_POST['proc'])){
	$RESPONSE=['error'=>'Произошла неизвестная ошибка.'];
	if($_POST['proc']=='tasks'){
		if(
			!empty($_POST['iblock_id'])
			and !empty($_POST['users_iblock_id'])
			and !empty($_POST['type'])
			and !empty($_POST['name'])
			and !empty($_POST['user'])
			and !empty($_POST['status'])
		){
			if($_POST['type']=='add'){
				$FIELDs=[
					'IBLOCK_ID'=>$_POST['iblock_id'],
					'NAME'=>$_POST['name'],
					'STATUS'=>$_POST['status']
				];
				
				$RESPONSIBLEs=explode(',', $_POST['user']);
				for($a=0; $a<count($RESPONSIBLEs); $a++){
					$FIELDs['RESPONSIBLE'][]=$RESPONSIBLEs[$a];
				}
				
				if(!empty($_POST['description'])){
					$FIELDs['PREVIEW_TEXT']=$_POST['description'];
				}
				
				$ADD=DCloud::addTask($FIELDs);
				if($ADD=='success'){
					$RESPONSE=DCloud::getTasks($_POST['users_iblock_id']);
					$RESPONSE['STATUSES']=DCloud::getTasksStatuses(['IBLOCK_ID'=>$_POST['iblock_id']]);
				}
				
			}elseif($_POST['type']=='edit' and !empty($_POST['id'])){
				$FIELDs=[
					'ID'=>$_POST['id'],
					'IBLOCK_ID'=>$_POST['iblock_id'],
					'NAME'=>$_POST['name'],
					'STATUS'=>$_POST['status']
				];
				
				$RESPONSIBLEs=explode(',', $_POST['user']);
				for($a=0; $a<count($RESPONSIBLEs); $a++){
					$FIELDs['RESPONSIBLE'][]=$RESPONSIBLEs[$a];
				}
				
				if(!empty($_POST['description'])){
					$FIELDs['PREVIEW_TEXT']=$_POST['description'];
				}
				
				$ADD=DCloud::editTask($FIELDs);
				if($ADD=='success'){
					$RESPONSE=DCloud::getTasks($_POST['users_iblock_id']);
					$RESPONSE['STATUSES']=DCloud::getTasksStatuses(['IBLOCK_ID'=>$_POST['iblock_id']]);
				}
				
			}elseif($_POST['type']=='delete' and !empty($_POST['id'])){
				DCloud::deleteTask(['IBLOCK_ID'=>$_POST['iblock_id'], 'ID'=>$_POST['id']]);
				$RESPONSE=DCloud::getTasks($_POST['users_iblock_id']);
				$RESPONSE['STATUSES']=DCloud::getTasksStatuses(['IBLOCK_ID'=>$_POST['iblock_id']]);
			}
		}else{
			$RESPONSE=['error'=>'Отсутствует один или несколько обязательных параметров.'];
		}
	}
	
	if($_POST['proc']=='users'){
		if(
			!empty($_POST['iblock_id'])
			and !empty($_POST['type'])
			and !empty($_POST['name'])
			and !empty($_POST['post'])
		){
			if($_POST['type']=='add'){
				$FIELDs=[
					'IBLOCK_ID'=>$_POST['iblock_id'],
					'NAME'=>$_POST['name'],
					'POST'=>$_POST['post']
				];
				
				if(!empty($_POST['last_name'])){
					$FIELDs['LAST_NAME']=$_POST['last_name'];
				}
				
				$ADD=DCloud::addUser($FIELDs);
				if($ADD=='success'){
					$RESPONSE=DCloud::getUsers();
				}
				
			}elseif($_POST['type']=='edit' and !empty($_POST['id'])){
				$FIELDs=[
					'IBLOCK_ID'=>$_POST['iblock_id'],
					'ID'=>$_POST['id'],
					'NAME'=>$_POST['name'],
					'POST'=>$_POST['post']
				];
				
				if(!empty($_POST['last_name'])){
					$FIELDs['LAST_NAME']=$_POST['last_name'];
				}
				
				$ADD=DCloud::editUser($FIELDs);
				if($ADD=='success'){
					$RESPONSE=DCloud::getUsers();
				}	
			}elseif($_POST['type']=='delete' and !empty($_POST['id']) and !empty($_POST['tasks_iblock_id'])){
				$ADD=DCloud::deleteUser(['TASKS_IBLOCK_ID'=>$_POST['tasks_iblock_id'], 'IBLOCK_ID'=>$_POST['iblock_id'], 'ID'=>$_POST['id']]);
				$RESPONSE=['error'=>$ADD];
				if($ADD=='success'){
					$RESPONSE=DCloud::getUsers();
				}elseif($ADD=='tasksFound'){
					$RESPONSE=['error'=>'Пользователь участвует в выполнении задач.'];
				}elseif($ADD=='any params not found'){
					$RESPONSE=['error'=>'Отсутствует один или несколько обязательных параметров.'];
				}
			}
		}else{
			$RESPONSE=['error'=>'Отсутствует один или несколько обязательных параметров.'];
		}
	}
	
	echo json_encode($RESPONSE);
}