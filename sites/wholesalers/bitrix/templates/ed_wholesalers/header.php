<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
// что б так платили как гонят ф-л и работу! (((
include 'lib.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<?$APPLICATION->ShowHead()?>

    <title><?$APPLICATION->ShowTitle()?></title>

	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bootstrap.min.css');?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/style.css');?>
	
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-2.2.0.min.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap.min.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/easy-autocomplete.min.js");?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/script.js");?>
</head>

<body>
<div class="panel"><?$APPLICATION->ShowPanel();?></div>

<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
	<div class="navbar-header">
		<button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		</button>
		<a href="/" class="navbar-brand"><img alt="Brand" src="/upload/medialibrary/093/logo2.png"> Electrodom</a>
	</div>
	<?if( $GLOBALS['USER']->IsAuthorized() &&
		  in_array(9, $GLOBALS['USER']->GetUserGroupArray())
	):?>
	<div class="navbar-collapse collapse" id="navbar">
		<ul class="nav navbar-nav">
			<li><a href="/wholesalers/">Скачать остатки</a></li>
		</ul>
		<form class="navbar-form navbar-left search-form" role="search">
			<div class="input-group">
				<input type="text" id="search-field" class="form-control" placeholder="Начните вводить артикул товара, например: 3254">
				<span class="input-group-btn">
					<button class="btn btn-default ajax-search" type="button">
						<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
						<span class="search-loading hidden">search... <img src="/wholesalers/loading.gif" height="18"></span>
					</button>
				</span>
			</div><!-- /input-group -->
		</form>
		<ul class="nav navbar-nav navbar-right">
			<li><a href="?logout=Y"><span class="glyphicon glyphicon-off" aria-hidden="true"></span> Logout</a></li>
		</ul>
	</div><!--/.nav-collapse -->
	<?endif;?>
</nav>
<div class="container main-content">