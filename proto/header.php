<?php
use Bitrix\Main\Application;
/**
 * @var CMain
 */ 
global $APPLICTION;
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="">
	<?$APPLICATION->ShowHead();?>
	<title><?$APPLICATION->ShowTitle();?></title>
	<?$APPLICATION->ShowProperty("og:tags")?>
	<!-- Latest compiled and minified CSS -->
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bootstrap.min.css');?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bootstrap-theme.min.css');?>
	<?$APPLICATION->SetAdditionalCSS('http://fonts.googleapis.com/css?family=Open+Sans:400,700,600&subset=latin,cyrillic-ext');?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/jquery.bxslider.css')?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/style.css')?>
	<?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/aniart.css')?>
	<!-- Just for debugging purposes. Don't actually copy this line! -->
	<!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	    <![endif]-->
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.js');?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.cookie.js');?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/bootstrap.min.js');?>
	<?$APPLICATION->AddHeadScript('http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js');?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.bxslider.js');?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/main.js');?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/aniart.js');?>
	<?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/aniart.popup.js');?>
	
</head>
<body>
	<div class="bitrix-admin-panel">
		<?$APPLICATION->ShowPanel();?>
	</div>
	
	<?if($_GET["success"] == "Y"):?>
		<script>
			$(function(){
				var successReg = $(".success-reg");
						successReg.fadeIn(2000);
						successReg.fadeOut(5000);
			})
		</script>	
	<?endif;?>
	
	<!-- Fixed navbar -->
	<div class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed"
					data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span> <span class="soc"></span>
				</button>
			</div>
			<div class="logo-head">
			<a href="/">
				<img class="replace-2x" src="<?=SITE_TEMPLATE_PATH?>/images/logo-head.png" width="127"
					height="11" alt=" ">
			</a>
			</div>
			
			<div class="soc-in">
				<?$APPLICATION->IncludeFile("/bitrix/templates/main/include_files/social_inc.php");?>
			</div>
			<!--/.nav-collapse -->
			<div class="tel">
				<?$APPLICATION->IncludeFile("/bitrix/templates/main/include_files/contact_inc.php");?>
			</div>
			<div class="right-m">
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown basc"><a href="/personal/" class="dropdown-toggle"
						data-toggle="dropdown"> <span class="tr"> <span class="r-tr"></span>
								<span class="tr-text"><?php echo (!isset($_SESSION['countBasketElement']))?'0':(int)$_SESSION['countBasketElement'];?></span>
						</span> <span class="basc-img"></span>
					</a>
					<ul class="dropdown-menu">
					<?php $APPLICATION->IncludeComponent(
						"bitrix:sale.basket.basket",
						"small",
						Array(
						"PATH_TO_ORDER" => '/order/',
						)
					);?>
					</ul>
