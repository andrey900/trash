<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?$this->setFrameMode(true);?>
<?$frame=$this->createFrame()->begin('');?>
<? if (!$USER->IsAuthorized()): ?>
    <?php echo $arResult['ULOGIN_CODE']; ?>
<? else: ?>
    <?=GetMessage("TALKHARD_ULOGIN_PRIVET")?><strong><?=$USER->GetLogin()?></strong>!
    <a href="<?=$APPLICATION->GetCurPageParam("logout=yes", array("logout"))?>"><?=GetMessage("TALKHARD_ULOGIN_VYYTI")?></a>
<?endif; ?>

<?$frame->end();?>