
/*Additional scripts of header*/

//jq110(window).load(function()
jq110(function()
{
	jq110(".webstat-select_field").click(function()
	{
		jq110(".webstat-select_list").slideToggle("fast");
	});
	
	jq110("ul.webstat-select_list li").click(function()
	{
		var selfield = jq110(this).html();
		jq110(".webstat-select_list").slideUp("fast");
		jq110(".webstat-select_field span").html(selfield);
		
		var project_id = jq110("#webstat-project_id").html();
		var engine_id = jq110(this).attr("alt");
		var path = jq110("#webstat-template_path").html();
		//selfield = encodeURI(selfield);
		
		var request = "?project_id="+project_id+"&engine_id="+engine_id+"&path="+path+"&engine_name="+selfield;
		
		jsAjaxUtil.InsertDataToNode(path+"/ajax/main.php"+request, "webstat-ajax1", true);
	//});
	}).filter(":first").click();

	jq110(".webstat-fbox").fancybox(
	{
		openEffect : 'elastic',
		openSpeed  : 500,
		closeEffect : 'elastic',
		closeSpeed  : 500,
		scrolling	: 'no',
		helpers : {overlay : {locked : false}}
	});

});
