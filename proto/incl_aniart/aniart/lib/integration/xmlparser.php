<?php

namespace Studio8\Main\Integration;


class XmlParser
{
    private $path = "/upload/exports_offers/";
    protected $xmlDoc;
    protected $strategy;

    public function __construct($fileName)
    {
        $link = $_SERVER['DOCUMENT_ROOT'].$this->path.$fileName;
        if( !file_exists($link) )
            throw new \Exception("File not found: ".$link);
        $this->xmlDoc = new \SimpleXMLElement(file_get_contents($link));
    }

    public function setStrategy(callable $func)
    {
        $this->strategy = $func;
    }

    public function getProp(\SimpleXMLElement $xml, $propName)
    {
        $f = $this->strategy;
        $result = $f($propName, $xml);
        if( is_array($result) )
            return $result;
        
        return $this->toStr($result);
    }

    protected function toStr($str)
    {
        return trim((string)$str);
    }

    public function getXml()
    {
        return $this->xmlDoc;
    }
}