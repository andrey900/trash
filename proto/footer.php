<!-- Подвал -->
<footer>
	<div class="container">
		<div class="row">
			<?$APPLICATION->IncludeComponent("bitrix:menu","bottomMenu",Array(
				"ROOT_MENU_TYPE" => "bottomMenu",
				"MAX_LEVEL" => "1",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "Y",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => ""
			)
			);?>
			<!-- Один Блок -->
			<div class="col-md-6 col-sm-6 col-xs-12 one-foot f-2">
				<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list","",
				Array(
				        "IBLOCK_TYPE" => "catalog",
				        "IBLOCK_ID" => IBLOCK_PRODUCT_ID,
				        "SECTION_ID" => "",
				        "SECTION_CODE" => "",
				        "SECTION_URL" => "",
				        "COUNT_ELEMENTS" => "Y",
				        "TOP_DEPTH" => "2",
				        "SECTION_FIELDS" => "",
				        "SECTION_USER_FIELDS" => "",
				        "ADD_SECTIONS_CHAIN" => "Y",
				        "CACHE_TYPE" => "A",
				        "CACHE_TIME" => "36000000",
				        "CACHE_NOTES" => "",
				        "CACHE_GROUPS" => "Y"
				    )		
				);?>
			</div>
			<!-- Конец Один Блок -->
			<!-- Один Блок -->
			<div class="col-md-3 col-sm-3 col-xs-12 one-foot f-3">
				<div class="copyright">
					<?$APPLICATION->IncludeFile("/bitrix/templates/main/include_files/footer_copyright_inc.php");?>
				</div>
			</div>
			<!-- Конец Один Блок -->
		</div>
	</div>
</footer>
</body>
</html>