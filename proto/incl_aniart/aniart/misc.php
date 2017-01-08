<?
/**
 * Функция генерирует пароль по номеру теелфона
 * @param string $phonenumber
 * @return string
 */
function CreateUserPassword($phonenumber){
	$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
	if($def_group!="")
	{
		$GROUP_ID = explode(",", $def_group);
		$arPolicy = CUser::GetGroupPolicy($GROUP_ID);
	}
	else
	{
		$arPolicy = CUser::GetGroupPolicy(array());
	}

	$password_min_length = intval($arPolicy["PASSWORD_LENGTH"]);
	if($password_min_length <= 0)
		$password_min_length = 6;
	$password_chars = array(
			"abcdefghijklnmopqrstuvwxyz",
			"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
			"0123456789",
	);
	if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
		$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";

	return randString($password_min_length+2, $password_chars);
}

// функция определяет активную вкл область для поисковых роботов в зависимости о текущего урла
function activeIncludeAreas(){
	$url = parse_url ($_SERVER["REQUEST_URI"]);
	$currentPath = $url["path"];
	if($currentPath == $_SERVER["REQUEST_URI"]){
		return $currentPath;
	}else{
		return false;
	}
}

// вертает true если юзер с указаным email существует
function isUsserIsset($email){
	$arFilter = Array(
		"EMAIL" => $email
	);
	
	$user = CUser::GetList(($by="id"), ($order="desc"),$arFilter);
	if($userInfo = $user->Fetch()){
		return $userInfo;
	}else {
		return false;
	}
}
// возвращает список всех email
function getUserEmailList(){
	$arFilter = Array(
		"ACTIVE" => "Y"
	);
	
	$user = CUser::GetList(($by="id"), ($order="desc"),$arFilter);
	while($userInfo = $user->Fetch()){
			$emailList [] = $userInfo["EMAIL"];
	}
	return $emailList;
}

/**
 * Function checked password curr user
 * @pass 	 - no crypt pass
 * @hashPass - hash pass DB ($USER->GetParam("PASSWORD_HASH"))
 **/
function checkPassword($pass='', $hashPass=''){
	
	if( empty($pass) || empty($hashPass))
		return false;

	$salt = substr($hashPass,0,8);
 	
 	if( $salt.md5($salt.$pass) ==  $hashPass)
 		return true;
 	else 
 		return false;
}

/**
 *  функция возвращает секции из отмеченным пользовательским свойством секции, 
 *  и считает количество активных товаров в секции
 */
function getTopMenuSections()
{
	return getSectionListByIblockID(IBLOCK_PRODUCT_ID, array('ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', '!UF_SHOW_IN_TOP_MENU' => false, 'CNT_ACTIVE' => true));
}

function getSectionListByIblockID($IBlockID, $arFilter = array(), $arSort = array())
{
	$arFilter = array_merge(array('IBLOCK_ID' => $IBlockID), $arFilter);
	if(empty($arSort)){
		$arSort = array('SORT' => 'ASC', 'NAME' => 'ASC');
	}
	$sectionList = array();
	$sectionDB = CIBlockSection::GetList($arOrder, $arSectionFilter, true, $arSelect);
	while($section = $sectionDB->GetNext()){
		$sectionList[$section["ID"]] = $section;
	}
	return $sectionList;
}

/**
 * Проверяет есть ли у файла(картинки) атрибут безопасности
 * (если атрибута нет - устанавливаем <img worksafe="Y" и не показываем картинку на сайте!)
 */
function isPictureWorksafe($File)
{
	if(is_int($File)){
		$File		= (int)$File;
		$FileData	= CFile::GetFileArray($File);
	}
	elseif(is_array($File)){
		$FileData 	= $File;	
	}
	
	return $FileData['DESCRIPTION'] != 'worksafe';
}

/**
 * Выводит блок с ценами
 */
function showProductPricesHtml($BasePrice, $DiscountPrice, $Type = 'default')
{
	$BasePrice		= round($BasePrice, 2);
	$DiscountPrice	= round($DiscountPrice, 2);
	if($Type == 'default'):
?>
	<div class="one-it-price">
		<span <?if($DiscountPrice > 0):?>class="cross-price"<?endif?>><?=$BasePrice?></span><?if($DiscountPrice > 0):?> <?=$DiscountPrice?><?endif;?>
		.–
	</div>
<?
	elseif($Type == 'main'):
?>
	<div class="top-pr">
		<span <?if($DiscountPrice > 0):?>class="cross-price"<?endif?>><?=$BasePrice?></span><?if($DiscountPrice > 0):?> <?=$DiscountPrice?><?endif;?>
		.–
	</div>
<?
	elseif($Type == 'monthly'):
?>
	<div class="sing-pric">
		<span <?if($DiscountPrice > 0):?>class="cross-price"<?endif?>><?=$BasePrice?></span><?if($DiscountPrice > 0):?> <?=$DiscountPrice?><?endif;?>
		.–
	</div>
<?
	elseif($Type == 'detail'):
?>
	<div class="det-slid sing-by">
		<a href="#productOffers">купить за <span <?if($DiscountPrice > 0):?>class="cross-price"<?endif?>><?=$BasePrice?></span><?if($DiscountPrice > 0):?> <?=$DiscountPrice?><?endif;?>
		.–
		</a>
	</div>
<?
	elseif($Type == 'detail-feature'):
?>
	<div class="sing-by-now">
		<a href="#productOffers">купить за <span <?if($DiscountPrice > 0):?>class="cross-price"<?endif?>><?=$BasePrice?></span><?if($DiscountPrice > 0):?> <?=$DiscountPrice?><?endif;?>
		.–
		</a>
	</div>
<?
	endif;
}

