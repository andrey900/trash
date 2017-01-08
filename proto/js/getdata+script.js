$.get( "<?=CUrlExt::GetCollectionURL($arResult['PROPERTIES']['CML2_LINK']['VALUE']);?>", function( data ) {
    $('.ajax-collection').append( $(data).find('.collection-elements ul li').addClass('collection-item') );
    var scriptTooltip = "";
    $(data).filter('.tooltip-ajax-data').each(function(){
        scriptTooltip += $(this).html();
    });
    
var docHead = document.getElementsByTagName("head")[0]; //head of Page A
var newScript = document.createElement("script");
newScript.setAttribute("type","text/javascript");
newScript.innerHTML = scriptTooltip; //insert plain text JS into script element
docHead.appendChild(newScript); //append script element to head of Page A

}); 