<?php /*					<ul class="dropdown-menu">
							<!-- Один товар в корзине -->
							<li>
								<div class="tov">
									<div class="number">
										<span class="plu"><b></b></span>
										<div class="inp">
											<input type="text" class="in-num" value="1" size="5" /> <span>шт</span>
										</div>
										<span class="minus"><b></b></span>
									</div>
									<div class="basc-thumb">
										<div class="basc-thumb-in">
											<img class="replace-2x" src="<?=SITE_TEMPLATE_PATH?>/images/basc-img-1.jpg"
												width="30" height="75" alt=" ">
										</div>
									</div>
									<div class="tov-tit">
										<div class="basc-price">1 030.–</div>
										<span> <a>Фаллоимитатор Real Feel №4</a>
										</span>
									</div>
									<a class="close"></a>
								</div>
								<div class="div"></div>
							</li>
							<!-- Конец Один товар в корзине -->
							<!-- Один товар в корзине -->
							<li>
								<div class="tov">
									<div class="number">
										<span class="plu"><b></b></span>
										<div class="inp">
											<input type="text" class="in-num" value="1" size="5" /> <span>шт</span>
										</div>
										<span class="minus"><b></b></span>
									</div>
									<div class="basc-thumb">
										<div class="basc-thumb-in">
											<img class="replace-2x" src="<?=SITE_TEMPLATE_PATH?>/images/basc-img-2.jpg"
												width="57" height="71" alt=" ">
										</div>
									</div>
									<div class="tov-tit">
										<div class="basc-price">2 040.–</div>
										<span> <a>Надувной шар с фаллоимитатором</a>
										</span>
									</div>
									<a class="close"></a>
								</div>
								<div class="div"></div>
							</li>
							<!-- Конец Один товар в корзине -->
							<div class="bt-basc">
								<input type="button" class="send" value="Оплатить 2 046 рублей">
							</div>
						</ul>*/?></li>
						<?php //p($_SERVER, false, true);?>
					<li <?if($USER->IsAuthorized()):?>class='dropdown my-photo'<?endif;?>>
	                <?php if ($USER->IsAuthorized()): ?>
	                	<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<div class="avat">
								<?php $userInfo = $USER->GetByID($USER->GetID());?>
								<img width="40" height="40" alt=" " src="<?php echo !empty($userInfo->arResult[0]["PERSONAL_PHOTO"]) ? CFile::GetPath($userInfo->arResult[0]["PERSONAL_PHOTO"]) : "/bitrix/templates/main/images/default_user.png";?>">
							</div>
						</a>
	                	<ul class="dropdown-menu">
							<a href="/personal/" class="link-lk">
								<span>Личный кабинет</span>
							</a>
							 <a href="<?php// echo $APPLICATION->GetCurUri(); ?>?logout=yes" id="bx-panel-logout" class="ex-lk">
	                    		<span><?php echo GetMessage("AUT_TEMPLATE_LOGOUT"); ?></span>
		               		 </a>
						</ul>
	                   
	                    <?php else: ?>
	                    	<a href="#" data-toggle="modal" data-target="#myModal" id="panel-login">
	                        	<span><?php echo GetMessage("AUT_TEMPLATE_LOGIN"); ?></span>
	                       	</a>
	                    <?php endif; ?>
                   </li>
					<li class="work">
						<div class="onoffswitch">
							<label class="swich" for="myonoffswitch">Worksafe</label> <input
								type="checkbox" name="onoffswitch" class="onoffswitch-checkbox"
								id="myonoffswitch"> <label class="onoffswitch-label"
								for="myonoffswitch"> <span class="onoffswitch-inner"></span> <span
								class="onoffswitch-switch"></span>
							</label>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>


<!-- Модальное окно авторизация -->
<div id="myModal" class="modal fade mod mod-1" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="all-im-mod">
                <div class="modal-header">
                    <div class="mod-clos">
                        <a href="#" data-dismiss="modal" class="ex ex-f"></a>
                    </div>
                    <div class="modal-title" id="myModalLabel">
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/logo.png" width="206" height="17" alt=" ">
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mod-left">
                        <div class="log-face in-mod">
                            <?php
                                $APPLICATION->IncludeComponent(
                                    "bitrix:system.auth.form",
                                    "main",
                                    Array(
                                        "REGISTER_URL" => "",
                                        "FORGOT_PASSWORD_URL" => "",
                                        "PROFILE_URL" => "",
                                        "SHOW_ERRORS" => "Y"
                                    ),
                                false
                                );
                            ?>
                        </div>
                        <div class="in-log log-mod">
                             <button type="submit" data-toggle="modal" data-target="#myModal1" data-dismiss="modal" class="btn">Вход через Email</button>
                             </form>
                        </div>
                    </div>
                    <div class="mod-right">
                        <div class="in-g in-max-screen entry-quest">
                            <a href="#"  data-dismiss="modal">Войти как гость</a>
                        </div>
                        <div class="in-d">
                            Вы не сможете читать истории и не получите доступ к эксклюзивному контенту
                        </div>
                        <div class="in-g in-min-screen">
                            <a href="#">Войти как гость</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Конец модальное окно авторизация -->

<!-- Модальное окно авторизация через email -->

