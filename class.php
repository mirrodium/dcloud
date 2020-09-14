<?namespace DCloud\TASKs;

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
				$curArr=['ID'=>$ELEMENT['ID'], 'NAME'=>$ELEMENT['NAME'], 'LAST_NAME'=>'', 'POST'=>''];
				$PROPs=\CIBlockElement::GetProperty($USERs["IBLOCK_ID"], $ELEMENT['ID'], Array(), Array('CODE'=>'POST'));
				$PROP=$PROPs->Fetch();
				if(!empty($PROP['VALUE'])){
					$curArr['POST']=$PROP['VALUE'];
				}
				$PROPs=\CIBlockElement::GetProperty($USERs["IBLOCK_ID"], $ELEMENT['ID'], Array(), Array('CODE'=>'LAST_NAME'));
				$PROP=$PROPs->Fetch();
				if(!empty($PROP['VALUE'])){
					$curArr['LAST_NAME']=$PROP['VALUE'];
				}
				
				$USERs["USERS"][]=$curArr;
			}
		}
		
		return $USERs;
	}
	
	public static function addUser($PARAMs){
		global $USER;
		$MESSAGE="error";
		
		if(!empty($PARAMs['IBLOCK_ID']) and !empty($PARAMs['NAME']) and !empty($PARAMs['POST'])){
			$ELEMENTs=new \CIBlockElement;
			$FIELDS=[
				'MODIFIED_BY'		=>	$USER->GetID(),
				'IBLOCK_ID'			=>	$PARAMs["IBLOCK_ID"],
				'NAME'				=>	$PARAMs['NAME'],
				'PROPERTY_VALUES'	=> []
			];
			
			$FIELD_IDs=\Bitrix\Iblock\PropertyTable::getList(['filter'=>['CODE'=>'POST', 'IBLOCK_ID'=>$PARAMs["IBLOCK_ID"]]]);
			$FIELD_ID=$FIELD_IDs->Fetch();
			if(!empty($FIELD_ID['ID'])){
				$FIELDS['PROPERTY_VALUES'][$FIELD_ID['ID']]=$PARAMs['POST'];
			}
			
			if(!empty($PARAMs['LAST_NAME'])){
				$FIELD_IDs=\Bitrix\Iblock\PropertyTable::getList(['filter'=>['CODE'=>'LAST_NAME', 'IBLOCK_ID'=>$PARAMs["IBLOCK_ID"]]]);
				$FIELD_ID=$FIELD_IDs->Fetch();
				if(!empty($FIELD_ID['ID'])){
					$FIELDS['PROPERTY_VALUES'][$FIELD_ID['ID']]=$PARAMs['LAST_NAME'];
				}
			}
			
			$ELEMENTs->Add($FIELDS);
			if(empty($ELEMENTs->LAST_ERROR)){
				$MESSAGE='success';
			}
		}else{
			$MESSAGE="any params not found";
		}
		
		return $MESSAGE;
	}
	
	public static function editUser($PARAMs){
		global $USER;
		$MESSAGE="error";
		
		if(!empty($PARAMs['IBLOCK_ID']) and !empty($PARAMs['ID'])){
			$ELEMENTs=new \CIBlockElement;
			$FIELDS=[
				'MODIFIED_BY'		=>	$USER->GetID(),
				'IBLOCK_ID'			=>	$PARAMs["IBLOCK_ID"],
				'NAME'				=>	$PARAMs['NAME'],
				'PROPERTY_VALUES'	=> []
			];
			
			$FIELD_IDs=\Bitrix\Iblock\PropertyTable::getList(['filter'=>['CODE'=>'POST', 'IBLOCK_ID'=>$PARAMs["IBLOCK_ID"]]]);
			$FIELD_ID=$FIELD_IDs->Fetch();
			if(!empty($FIELD_ID['ID'])){
				$FIELDS['PROPERTY_VALUES'][$FIELD_ID['ID']]=$PARAMs['POST'];
			}
			
			if(!empty($PARAMs['LAST_NAME'])){
				$FIELD_IDs=\Bitrix\Iblock\PropertyTable::getList(['filter'=>['CODE'=>'LAST_NAME', 'IBLOCK_ID'=>$PARAMs["IBLOCK_ID"]]]);
				$FIELD_ID=$FIELD_IDs->Fetch();
				if(!empty($FIELD_ID['ID'])){
					$FIELDS['PROPERTY_VALUES'][$FIELD_ID['ID']]=$PARAMs['LAST_NAME'];
				}
			}
			
			$ELEMENTs->Update($PARAMs['ID'], $FIELDS);
			if(empty($ELEMENTs->LAST_ERROR)){
				$MESSAGE='success';
			}
		}else{
			$MESSAGE="any params not found";
		}
		
		return $MESSAGE;
	}
	
	public static function deleteUser($PARAMs){
		global $DB;
		$MESSAGE="error";
		if(!empty($PARAMs['TASKS_IBLOCK_ID']) and !empty($PARAMs['IBLOCK_ID']) and !empty($PARAMs['ID'])){
			$PROPs=\CIBlockElement::GetList(Array(), ['IBLOCK_ID'=>$PARAMs['TASKS_IBLOCK_ID'], 'PROPERTY_RESPONSIBLE'=>$PARAMs['ID']]);
			$PROP=$PROPs->Fetch();
			if($PROPs->selectedRowsCount()>0){
				$MESSAGE="tasksFound";
			}else{
				if(\CIBlock::GetPermission($PARAMs['IBLOCK_ID'])>='W'){
					$DB->StartTransaction();
					if(!\CIBlockElement::Delete($PARAMs['ID'])){
						$strWarning.='Error!';
						$DB->Rollback();
					}else
						$DB->Commit();
				}
			}
		}else{
			$MESSAGE="any params not found";
		}
		return $MESSAGE;
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
						'MULTIPLE'			=>'Y',
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
				$curArr=['ID'=>$ELEMENT['ID'], 'NAME'=>$ELEMENT['NAME'], 'RESPONSIBLE'=>[], 'STATUS'=>''];
				$PROPs=\CIBlockElement::GetProperty($TASKs["IBLOCK_ID"], $ELEMENT['ID'], Array(), Array('CODE'=>'STATUS'));
				$PROP=$PROPs->Fetch();
				if(!empty($PROP['VALUE'])){
					$curArr['STATUS']=$PROP['VALUE'];
				}
				
				$PROPs=\CIBlockElement::GetProperty($TASKs["IBLOCK_ID"], $ELEMENT['ID'], Array(), Array('CODE'=>'RESPONSIBLE'));
				while($PROP=$PROPs->Fetch()){
					if(!empty($PROP['VALUE'])){
						$curArr['RESPONSIBLE'][]=$PROP['VALUE'];
					}
				}
				
				$TASKs["TASKS"][]=$curArr;
			}
		}
		
		return $TASKs;
	}
	
	public static function addTask($PARAMs){
		global $USER;
		$MESSAGE="error";
		if(!empty($PARAMs['IBLOCK_ID']) and !empty($PARAMs['NAME']) and !empty($PARAMs['STATUS']) and !empty($PARAMs['RESPONSIBLE'])){
			$ELEMENTs=new \CIBlockElement;
			$FIELDS=[
				'MODIFIED_BY'		=>	$USER->GetID(),
				'IBLOCK_ID'			=>	$PARAMs["IBLOCK_ID"],
				'NAME'				=>	$PARAMs['NAME'],
				'PROPERTY_VALUES'	=> []
			];
			
			$FIELD_IDs=\Bitrix\Iblock\PropertyTable::getList(['filter'=>['CODE'=>'STATUS', 'IBLOCK_ID'=>$PARAMs["IBLOCK_ID"]]]);
			$FIELD_ID=$FIELD_IDs->Fetch();
			if(!empty($FIELD_ID['ID'])){
				$FIELDS['PROPERTY_VALUES'][$FIELD_ID['ID']]=$PARAMs['STATUS'];
			}
			
			$FIELD_IDs=\Bitrix\Iblock\PropertyTable::getList(['filter'=>['CODE'=>'RESPONSIBLE', 'IBLOCK_ID'=>$PARAMs["IBLOCK_ID"]]]);
			$FIELD_ID=$FIELD_IDs->Fetch();
			if(!empty($FIELD_ID['ID'])){
				$FIELDS['PROPERTY_VALUES'][$FIELD_ID['ID']]=$PARAMs['RESPONSIBLE'];
			}
			
			$FIELD_IDs=\Bitrix\Iblock\PropertyTable::getList(['filter'=>['CODE'=>'STATUS', 'IBLOCK_ID'=>$PARAMs["IBLOCK_ID"]]]);
			$FIELD_ID=$FIELD_IDs->Fetch();
			if(!empty($FIELD_ID['ID'])){
				$FIELDS['PROPERTY_VALUES'][$FIELD_ID['ID']]=$PARAMs['STATUS'];
			}
			
			$ELEMENTs->Add($FIELDS);
			if(empty($ELEMENTs->LAST_ERROR)){
				$MESSAGE='success';
			}
		}else{
			$MESSAGE="any params not found";
		}
		return $MESSAGE;
	}
	
	public static function editTask($PARAMs){
		global $USER;
		$MESSAGE="error";
		if(!empty($PARAMs['IBLOCK_ID']) and !empty($PARAMs['ID']) and !empty($PARAMs['NAME']) and !empty($PARAMs['STATUS']) and !empty($PARAMs['RESPONSIBLE'])){
			$ELEMENTs=new \CIBlockElement;
			$FIELDS=[
				'MODIFIED_BY'		=>	$USER->GetID(),
				'IBLOCK_ID'			=>	$PARAMs["IBLOCK_ID"],
				'NAME'				=>	$PARAMs['NAME'],
				'PROPERTY_VALUES'	=> []
			];
			
			$FIELD_IDs=\Bitrix\Iblock\PropertyTable::getList(['filter'=>['CODE'=>'STATUS', 'IBLOCK_ID'=>$PARAMs["IBLOCK_ID"]]]);
			$FIELD_ID=$FIELD_IDs->Fetch();
			if(!empty($FIELD_ID['ID'])){
				$FIELDS['PROPERTY_VALUES'][$FIELD_ID['ID']]=$PARAMs['STATUS'];
			}
			
			$FIELD_IDs=\Bitrix\Iblock\PropertyTable::getList(['filter'=>['CODE'=>'RESPONSIBLE', 'IBLOCK_ID'=>$PARAMs["IBLOCK_ID"]]]);
			$FIELD_ID=$FIELD_IDs->Fetch();
			if(!empty($FIELD_ID['ID'])){
				$FIELDS['PROPERTY_VALUES'][$FIELD_ID['ID']]=$PARAMs['RESPONSIBLE'];
			}
			
			$FIELD_IDs=\Bitrix\Iblock\PropertyTable::getList(['filter'=>['CODE'=>'STATUS', 'IBLOCK_ID'=>$PARAMs["IBLOCK_ID"]]]);
			$FIELD_ID=$FIELD_IDs->Fetch();
			if(!empty($FIELD_ID['ID'])){
				$FIELDS['PROPERTY_VALUES'][$FIELD_ID['ID']]=$PARAMs['STATUS'];
			}
			
			$ELEMENTs->Update($PARAMs['ID'], $FIELDS);
			if(empty($ELEMENTs->LAST_ERROR)){
				$MESSAGE='success';
			}
		}else{
			$MESSAGE="any params not found";
		}
		
		return $MESSAGE;
	}
	
	public static function deleteTask($PARAMs){
		global $DB;
		$MESSAGE="error";
		if(!empty($PARAMs['IBLOCK_ID']) and !empty($PARAMs['ID'])){
			if(\CIBlock::GetPermission($PARAMs['IBLOCK_ID'])>='W'){
				$DB->StartTransaction();
				if(!\CIBlockElement::Delete($PARAMs['ID'])){
					$strWarning.='Error!';
					$DB->Rollback();
				}else{
					$DB->Commit();
				}
			}
		}else{
			$MESSAGE="any params not found";
		}
		return $MESSAGE;
	}
}?>