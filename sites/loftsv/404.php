<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
CHTTP::SetStatus('404 Not Found');
$APPLICATION->SetTitle("1С-Битрикс: Управление сайтом");
?>
<!-- Start page content -->
<div id="page-content" class="page-wrapper">

<!-- ERROR SECTION START -->
<div class="error-section mb-80">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="error-404 box-shadow">
                    <img src="https://img.epizod.ua/uploads/2016/10/404new-page_1.jpg" alt="">
                    <div class="go-to-btn btn-hover-2">
                        <a href="/">go to home page</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ERROR SECTION END -->
        
</div>
<!-- End page content -->
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>