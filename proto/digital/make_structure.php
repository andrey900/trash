#!/usr/bin/php
<?
define('ANIART_DEBUG', true);
/**
 * Created by PhpStorm.
 * User: root
 * Date: 09.04.14
 * Time: 18:05
 */

@set_time_limit(0);
ini_set("memory_limit", "512M");

require(dirname(__FILE__).'/config.php');
$str_file_load_materials = $_SERVER["DOCUMENT_ROOT"].'/work-scripts/structure.xml';

// Документ - источник
$doc = new DOMDocument;
$doc->load($str_file_load_materials);
$xpath = new DOMXPath($doc);
$nodes_level_1 = $xpath->query("/Группа");
if ($nodes_level_1->length) {
    $node_level_1 = $nodes_level_1->item(0);
}

function IterationNode($node_group) {

    if (!$node_group->hasChildNodes() ) return '';
    $str = '';
    $b_has_child = false;
    foreach ($node_group->childNodes as $node_group_nodes) {


        if($node_group_nodes->nodeName=='Наименование') {
            $name = $node_group_nodes->nodeValue;
        } elseif($node_group_nodes->nodeName=='Ид') {
            $id = $node_group_nodes->nodeValue;
        } elseif($node_group_nodes->nodeName=='Группы') {
            $b_has_child = true;
            // все подуровни обрабатываем рекурсивно
            if ($node_group_nodes->hasChildNodes()) {
                foreach ($node_group_nodes->childNodes as $node_sub_group_node) {
                    $str.= IterationNode($node_sub_group_node);
                }
            }
        }
    }
    if (!$b_has_child) {
        $str.= sprintf("\n
            '%1s'=>Array(
                '%2s',
                'BRAND'=>array('Бренд','','','brands'),
                'MINIMUM_PRICE'=>array('Цена','','','price'),
                //'CATALOG_1'=>array('','%1s-1',array('')),
                //'CATALOG_2'=>array('','%1s-2',array('')),
                //'CATALOG_3'=>array('','%1s-3',array('')),
            ),\n",
            $id,
            $name,
            $id,
            $id,
            $id
        );
    }
    return $str;
} // IterationNode


echo IterationNode($node_level_1);
