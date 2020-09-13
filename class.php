<?namespace Dcloud\TASKs;

class Main{
	public static function getUsers(){
		$USERs=["IBLOCK_ID"=>0, "USERS"=>[]];
		
		$ITEMs=\Bitrix\Iblock\IblockTable::getList(['filter'=>['IBLOCK_TYPE_ID'=>'references', 'LID'=>SITE_ID, 'CODE'=>'dcloudUSERs']]);
		if($ITEMs->getSelectedRowsCount()==0){
			$TABLE=\Bitrix\Iblock\IblockTable::Add([
				'fields'=>[ 
					'LID'=>SITE_ID, 
					'CODE'				=>	'dcloudUSERs', 
					'NAME'				=>	'Пользователи планировщика задач',
					'IBLOCK_TYPE_ID'	=>	'references',
					'LIST_PAGE_URL'		=>	'#SITE_DIR#/references/index.php?ID=#IBLOCK_ID#',
					'DETAIL_PAGE_URL'	=>	'#SITE_DIR#/references/detail.php?ID=#ELEMENT_ID#'
			]]);

			$USERs["IBLOCK_ID"]=$TABLE->getID();
			if(!empty($USERs["IBLOCK_ID"])){
				\Bitrix\Iblock\IblockSiteTable::Add([
					'fields'=>[
						'SITE_ID'=>SITE_ID,
						'IBLOCK_ID'=>$USERs["IBLOCK_ID"]
					]
				]);
				
				
				
				$PROPs=[
					'POST'		=>	['NAME'=>'Должность сотрудника',	'IS_REQUIRED'=>'Y'],
					'LAST_NAME'	=>	['NAME'=>'Фамилия сотрудника',		'IS_REQUIRED'=>'N']
				];

				foreach($PROPs as $KEY=>$VALUE){
					\Bitrix\Iblock\PropertyTable::Add([
						'fields'=>[
							'IBLOCK_ID'		=>	$USERs["IBLOCK_ID"],
							'CODE'			=>	$KEY,
							'NAME'			=>	$VALUE['NAME'],
							'IS_REQUIRED'	=>	$VALUE['IS_REQUIRED']
						]
					]);
				}
			}
		}else{
			$ITEM=$ITEMs->Fetch();
			$USERs["IBLOCK_ID"]=$ITEM['ID'];
		}
		
		$ELEMENTs=\Bitrix\Iblock\ElementTable::getList(['filter'=>['IBLOCK_ID'=>$USERs["IBLOCK_ID"]]]);
		while($ELEMENT=$ELEMENTs->Fetch()){
			if($ITEM['ACTIVE']=='Y'){
				$USERs["USERS"][]=['NAME'=>$ELEMENT['NAME'], 'LAST_NAME'=>$ELEMENT['LAST_NAME'], 'POST'=>$ELEMENT['POST']];
			}
		}
		
		return $USERs;
	}
	
