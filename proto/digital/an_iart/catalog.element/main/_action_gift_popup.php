<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
$(function(){
	$('.add_to_basket_with_gift').click(function(){
		
		var selected_action = $('#detail-jcarousel-gift').jcarousel('visible');
		period_hash = selected_action.attr("period_hash"); 
		var ar_product_id = [];
		
		$('.gift-info.detail-page [link_period_hash="'+period_hash+'"]').each(function( index) {
			var selected_product = $(this).jcarousel('visible');
			ar_product_id[index] = selected_product.attr("product_id");
		});


		$.post(
			"/catalog/ajax/add_to_basket_with_gift.php",
			{
				product_id: <?=$productInfo["ID"]?>,
				period_hash: period_hash,
				ar_product_id: ar_product_id,
				ar_gift_id: "<?=base64_encode(serialize($productInfo["PROPERTY_GIFTS_VALUE"]))?>",  
			},"html")
			.done(function( data ){
				$("#add-to-basket-answer").html(data);
				$('#add-to-basket-link').fancybox();
				$('#add-to-basket-link').trigger('click');

				setTimeout( function () { location.reload(); }, 3000);
			});
	});
	
	$('.add_to_basket_with_gift_fancy').fancybox();
});
</script>
