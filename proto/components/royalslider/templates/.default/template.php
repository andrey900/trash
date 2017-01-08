<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="slider rs-slider load">
	<div class="tp-banner-container">
		<div class="tp-banner">
			<ul>
			<?foreach($arResult['ELEMENTS'] as $Slide):?>
				<!-- Один слайд -->
				<li data-delay="8000" data-transition="fade" data-slotamount="2"
					data-masterspeed="2000">
					<div class="elements">
						<div class="tp-caption lfr skewtoleft img-slid" data-x="0"
							data-y="bottom" data-speed="1500" data-start="1000"
							data-easing="Power4.easeOut" data-endspeed="1000"
							data-endeasing="Power1.easeIn" style="z-index: 1">
							<div class="slid-img">
								<div class="slid-img-in">
									<img class="replace-2x" src="<?=$Slide['PICTURE']['SRC']?>" width="<?=$Slide['PICTURE']['WIDTH']?>"
										<?if(isPictureWorksafe($Slide['PICTURE'])):?>
										worksafe="Y"
										<?endif;?>
										height="<?=$Slide['PICTURE']['HEIGHT']?>" alt="<?=$Slide['NAME']?>">
								</div>
							</div>
						</div>
						<div class="tp-caption lfr skewtoleft slid-inform" data-x="755"
							data-y="122" data-speed="1500" data-start="1200"
							data-easing="Power4.easeOut" data-endspeed="1200"
							data-endeasing="Power1.easeIn">
							<div class="slid-about">
								<a href="javascript:void(0)" class="tag"><?=$Slide['ENTITY_NAME']?></a>
								<div class="post-tit"><?=$Slide['NAME']?></div>
								<div class="post-text"><?=$Slide['TEXT']?></div>
								<div class="det-slid">
									<a href="<?=$Slide['DETAIL_PAGE_URL']?>">подробнее</a>
								</div>
							</div>
						</div>
					</div> <img class="replace-2x" src=""
					style="background-color: <?=$Slide['BACKGROUND_COLOR']?>" alt="" data-bgfit="cover"
					data-bgposition="center top" data-bgrepeat="no-repeat"
					class="bg-slid">
				</li>
				<!-- Конец Один слайд -->
			<?endforeach;?>
			</ul>
		</div>
	</div>
</div>