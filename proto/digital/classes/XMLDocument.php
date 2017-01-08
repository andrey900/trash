<?php
//d07d62d1-4152-11e3-831d-0019995a0652#8992eda0-4de3-11e3-93e9-00199999144e
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15.03.14
 * Time: 19:49
 */


// предложения offers.xml
class CXMLOffersDocument extends CXMLDocument {


    function  __construct($uid, $name) {
        parent::__construct($uid, $name);
        $this->path_entry = '/КоммерческаяИнформация/ПакетПредложений/Предложения';
        $this->path_group = '/КоммерческаяИнформация/Классификатор/Группы';
        $this->entry_root_for_insert  = $this->GetPositionEntry();
    }

    function LoadXML() {
        $this->doc->loadXML('<КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="18-06-2012T16:02:08"><Классификатор><Ид>'.$this->uid.'</Ид><Наименование>'.$this->name.'</Наименование><Группы></Группы></Классификатор><ПакетПредложений><Ид>'.$this->uid.'</Ид><ИдКлассификатора>'.$this->uid.'</ИдКлассификатора><Наименование>'.$this->name.'</Наименование><Предложения></Предложения><СодержитТолькоИзменения>false</СодержитТолькоИзменения></ПакетПредложений></КоммерческаяИнформация>');
    }

    function GetNewEntry() {
        return $this->doc->createElement('Предложение','');
    }


}

// товары import.xml
class CXMLDocument {

    var $doc;
    var $xpath;
    var $uid;
    var $name;
    var $path_entry;
    var $path_group;
    var $file_path;
    var $entry_root_for_insert;


    function  __construct($uid, $name) {
        $this->uid = $uid;
        $this->name = $name;
        $this->doc = new DOMDocument;
        $this->LoadXML();
        $this->doc->encoding = 'UTF-8';
        $this->doc->formatOutput = true;
        $this->xpath = new DOMXPath($this->doc);
        $this->path_entry = '/КоммерческаяИнформация/Каталог/Товары';
        $this->path_group = '/КоммерческаяИнформация/Классификатор/Группы';

        $this->entry_root_for_insert  = $this->GetPositionEntry();

    }

    function LoadXML() {
        $this->doc->loadXML('<КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="18-06-2012T16:02:08"><Классификатор><Ид>'.$this->uid.'</Ид><Наименование>'.$this->name.'</Наименование><Группы></Группы></Классификатор><Каталог><Ид>'.$this->uid.'</Ид><ИдКлассификатора>'.$this->uid.'</ИдКлассификатора><Наименование>'.$this->name.'</Наименование><Товары></Товары><СодержитТолькоИзменения>false</СодержитТолькоИзменения></Каталог></КоммерческаяИнформация>');
    }

    function GetPositionEntry() {
        $entries_for_insert = $this->xpath->query( $this->path_entry );
        if ($entries_for_insert->length) {
            $entry_root_for_insert = $entries_for_insert->item(0);
        } else {
            $entry_root_for_insert = false;
        }
        return $entry_root_for_insert;

    }


    function GetPositionGroup() {
        $groups_for_insert = $this->xpath->query( $this->path_group );
        if ($groups_for_insert->length) {
            $group_root_for_insert = $groups_for_insert->item(0);
        } else {
            $group_root_for_insert = false;
        }
        return $group_root_for_insert;
    }

    function GetNewEntry() {
        return $this->doc->createElement('Товар','');
    }

    function InsertEntry($node) {
        // фиксируем товар в списке
        $this->entry_root_for_insert->appendChild($node);
    }


    function SaveDoc($file_path) {
        $this->file_path = $file_path;
        $this->doc->save( $this->file_path );
    }

    function LoadToIBLOCK() {
        $_GET["mode"] = "import";
        $_GET["filename"] = $this->file_path;
        RunXMLLoader();
    }

}

// предложения offers.xml для SKU
class CXMLOffersSKUDocument extends CXMLOffersDocument {

    function LoadXML() {
        $this->doc->loadXML('<КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="18-06-2012T16:02:08">
        <Классификатор><Ид>'.$this->uid.'</Ид><Наименование>'.$this->name.'</Наименование>
        </Классификатор>
        <ПакетПредложений>
        <Ид>'.$this->uid.'#</Ид>
        <ИдКаталога>'.$this->uid.'</ИдКаталога>
        <ИдКлассификатора>'.$this->uid.'</ИдКлассификатора>
        <Наименование>'.$this->name.'</Наименование>
        <Предложения></Предложения><СодержитТолькоИзменения>false</СодержитТолькоИзменения>
        </ПакетПредложений></КоммерческаяИнформация>');
    }

}