	public static function getTasks($usersIBLOCK_ID){
		$TASKs=["IBLOCK_ID"=>0, "TASKS"=>[]];
		$ITEMs=\Bitrix\Iblock\IblockTable::getList(['filter'=>['IBLOCK_TYPE_ID'=>'references', 'LID'=>SITE_ID, 'CODE'=>'dcloudTASKs']]);
		if($ITEMs->getSelectedRowsCount()==0 and $usersIBLOCK_ID>0){
			$TABLE=\Bitrix\Iblock\IblockTable::Add([
				'fields'=>[ 
					'LID'=>SITE_ID, 
					'CODE'				=>	'dcloudTASKs', 
					'NAME'				=>	'Планировщик задач',
					'IBLOCK_TYPE_ID'	=>	'references',
					'LIST_PAGE_URL'		=>	'#SITE_DIR#/references/index.php?ID=#IBLOCK_ID#',
					'DETAIL_PAGE_URL'	=>	'#SITE_DIR#/references/detail.php?ID=#ELEMENT_ID#'
			]]);
			$TASKs["IBLOCK_ID"]=$TABLE->getID();
			
			if(!empty($TASKs["IBLOCK_ID"])){
				global $DB;
				\Bitrix\Iblock\IblockSiteTable::Add([
					'fields'=>[
						'SITE_ID'=>SITE_ID,
						'IBLOCK_ID'=>$TASKs["IBLOCK_ID"]
					]
				]);
				
				$PROPs=[
					'STATUS'=>[
						'NAME'			=>	'Статус',
						'IS_REQUIRED'	=>	'Y',
						'PROPERTY_TYPE'	=>	'L',
						'MULTIPLE'		=>	'N',
						'VALUES'		=>	[
							['NAME'	=>	'Открыта',		'DEF'	=>	'Y',	'XML_ID'=>'dcloudTaskStatusOpen'],
							['NAME'	=>	'В работе',		'DEF'	=>	'N',	'XML_ID'=>'dcloudTaskStatusWork'],
							['NAME'	=>	'Завершена',	'DEF'	=>	'N',	'XML_ID'=>'dcloudTaskStatusClose']
						]
					],
					'RESPONSIBLE'=>[
						'NAME'				=>'Ответственный',
						'IS_REQUIRED'		=>'Y',
						'PROPERTY_TYPE'		=>'E',
						'MULTIPLE'			=>'N',
						'LINK_IBLOCK_ID'	=>$usersIBLOCK_ID
					]
				];

				foreach($PROPs as $KEY=>$VALUE){
					$FIELDs=[
						'IBLOCK_ID'		=>	$TASKs["IBLOCK_ID"],
						'CODE'			=>	$KEY,
						'NAME'			=>	$VALUE['NAME'],
						'IS_REQUIRED'	=>	$VALUE['IS_REQUIRED'],
						'PROPERTY_TYPE'	=>	$VALUE['PROPERTY_TYPE'],
						'MULTIPLE'		=>	$VALUE['MULTIPLE']
					];
					if(!empty($VALUE['LINK_IBLOCK_ID'])){
						$FIELDs['LINK_IBLOCK_ID']=$VALUE['LINK_IBLOCK_ID'];
					}
					$FIELD=\Bitrix\Iblock\PropertyTable::Add(['fields'=>$FIELDs]);
					if(!empty($VALUE['VALUES']) and !empty($FIELD->getID())){
						for($a=0; $a<count($VALUE['VALUES']); $a++){
							$QUERY="
								INSERT INTO
									`b_iblock_property_enum`(
										`PROPERTY_ID`,
										`VALUE`,
										`DEF`,
										`XML_ID`
									)VALUES(
										'".$FIELD->getID()."',
										'".$VALUE['VALUES'][$a]["NAME"]."',
										'".$VALUE['VALUES'][$a]["DEF"]."',
										'".$VALUE['VALUES'][$a]["XML_ID"]."'
									)";
							$DB->Query($QUERY);
						}
					}
				}
			}
		}else{
			$ITEM=$ITEMs->Fetch();
			$TASKs["IBLOCK_ID"]=$ITEM['ID'];
		}
		
		$ELEMENTs=\Bitrix\Iblock\ElementTable::getList(['filter'=>['IBLOCK_ID'=>$TASKs["IBLOCK_ID"]]]);
		while($ELEMENT=$ELEMENTs->Fetch()){
			if($ITEM['ACTIVE']=='Y'){
				$curArr=['NAME'=>$ELEMENT['NAME'], 'RESPONSIBLE'=>'', 'STATUS'=>''];
				$PROPs=\CIBlockElement::GetProperty($TASKs["IBLOCK_ID"], $ELEMENT['ID'], Array(), Array('CODE'=>'STATUS'));
				$PROP=$PROPs->Fetch();
				if(!empty($PROP['VALUE'])){
					$curArr['STATUS']=$PROP['VALUE'];
				}
				
				$PROPs=\CIBlockElement::GetProperty($TASKs["IBLOCK_ID"], $ELEMENT['ID'], Array(), Array('CODE'=>'RESPONSIBLE'));
				$PROP=$PROPs->Fetch();
				if(!empty($PROP['VALUE'])){
					$curArr['RESPONSIBLE']=$PROP['VALUE'];
				}
				
				$TASKs["TASKS"][]=$curArr;
			}
		}
		
		return $TASKs;
	}
}?>