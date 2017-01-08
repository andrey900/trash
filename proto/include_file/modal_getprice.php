<?php
extract('arResult');//p($arResult,false);

$arStyle['form_color']       = ($arResult['form_color'])?$arResult['form_color']:'#CACACA';
$arStyle['btn_class_send']   = ($arResult['class_send'])?$arResult['class_send']:'';
$arStyle['btn_class_cancel'] = ($arResult['class_cancel'])?$arResult['class_cancel']:'';
$arStyle['use_css_animation']= ($arResult['use_css_animation']!='Y')?false:true;

$arResult['TITLE']          = (isset($arResult['TITLE']))?$arResult['TITLE']:'Модальное окно';
$arResult['FOOTER']         = (isset($arResult['FOOTER']))?$arResult['FOOTER']:'Краткое описание';
$arResult['INPUT_LABEL']    = (isset($arResult['INPUT_LABEL']))?$arResult['INPUT_LABEL']:'Заполните данное поле';
$arResult['BTN_SEND']       = (isset($arResult['BTN_SEND']))?$arResult['BTN_SEND']:'Отправить';
$arResult['BTN_CANCEL']     = (isset($arResult['BTN_CANCEL']))?$arResult['BTN_CANCEL']:'Отменить';
$arResult['BTN_CANCEL_YES'] = ($arResult['BTN_CANCEL_YES']!='Y')?false:true;

// from many include file
$hash = md5(serialize($arResult));
?>
<style>
/* Start modal style */
.overlay-bg {
    background-color: rgba(0, 0, 0, 0.4);
    display: none;
    height: 100%;
    left: 0;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
}
.modal-window {
    background-color: white;
    border: 5px solid <?=$arStyle['form_color'];?>;
    border-radius: 15px;
    left: 30%;
    padding: 15px;
    position: absolute;
    top: 30%;
    width: 500px;
    z-index: 1010;
}
.modal-window-close {
    background-color: #fff;
    border: 3px solid <?=$arStyle['form_color'];?>;
    border-radius: 50%;
    cursor: pointer;
    display: table-cell;
    float: right;
    height: 25px;
    margin-right: 15px;
    margin-top: -32px;
    text-align: center;
    vertical-align: middle;
    width: 25px;
    font-size: 20px;
}
.modal-window-close:hover {
    background-color: <?=$arStyle['form_color'];?>;
    color: white;
}
.modal-content {
    font-size: 14px;
    height: 100%;
    width: 100%;
}
.modal-content .default-popup-content{
    text-align: center;
}
/* End Modal style */

/* Answer style */
.modal-content .answer{
    text-align: center;
}
.answer-ok{
    color:green;
    text-align: center;
}
.answer-err{
    color:red;
    text-align: center;
}
/* End Answer style*/

<?if( $arStyle['use_css_animation'] ):?>
/* Start CSS animation style*/
#btn_send, #btn_cancel, .modal-window-close {
    -webkit-transition: background 0.1s linear;
    -moz-transition: background 0.1s linear;
    -ms-transition: background 0.1s linear;
    -o-transition: background 0.1s linear;
    transition: background 0.1s linear;
}
#btn_send:hover, #btn_cancel:hover{
    box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.3);
}
/* End CSS animation style */

#back_email{
    width: 30%;height: 25px;padding-left: 10px;
}
.info_oneclick{
    font-family: Arial; font-size: 14px;margin-top:15px;text-align: center;color: #706E6E;
}
#btn_send, #btn_cancel {
    margin-top: 30px;padding: 4px;float: right;margin-right: 15px;margin-bottom: -18px; border-radius: 5px 5px 0 0; border:none;
}
<?endif;?>