<div class="modal fade mod mod-1" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="all-im-mod">
                <div class="modal-header">
                    <div class="mod-clos">
                        <a href="#" data-dismiss="modal" class="ex"></a>
                    </div>
                    <div class="modal-title" id="myModalLabel">
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/logo.png" width="206" height="17" alt=" ">
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mod-left">
                        <div class="log-face in-mod">
                            <?php
                                $APPLICATION->IncludeComponent(
                                    "bitrix:system.auth.form",
                                    "main",
                                    Array(
                                        "REGISTER_URL" => "",
                                        "FORGOT_PASSWORD_URL" => "",
                                        "PROFILE_URL" => "",
                                        "SHOW_ERRORS" => "Y"
                                    ),
                                false
                                );
                            ?>
                        </div>
                        <form id="login_in" method="POST" action="javascript:void(null);" role="form">
                            <div class="in-log mod-inp">
                                <div>
                                  <input name="user_login" class="one-in" type="text" id="exampleInputlog" placeholder="Ваш логин">
                                </div>
                                <div class="input-group">
                                    <input name="user_pass" type="password" class="form-control one-in" placeholder="Пароль">
                                    <span class="input-group-addon">
                                        <a href="#" data-toggle="modal" data-target="#myModal3" data-dismiss="modal">забыл</a>
                                    </span>
                                </div>
                            </div>
                            <div class="in-log log-mod">
                                <button name="login" type="submit" class="btn">Вход</button>
                            </div>
                        </form>
                    </div>
                    <div class="mod-right">
                        <div class="in-d aut">
                            Авторизация дает вам возможность более быстро просматривать товары, а также дает возможность их купить
                        </div>
                        <div class="in-log log-mod">
                            <button type="submit" data-toggle="modal" data-target="#myModal2" data-dismiss="modal" class="btn">Регистрация</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Конец модальное окно авторизация через email -->
    
<!-- Модальное окно регистрации нового пользователя -->
<div class="modal fade mod mod-1" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="all-im-mod">
                <div class="modal-header">
                    <div class="mod-clos">
                        <a href="#" data-dismiss="modal" class="ex"></a>
                    </div>
                    <div class="modal-title" id="myModalLabel">
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/logo.png" width="206" height="17" alt=" ">
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mod-left">
                        <div class="log-face in-mod">
                            <?php
                                $APPLICATION->IncludeComponent(
                                    "bitrix:system.auth.form",
                                    "main",
                                    Array(
                                        "REGISTER_URL" => "",
                                        "FORGOT_PASSWORD_URL" => "",
                                        "PROFILE_URL" => "",
                                        "SHOW_ERRORS" => "Y"
                                    ),
                                false
                                );
                            ?>
                        </div>
                        <form id="registration_new" method="POST" action="javascript:void(null);" role="form">
                        <div class="in-log mod-inp">
                            <div>
                                <input class="one-in" name="reg_login" type="text" id="exampleInputlog" placeholder="Ваш логин">
                            </div>
                            <div>
                                <input class="one-in" name="reg_email" type="email" id="exampleInputlog" placeholder="Email">
                            </div>
                            <div>
                                <input class="one-in" name="reg_password" type="password" id="exampleInputlog" placeholder="Пароль">
                            </div>
                        </div>
                        <div class="in-log log-mod">
                             <button type="submit" class="btn" id="reg">
                                 <?php echo GetMessage("REG_TEMPLATE_REG_BUTTON"); ?>
                             </button>
                        </div>
                        </form>
                    </div>
                    <div class="mod-right">
                        <div class="in-d aut aut-2">
                            Авторизация дает вам возможность более быстро просматривать товары, а также дает возможность их купить
                        </div>
                        <div class="in-log log-mod">
                            <button type="submit" data-toggle="modal" data-target="#myModal1" data-dismiss="modal" class="btn" >
                                <?php echo GetMessage("REG_TEMPLATE_REG_ON"); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="success-reg">
	<p>
		Регистрация подтверджена!
	</p>
