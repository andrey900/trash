<?php
$settings = include $_SERVER['DOCUMENT_ROOT'].'/bitrix/.settings.php';

$dbSettings = $settings['connections']['value']['default'];
$mysqli = new mysqli($dbSettings['host'], $dbSettings['login'], $dbSettings['password'], $dbSettings['database']);
$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT CODE FROM `b_iblock_element` WHERE `IBLOCK_ID` = ? AND `ACTIVE` = ?");
$stmt->bind_param("is", $id1='3', $a="Y");
$stmt->execute();
$stmt->bind_result($code);
$strBrands = [];
while ($stmt->fetch()) {
    $strBrands[] = $code;
}
$strBrands = implode('|', $strBrands);

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT CODE FROM `b_iblock_section` WHERE `IBLOCK_ID` = ? AND `ACTIVE` = ?");
$stmt->bind_param("is", $id1='4', $a="Y");
$stmt->execute();
$stmt->bind_result($code);
$strSections = [];
while ($stmt->fetch()) {
    $strSections[] = $code;
}
$strSections = implode('|', $strSections);
$mysqli->close();

if( $strBrands )
	return $strBrands.'|'.$strSections;
else
	return "";