/**
 * Проверяет является ли страница главной
 */
function changeColorByPath()
{
	global $APPLICATION;
	if($APPLICATION->GetCurPage(true) == '/index.php' || $APPLICATION->GetCurPage(true) == '/special-offers/index.php'){
		return true;
	}else{
		return false;
	}
}

/**
 *Возвращает массив с цветами 
 */
function GetColorsList()
{
	$Colors = array();
	$obCache = new CPHPCache;
	if($obCache->InitCache(3600, 'Aniart.Colors', "/")){
		$Vars	= $obCache->GetVars();
		$Colors = $Vars['COLORS'];
	}
	elseif($obCache->StartDataCache()){
		$rsColors = CIBlockElement::GetList(array(), array('ACTIVE' => 'Y', 'IBLOCK_ID' => IBLOCK_COLORS_ID),
			false, false,
			array('ID', 'IBLOCK_ID', 'NAME', 'CODE')
		);
		while($arColor = $rsColors->GetNext()){
			$Colors[] = array(
				'ID'	=> $arColor['ID'],
				'NAME'	=> $arColor['~NAME'],
				'CODE'	=> $arColor['CODE']
			);
		}
		$obCache->EndDataCache(array('COLORS' => $Colors));
	}
	return $Colors;
}

/**
 * Возвращает цвет по его ID
 */
function GetColorByID($ColorID, $JustCode = false)
{
	$ColorID = (int)$ColorID;
	if($ColorID > 0){
		foreach(GetColorsList() as $Color){
			if($Color['ID'] == $ColorID){
				if($JustCode){
					return $Color['CODE'];
				}
				return $Color;
			}
		}
	}
	return false;
}

/**
 * Возвращает случайный цвет из списка всех цветов
 */
function GetRandomColor($JustCode = false)
{
	$Colors = GetColorsList();
	if(!empty($Colors)){
		$Color = $Colors[rand(0, count($Colors)-1)];
		if($JustCode && !empty($Color['CODE'])){
			return $Color['CODE'];
		}
		else{
			return $Color;
		}
	}
	
	return false;
}

/**
 * Возвращает идентификаторы товаров, которые являются "сопутствующими" для указанной секции
 */
function GetRelatedProductsIDForSection($Section)
{
	$RelatedProducts = array();
	$arFilter = array('IBLOCK_ID' => IBLOCK_PRODUCT_ID);
	if(is_numeric($Section)){
		$arFilter['ID'] = $Section;
	}
	else{
		$arFilter['CODE'] = $Section;
	}
	if(count($arFilter) > 1){
		$rsSections = CIBlockSection::GetList(array(), $arFilter, false, array('IBLOCK_ID', 'ID', 'UF_RELATED_PRODUCTS'));
		if($arSection = $rsSections->Fetch()){
			$RelatedProducts = $arSection['UF_RELATED_PRODUCTS'];
		}
	}
	
	return $RelatedProducts;
}

/**
 * Меняет размер картинки и перезаписывает соотв. поля новыми значениями
 */
function ResizeInitialPicture($Picture, $Width, $Height, $ResizeType = BX_RESIZE_IMAGE_PROPORTIONAL_ALT)
{
	if(!empty($Picture)){
		if(!is_array($Picture)){
			$Picture = CFile::GetFileArray($Picture);
		}
		if($Picture['WIDTH'] > $Width || $Picture['HEIGHT'] > $Height){
			$ResizedPicture = CFile::ResizeImageGet($Picture, array(
				'width'		=> $Width,
				'height'	=> $Height
			), $ResizeType, true);
			if(!empty($ResizedPicture)){
				$Picture['WIDTH'] = $ResizedPicture['width'];
				$Picture['HEIGHT'] = $ResizedPicture['height'];
				$Picture['SRC'] = $ResizedPicture['src'];

				return $Picture;
			}
		}
	}
	
	return false;
}

?>