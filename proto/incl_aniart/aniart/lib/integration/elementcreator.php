<?php

namespace Studio8\Main\Integration;


class ElementCreator
{
    public $quantity = 10;
    public $price = 0;
    public $currency = "BYN";
    public $arPictures = [];

    protected $element;
    protected $morePhotoPropId = 61;
    protected $arFields = [
        "IBLOCK_ID" => 14,
        "ACTIVE"    => "Y",
    ];
    protected $id;

    protected $arProps = [];

    private $useHiLoad = false;

    /**
     * If set @enity_name, use HiLoad block, else use IBlockElement
     * @param bool $entity_name
     */
    public function __construct($entity_name=false)
    {
        if( !$entity_name ){
            $this->element = new \CIBlockElement;
        } else {
            $this->element = $this->makeHiloadObj($entity_name);
            $this->arFields = [];
            $this->useHiLoad = true;
        }
    }

    public function getId(){
        return $this->id;
    }

    /**
     * Set field and value
     * @param $fieldsName
     * @param $value
     */
    public function setField($fieldsName, $value)
    {
        $this->arFields[$fieldsName] = $value;
        return $this;
    }

    /**
     * Set property and value
     * @param $propName
     * @param $value
     */
    public function setProp($propName, $value)
    {
        $this->arProps[$propName] = $value;
        return $this;
    }

    /**
     * Save data in ...
     * @param bool $is_catalog
     * @throws \Exception
     */
    public function save($is_catalog = false)
    {
        if( $this->id ){
            throw new \Exception("Element is created ".$this->id);
        }

        if( $this->useHiLoad )
            $this->addHlElement();
        else
            $this->addIblockElement($is_catalog);
    }

   //немножко говнокода,  фильтруем цвета в Hlelement по XML_ID
    protected function addHlElement()
    {
        $clName = $this->element;
        $filtXmlId = $clName::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "ASC"),
            "filter" => array('UF_XML_ID' => $this->arFields["UF_XML_ID"])
        ));
        if (!$HlElData = $filtXmlId->Fetch()) {
            $result = $clName::add($this->arFields);
            if (!$result->isSuccess()) {
                echo implode(', ', $result->getErrorMessages()); //выведем ошибки
            }
            $this->id = $result->getId();
        } else {
            $this->id = $HlElData["ID"];
        }
    }

    //Функция добавления  Hl элемента
    /*protected function addHlElement(){
        $clName = $this->element;
        $result = $clName::add($this->arFields);
          if(!$result->isSuccess()){
             echo implode(', ', $result->getErrorMessages()); //выведем ошибки
          }
          $this->id = $result->getId();
    }*/

    protected function makeHiloadObj($ename){
        $rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('NAME'=>$ename)));

        if ( !($arData = $rsData->fetch()) ){
            throw new \Exception('Инфоблок не найден');
        }

        $Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
        //Создадим объект DataClass
        return $Entity->getDataClass();
    }

    protected function addIblockElement($is_catalog){
        $arFields = $this->arFields;
        $arFields['PROPERTY_VALUES'] = $this->arProps;
        if($this->id = $this->element->Add($arFields)){
            $this->addMorePhoto();
            if( $is_catalog ){
                \CCatalogProduct::add(array('ID' => $this->id, 'QUANTITY' => $this->quantity));
                $this->setPrice();
            }
        } else {
            echo "Error for \"".$this->arFields['NAME']."\": ".$this->element->LAST_ERROR."<br>";
        }
    }

    protected function setPrice(){
        $this->price = floatval($this->price);

        if( $this->price <= 0 )
            return;

        $arFields = Array(
            "PRODUCT_ID" => $this->id,
            "CATALOG_GROUP_ID" => 1,
            "PRICE" => $this->price,
            "CURRENCY" => $this->currency
        );
        \CPrice::Add($arFields);
    }

    protected function addMorePhoto(){
        if( count($this->arPictures) < 1 )
            return;

        $arFils = array(); //сюда перечисляете ваши картинки

        //добавление очередной картинки:
        $i = 0;
        foreach($this->arPictures as $pic){

            if( ++$i == 1 ) continue;

            $arTmpFile = \CFile::MakeFileArray((string)trim($pic));

            if( !$arTmpFile ) continue;

            $arFils[]=array(
                'VALUE'=>$arTmpFile,
                'DESCRIPTION'=>"",
            );
        }
        //когда массив заполнен всеми картинками делаете так
        \CIBlockElement::SetPropertyValues($this->id, $this->arFields['IBLOCK_ID'], $arFils, $this->morePhotoPropId);
    }
}