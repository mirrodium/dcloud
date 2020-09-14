<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
CModule::IncludeModule('iblock');
require_once('class.php');
use Dcloud\TASKs\Main as Dcloud;?>
<!doctype html>
<html>
	<head>
		<script src="js/jquery-3.5.1.min.js"></script>
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
						<?$USERs=DCloud::getUsers();
						$TASKs=DCloud::getTasks($USERs['IBLOCK_ID']);
						if(count($TASKs['TASKS'])==0){?>
							<font color="#f00">Ещё нет ни одной задачи</font>
						<?}else{
						}?>
						<div style="width: 100%;" align="right">
							<br>
							<button type="button" class="btn btn-lg btn-success">Добавить</button>
						</div>
					</div>
					<div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">222222</div>
				</div>
			<?}?>
		</div>
	</body>
</html>