</style>
<script type="text/javascript">
    var getprice = {
        'id-elem':0,
    }

    function hideModal(){
        $('.overlay-bg').fadeOut('fast');
        $('.modal-content .answer').remove();
    }

    function showModal(claname){
        w_width   = $(window).width();
        w_height  = $(window).height();
        mw_width  = $('.overlay-bg.mw-'+claname+' .modal-window').width();
        mw_height = $('.overlay-bg.mw-'+claname+' .modal-window').height();
        
        m_top = w_height/2-100;//        m_top = w_height/2-mw_height/2;
        if( m_top < 0 )
            m_top = 10;
        m_left = w_width/2-mw_width/2;

        $('.overlay-bg.mw-'+claname+' .modal-window').css({top:m_top+'px',left:m_left+'px'});

        $('.overlay-bg.mw-'+claname).fadeToggle('fast');
        clearTimeout(getprice['timeout-'+claname]);
        $('.mw-'+claname+' .modal-content .answer').remove();
        $('.mw-'+claname+' .default-popup-content').show();
    }

    $(document).on('click', '<?=$arResult["JS_CLICK_ACTIVE"]?>', function(){
        showModal('<?=$hash?>');

        if( $(this).attr('id-elem') )
            getprice['id-elem-<?=$hash?>'] = parseInt($(this).attr('id-elem'));
        
    });
    $(document).on('click', '.modal-window-close', function(){
        hideModal();
    });
    $(document).on('keyup',function(evt) {
        if (evt.keyCode == 27 && $('.overlay-bg').is(':visible') ) {
           hideModal();
        }
    });
    $(document).on('click', '#btn_send', function(){
        
        var email = $('#back_email').val();
        var menuId = getprice['id-elem-<?=$hash?>'];
        if( !email ){
            alert('Укажите Ваш email для связи');
            return false;
        }

        var request = $.ajax({
            url: "<?=$arResult['AJAX_REQUEST']?>",
            type: "POST",
            data: { id : menuId, email : email },
            dataType: "json"
        });
        request.done(function( msg ) {
            $('.default-popup-content').hide();
            $('#back_email').val('');
            $( ".mw-<?=$hash?> .modal-content" ).append( $( '<div class="answer">'+msg.order.text+'</div>' ) );
            getprice['timeout-<?=$hash?>'] = setTimeout(hideModal, 5000);
        });
        request.fail(function( jqXHR, textStatus ) {
            alert( "Возникла ошибка: " + textStatus );
            });
        });
</script>
<div class="overlay-bg mw-<?=$hash?>" style="display: none;">
    <div class="modal-window" style="top: 100px; left: 200px;">
        <div class="modal-window-close">X</div>
        <div class="modal-content">
            <div class="default-popup-content">
                <h2><?=$arResult['TITLE'];?></h2>
                <div class="myinput">
                <div class="input" style="overflow: auto;margin: 0 auto;">
                <span><?=$arResult['INPUT_LABEL'];?></span>
                <input type="text" id="back_email">
                <div class="info_oneclick"><?=$arResult['FOOTER'];?></div>
                </div>
                <?if( $arResult['BTN_CANCEL_YES'] ):?>
                <input type="button" class="<?=$arStyle['btn_class_cancel'];?>" style="cursor:pointer;" id="btn_cancel" value="<?=$arResult['BTN_CANCEL'];?>">
                <?endif;?>
                <input type="button" class="<?=$arStyle['btn_class_send'];?>" style="cursor:pointer;" id="btn_send" value="<?=$arResult['BTN_SEND'];?>">
                </div>
            </div>
        </div>
    </div>
</div>
<?unset($arResult);/*
<?$APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH."/include/modal_getprice.php",
            Array(
                "$arResult" => array("TITLE"           => "",
                                    "FOOTER"           => "",
                                    "INPUT_LABEL"      => "",
                                    "BTN_SEND"         => "",
                                    "BTN_CANCEL"       => "",
                                    "BTN_CANCEL_YES"   => "",
                                    "JS_CLICK_ACTIVE"  => ".price.empty-price",
                                    "AJAX_REQUEST"     => "/ajax/get_price.php",
                                    "form_color"       => "",
                                    "class_send"       => "",
                                    "class_cancel"     => "",
                                    "use_css_animation"=> "",
                                    //"AVAILABILITY"  => "",
                                     ),
                ),
            Array("MODE"=>"php")
);?>
*/?>