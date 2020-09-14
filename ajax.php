<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

require_once('class.php');

if(!empty($_POST['proc'])){
	echo 123;
}