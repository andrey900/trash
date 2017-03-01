<?php

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
if (class_exists('studio8_main')) {
    return;
}
class studio8_main extends CModule
{
    public $MODULE_ID = 'studio8.main';
    public $MODULE_VERSION = '0.1';
    public $MODULE_VERSION_DATE = '2015-12-22';
    public $MODULE_NAME = 'Базовый модуль Studio8';
    public $MODULE_DESCRIPTION = 'Служит для автоподключение классов. Использует движок D7.';
    public $MODULE_GROUP_RIGHTS = 'N';
    public $PARTNER_NAME = "Studio8";
    public $PARTNER_URI = "http://studio8.by";


    public function InstallDB()
    {
        global $DB;
        $DB->RunSQLBatch(dirname(__FILE__)."/sql/install.sql");
        return true;
    }
    public function UnInstallDB()
    {
        global $DB;
        $DB->RunSQLBatch(dirname(__FILE__)."/sql/uninstall.sql");
        return true;
    }

    public function DoInstall()
    {
        global $APPLICATION;
        // $this->InstallDB();
        RegisterModule($this->MODULE_ID);
    }
    
    public function DoUninstall()
    {
        global $APPLICATION;
        // $this->UnInstallDB();
        UnRegisterModule($this->MODULE_ID);
    }

}
