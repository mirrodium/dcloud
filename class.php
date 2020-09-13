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
					'POST'		=>	['NAME'=>'Должность сотрудника', 'IS_REQUIRED'=>'Y'],
					'LAST_NAME'	=>	['NAME'=>'Фамилия сотрудника', 'IS_REQUIRED'=>'N']
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
				$USERs["USERS"][]=['NAME'=>$ITEM['NAME'], 'LAST_NAME'=>$ITEM['LAST_NAME'], 'POST'=>$ITEM['POST']];
			}
		}
		
		return $USERs;
	}
}?>