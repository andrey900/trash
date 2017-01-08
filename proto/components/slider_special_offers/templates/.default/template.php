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
							<?=!empty($Slide["LINK"]) ? "<a href='{$Slide["LINK"]}'>" : "";?>
								<div class="slid-img-in">
									<img class="replace-2x" src="<?=$Slide['DETAIL_PICTURE']['SRC']?>" width="<?=$Slide['DETAIL_PICTURE']['WIDTH']?>"
										<?if(isPictureWorksafe($Slide['DETAIL_PICTURE'])):?>
										worksafe="Y"
										<?endif;?>
										height="<?=$Slide['DETAIL_PICTURE']['HEIGHT']?>" alt="<?=$Slide['NAME']?>">
								</div>
							<?=!empty($Slide["LINK"]) ? "</a>" : "";?>
							</div>
						</div>
					</div> <img class="replace-2x" src="<?=$Slide['PREVIEW_PICTURE']["SRC"]?>"
					 alt="" data-bgfit="auto"
					data-bgposition="center top" data-bgrepeat="repeat"
					class="bg-slid">
				</li>
				<!-- Конец Один слайд -->
			<?endforeach;?>
			</ul>
		</div>
	</div>
</div>

