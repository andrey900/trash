<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<script>
	$(function(){
		$("#accordion .panel:first")
			.find(".panel-collapse")
			.addClass("in");
		setTimeout(function(){
			$("#accordion .panel")
				.not(":first")
				.find(".panel-title > a")
				.addClass("collapsed");
		}, 200);
	})
</script>
<div class="container">
	<div id="accordion" class="panel-group adres LK">
		<?
			$arIncludeAreas = Array(
					"delivery_and_payment" => "/bitrix/templates/main/include_files/delivary_and_payment_inc.php",
					"contacts" => "/bitrix/templates/main/include_files/contact_page_inc.php",
					"about" => "/bitrix/templates/main/include_files/about_project_page_inc.php"
			);
			
			$activeIncludeAreasDir = activeIncludeAreas();
			foreach ($arIncludeAreas as $dir => $pathIncludeAreas){
				if (strpos($activeIncludeAreasDir, $dir) !== false){
					$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => $pathIncludeAreas,
							"EDIT_TEMPLATE" => ""
					),false);
				}else{?>
					<script>
						$(function(){
							$.post("<?=$pathIncludeAreas?>", "", function(data){
								$("#accordion").append(data);
							})
							
						});
					</script>
				<?}
			}
		?>
	</div>
</div>
  
  