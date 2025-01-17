<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
CModule::IncludeModule('iblock');
require_once('class.php');
use Dcloud\TASKs\Main as Dcloud;?>
<!doctype html>
<html>
	<head>
		<script src="js/jquery-3.5.1.min.js"></script>
		<script src="js/core.js"></script>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta charset="UTF-8">
		<title>Планировщик задач</title>
	</head>

	<body>
		<div class="container">
			<?if(!$USER->isAuthorized()){?>
				<font color="#f00">Вы не авторизированы!</font>
			<?}else{?>
				<ul class="nav nav-tabs" id="myTab" role="tablist" style="margin-top: 55px;">
					<li class="nav-item active">
						<a class="nav-link active" id="tasks-tab" data-toggle="tab" href="#tasks" role="tab" aria-controls="tasks" aria-selected="true">Задачи</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="users-tab" data-toggle="tab" href="#users" role="tab" aria-controls="users" aria-selected="false">Исполнители</a>
					</li>
				</ul>
				<div class="tab-content" id="myTabContent">
					<div class="tab-pane active" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
						<table id="AddEditTaskTable" class="table table-striped">
							<thead>
								<tr>
									<th>ИД</th>
									<th>Название</th>
									<th>Исполнитель</th>
									<th>Статус</th>
									<th>Действия</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<?$USERs	=	DCloud::getUsers();
						$TASKs		=	DCloud::getTasks($USERs['IBLOCK_ID']);
						$STATUSEs	=	DCloud::getTasksStatuses(['IBLOCK_ID'=>$TASKs['IBLOCK_ID']]);
						$uHidden	=	'';
						$tHidden	=	'';
						if(count($TASKs['TASKS'])>0){
							$tHidden	=	'hidden="hidden"';?>
							<script>
								<?for($a=0; $a<count($TASKs['TASKS']); $a++){
									$USERS		=	'';
									$USERS_IDS	=	'';
									$INDEX		=	0;
									foreach($TASKs['TASKS'][$a]['RESPONSIBLE'] as $ID=>$NAME){
										if($INDEX>0){
											$USERS.=', ';
											$USERS_IDS.=',';
										}
										$USERS.=$NAME;
										$USERS_IDS.=$ID;
										$INDEX++;
									}?>
								
									$.fn.AddTaskRow({
										ID			:	'AddEditTaskTable',
										IBLOCK_ID	:	'<?=$TASKs['IBLOCK_ID']?>',
										ROW			:	{
											ID			:	'<?=$TASKs['TASKS'][$a]['ID']?>',
											NAME		:	'<?=$TASKs['TASKS'][$a]['NAME']?>',
											USERS		:	'<?=$USERS?>',
											USERS_IDS	:	'<?=$USERS_IDS?>',
											STATUSEs	:	{
												<?foreach($STATUSEs as $KEY=>$VALUE){
													$SELECTED='N';
													if($KEY==$TASKs['TASKS'][$a]['STATUS']){
														$SELECTED='Y';
													}?>
													<?=$KEY?>:{
														ID			:	'<?=$KEY?>',
														NAME		:	'<?=$VALUE['NAME']?>',
														SELECTED	:	'<?=$SELECTED?>'
													},
												<?}?>
											},
											DESCRIPTION	:	'<?=$TASKs['TASKS'][$a]['PREVIEW_TEXT']?>',
										}
									});
								<?}?>
							</script>
						<?}?>
						<font color="#f00" id="AddEditTaskTableError" <?=$tHidden?>>Ещё нет ни одной задачи</font>
						<div style="width: 100%;" align="right">
							<br>
							<button id="btnForTaskAdd" type="button" class="btn btn-lg btn-success" data-toggle="modal" data-target="#modalForTasks">Добавить</button>
						</div>
					</div>
					<div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
						<table id="AddEditUserTable" class="table table-striped">
							<thead>
								<tr>
									<th>ИД</th>
									<th>Имя</th>
									<th>Фамилия</th>
									<th>Должность</th>
									<th>Действия</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<?if(count($USERs['USERS'])>0){
							$uHidden	=	'hidden="hidden"';?>
							<script>
								<?for($a=0; $a<count($USERs['USERS']); $a++){?>
								
									$.fn.AddUserRow({
										ID			:	'AddEditUserTable',
										IBLOCK_ID	:	'<?=$USERs['IBLOCK_ID']?>',
										ROW			:	{
											ID			:	'<?=$USERs['USERS'][$a]['ID']?>',
											NAME		:	'<?=$USERs['USERS'][$a]['NAME']?>',
											LAST_NAME	:	'<?=$USERs['USERS'][$a]['LAST_NAME']?>',
											POST		:	'<?=$USERs['USERS'][$a]['POST']?>'
										}
									});
								<?}?>
							</script>
						<?}?>
						<font color="#f00" id="AddEditUserTableError" <?=$uHidden?>>Ещё нет ни одного пользователя</font>
						<div style="width: 100%;" align="right">
							<br>
							<button id="btnForUserAdd" type="button" class="btn btn-lg btn-success" data-toggle="modal" data-target="#modalForUsers">Добавить</button>
						</div>
					</div>
				</div>
			<?}?>
		</div>
		
		<!--<Modal for Tasks>-->
		<div class="modal fade" id="modalForTasks">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="AddEditTaskClose">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Добавление задачи</h4>
					</div>
					<div class="modal-body">
						<form id="AddEditTask" method="post" action="ajax.php">
							<input id="AddEditTaskID" type="hidden" name="id" value=""/>
							<input id="AddEditTaskType" type="hidden" name="type" value=""/>
							<input id="AddEditTaskIblock_Id" type="hidden" name="type" value="<?=$TASKs['IBLOCK_ID']?>"/>
							<input id="AddEditTaskUsers_Iblock_ID" type="hidden" name="type" value="<?=$USERs['IBLOCK_ID']?>"/>
							<label>Название:</label><input id="AddEditTaskName" name="name" required style="width: 100%"/>
							<br>
							<br>
							<label>Исполнитель:</label><select id="AddEditTaskUser" name="user" required multiple  style="width: 100%">
								<?for($a=0; $a<count($USERs['USERS']); $a++){?>
									<option value="<?=$USERs['USERS'][$a]['ID']?>"><?=$USERs['USERS'][$a]['NAME']?></option>
								<?}?>
							</select>
							<br>
							<br>
							<label>Статус:</label><br>
							<select id="AddEditTaskStatus" name="status" required>
								<?foreach($STATUSEs as $KEY=>$VALUE){
									$SELECTED='';
									if($VALUE['DEF']=="Y"){
										$SELECTED='selected="selected"';
									}?>
									<option value="<?=$KEY?>" <?=$SELECTED?>><?=$VALUE['NAME']?></option>
								<?}?>
							</select>
							<br>
							<br>
							<label>Описание:</label><br>
							<textarea id="AddEditTaskDescription" name="desc" style="width: 100%"></textarea>
							<div id="AddEditTaskError" hidden="hidden" style="color: #f00;"></div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
						<button type="button" class="btn btn-primary" onClick="$.fn.AddEditTask('AddEditTask')">Сохранить</button>
					</div>
				</div>
			</div>
		</div>
		<!--</Modal for Tasks>-->
		
		<!--<Modal for Users>-->
		<div class="modal fade" id="modalForUsers">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="AddEditUserClose">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Добавление пользователя</h4>
					</div>
					<div class="modal-body">
						<form id="AddEditUser" method="post" action="ajax.php">
							<input id="AddEditUserID" type="hidden" name="id" value=""/>
							<input id="AddEditUserType" type="hidden" name="type" value=""/>
							<input id="AddEditUserIblock_Id" type="hidden" name="type" value="<?=$USERs['IBLOCK_ID']?>"/>
							<input id="AddEditUserTasks_Iblock_ID" type="hidden" name="type" value="<?=$TASKs['IBLOCK_ID']?>"/>
							<label>Имя:</label><input id="AddEditUserName" name="name" required style="width: 100%"/>
							<br>
							<br>
							<label>Фамилия:</label><input id="AddEditUserLastName" name="last_name" style="width: 100%"/>
							<br>
							<br>
							<label>Должность:</label><input id="AddEditUserPost" name="post" required style="width: 100%"/>
							<div id="AddEditUserError" hidden="hidden" style="color: #f00;"></div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
						<button type="button" class="btn btn-primary" onClick="$.fn.AddEditUser('AddEditUser')">Сохранить</button>
					</div>
				</div>
			</div>
		</div>
		<!--</Modal for Users>-->
	</body>
</html>