</div>
<!-- Конец модальное окно регистрации нового пользователя -->
    
    <!-- Modal one -->
    <div class="modal fade mod mod-1" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
        	<div class="all-im-mod">
              <div class="modal-header">
              	<div class="mod-clos">
                    <a href="#" data-dismiss="modal" class="ex"></a>
                  </div>
                <div class="modal-title" id="myModalLabel">
                    <img src="<?=SITE_TEMPLATE_PATH?>/images/logo.png" width="206" height="17" alt=" ">
                </div>
              </div>
              <div class="modal-body">
                 <div class="mod-left">
                    <div class="log-face in-mod">
                        <?php
                            $APPLICATION->IncludeComponent(
                                "bitrix:system.auth.form",
                                "main",
                                Array(
                                    "REGISTER_URL" => "",
                                    "FORGOT_PASSWORD_URL" => "",
                                    "PROFILE_URL" => "",
                                    "SHOW_ERRORS" => "Y"
                                ),
                            false
                            );
                        ?>
                    </div>
                    
                    <div class="in-log mod-inp">
                        	<form role="form" action="/">
                              <div>
                                <input class="one-in" type="email" id="email-repair-pass" placeholder="Почта для возобновления пароля" required>
                              </div>
                            </form>
                        </div>
                    
                    <div class="in-log log-mod">
                         <button type="submit" class="btn" id="repair-pass" data-target="#myModal4">Восстановить</button>
                         </form>
                    </div>
                 </div>
                 <div class="mod-right">
                    <div class="in-d aut aut-3">
                        Авторизация дает вам возможность более быстро просматривать товары, а также дает возможность их купить
                    </div>
                    <div class="in-log log-mod">
                         <button type="submit" data-toggle="modal" data-target="#myModal2" data-dismiss="modal" class="btn">Регистрация</button>
                         </form>
                    </div>
                 </div>
              </div>
          </div>
         
        </div>
      </div>
    </div>
    <!-- The end Modal one -->
    
    <!-- Modal Forgot Pass -->
        <div class="modal fade mod mod-1" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
        	<div class="all-im-mod">
              <div class="modal-header">
              	<div class="mod-clos">
                    <a href="#" data-dismiss="modal" class="ex"></a>
                  </div>
                <div class="modal-title" id="myModalLabel">
                    <img src="<?=SITE_TEMPLATE_PATH?>/images/logo.png" width="206" height="17" alt=" ">
                </div>
              </div>
              <div class="modal-body">
                 <div class="mod-left">
                    <div class="in-log mod-inp">
                        <form role="form" action="/" id="form-repair-pass">
                        			<input class="one-in" name="email" type="email" placeholder="Email">
                            	<input class="one-in" name= "repairCode" type="text" placeholder="Код восстановления">
                            	<input class="one-in" type="password" name="pass" placeholder="Пaроль">
                            	<input class="one-in" type="password" name="repeatPass" placeholder="Подтверждение пароля">
                        </form>
                    </div>
                    
                    <div class="in-log log-mod">
                         <button type="submit" class="btn" id="update-pass">Восстановить пароль</button>
                         </form>
                    </div>
                 </div>
                 <div class="mod-right">
                    <div class="in-d aut aut-3">
                        Здесь будут отображатся ошибки 
                    </div>
                 </div>
              </div>
          </div>
        </div>
      </div>
    </div>
    
    
    
    <!-- The end Modal one -->
    </div>
        
        
	<!-- Меню над слайдером -->
	<div class="container">
		<div class="top-cont <?if(!changeColorByPath()):?>bl<?endif;?>">
			<div class="logo">
				<a href ="/">
					<img class="replace-2x" src="<?=SITE_TEMPLATE_PATH?>/images/logo<?if(!changeColorByPath()):?>-black<?endif;?>.png" width="206"
						height="17" alt=" ">
				</a>
			</div>
			<div class="shop-menu">
				<?$APPLICATION->IncludeComponent("bitrix:menu","topMenu",Array(
						"ROOT_MENU_TYPE" => "topMenu", 
						"MAX_LEVEL" => "2", 
						"CHILD_MENU_TYPE" => "top", 
						"USE_EXT" => "Y",
						"DELAY" => "N",
						"ALLOW_MULTI_SELECT" => "Y",
						"MENU_CACHE_TYPE" => "N", 
						"MENU_CACHE_TIME" => "3600", 
						"MENU_CACHE_USE_GROUPS" => "Y", 
						"MENU_CACHE_GET_VARS" => "" 
					)
				);?>
			</div>
		</div>
	</div>
	<!-- Конец Меню над слайдером -->