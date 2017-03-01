<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';

$strReturn .= '<div class="bx-breadcrumb">';

$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);

	$nextRef = ($index < $itemSize-2 && $arResult[$index+1]["LINK"] <> ""? ' itemref="bx_breadcrumb_'.($index+1).'"' : '');
	$child = ($index > 0? ' itemprop="child"' : '');
	$arrow = ($index > 0? '<i class="fa fa-angle-right"></i>' : '');

	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
	{
		$strReturn .= '
			<div class="bx-breadcrumb-item" id="bx_breadcrumb_'.$index.'" itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"'.$child.$nextRef.'>
				'.$arrow.'
				<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="url">
					<span itemprop="title">'.$title.'</span>
				</a>
			</div>';
	}
	else
	{
		$strReturn .= '
			<div class="bx-breadcrumb-item">
				'.$arrow.'
				<span>'.$title.'</span>
			</div>';
	}
}

$strReturn .= '<div style="clear:both"></div></div>';
// return $strReturn;

$strReturn = "<!-- BREADCRUMBS SETCTION END -->\r\n";
$strReturn .= '<div class="breadcrumbs-section mb-10">';
$strReturn .= '<div class="container-fluid">';
$strReturn .= '<div class="row plr-185">';
$strReturn .= '<div class="col-md-12">';
$strReturn .= '<ol class="breadcrumb p-10" itemscope itemtype="http://schema.org/BreadcrumbList">';
$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	$link = $arResult[$index]["LINK"];

	$actClass = "";
	$type = "a";
	if($arResult[$index]["LINK"] == "" && $index == $itemSize-1)
	{
		$actClass = 'class="active"';
		$type = 'span';
	}

	$strReturn .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" '.$actClass.'>';
	$type = ($actClass)?"span":"a";
	$strReturn .= '<'.$type.' itemprop="item" href="'.$link.'">';
	$strReturn .= '<span itemprop="name">'.$title.'</span>';
	$strReturn .= '</span>';
	$strReturn .= '</'.$type.'>';
	$strReturn .= '</li>';
}
$strReturn .= '</ol></div></div></div></div>';
$strReturn .= "\r\n<!-- End page content -->";

return $strReturn;