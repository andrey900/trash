
/*Additional scripts of header*/

//$(window).load(function()
$(function()
{
	$(".webstat-select_field").click(function()
	{
		$(".webstat-select_list").slideToggle("fast");
	});
	
	$("ul.webstat-select_list li").click(function()
	{
		var selfield = $(this).html();
		$(".webstat-select_list").slideUp("fast");
		$(".webstat-select_field span").html(selfield);
		
		var project_id = $("#webstat-project_id").html();
		var engine_id = $(this).attr("alt");
		var path = $("#webstat-template_path").html();
		//selfield = encodeURI(selfield);
		
		var request = "?project_id="+project_id+"&engine_id="+engine_id+"&path="+path+"&engine_name="+selfield;
		
		jsAjaxUtil.InsertDataToNode(path+"/ajax/main.php"+request, "webstat-ajax1", true);
	//});
	}).filter(":first").click();

	$(".webstat-fbox").fancybox(
	{
		openEffect : 'elastic',
		openSpeed  : 500,
		closeEffect : 'elastic',
		closeSpeed  : 500,
		scrolling	: 'no',
		helpers : {overlay : {locked : false}}
	});

});
