<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10.03.14
 * Time: 15:43
 */


// работа с секцией ХарактеристикиТовара
class CXMLProductFeatures {

    // функция обрабатывает тэг "ХарактеристикиТовара"
    // если данная секция есть, то она возвращет характеристики в виде массива
    function GetProductFeaturesByArray(&$p_node) {
        $ar_values = array();
        if ( $p_node->hasChildNodes() ) {

            foreach($p_node->childNodes as $node) {
                $node_name = CXMLNodeWork::GetNodeByName($node,'Наименование');
                $node_value = CXMLNodeWork::GetNodeByName($node,'Значение');
                if ($node_name) {
                    $ar_values[] = Array($node_value->nodeValue, $node_name->nodeValue);
                }
            }
        }
        return $ar_values;
    } //



    // функция ищет значение в тэге "ХарактеристикиТовара" по ключу
    function FindValueInProductFeatures(&$p_node, $ar_key) {
        $val_return = array();
        if ( $p_node->hasChildNodes() ) {
            foreach($p_node->childNodes as $node) {
                $node_name = CXMLNodeWork::GetNodeByName($node,'Наименование');
                $node_value = CXMLNodeWork::GetNodeByName($node,'Значение');
                if (in_array($node_name->nodeValue,$ar_key) ) {
                    $val_return[$node_name->nodeValue] = $node_value->nodeValue;
                }
            }
        }
        //print_r($val_return);die();
        return $val_return;
    } //



    // конвертирует секцию "ХарактеристикиТовара" в свойство  CML2_ATTRIBUTES
    function ConvertProductFeaturesToProperty(&$doc, &$node_tovar, &$node_old_tovar) {

        if ( $node = CXMLNodeWork::GetNodeByName($node_old_tovar,'ХарактеристикиТовара')  ) {
            $ar = self::GetProductFeaturesByArray($node);

            if ( $nodes = CXMLNodeWork::AddMulPropertyNode($doc,'CML2_ATTRIBUTES',$ar) ) {
                return $nodes;
            }
        }
        return false;
    }

}

// работа с каталогом
class CStructureCopy {

    static $first_level = 0;

    // возвращает UID всех подсекций переданной секции
    function GetGroupsUID($node_group) {

        $ar_result = array();
        foreach ($node_group->childNodes as $node_group_nodes) {
            if($node_group_nodes->nodeName=='Ид') {
                $ar_result[] = $node_group_nodes->nodeValue;
            } elseif($node_group_nodes->nodeName=='Группы') {

                // все подуровни обрабатываем рекурсивно
                if ($node_group_nodes->hasChildNodes()) {
                    foreach ($node_group_nodes->childNodes as $node_sub_group_node) {
                        $ar_result = array_merge( $ar_result, CStructureCopy::GetGroupsUID($node_sub_group_node) );
                    }
                }
            }
        }
        return $ar_result;
    } //

    function IterationNode($newdoc, $node_group) {

        $element_group = $newdoc->createElement('Группа', '');
        foreach ($node_group->childNodes as $node_group_nodes) {

            if($node_group_nodes->nodeName=='Наименование') {
                $element_group->appendChild( $newdoc->createElement('Наименование', $node_group_nodes->nodeValue) );
            } elseif($node_group_nodes->nodeName=='Ид') {
                $element_group->appendChild( $newdoc->createElement('Ид', $node_group_nodes->nodeValue) );
            } elseif($node_group_nodes->nodeName=='Группы') {

                // все подуровни обрабатываем рекурсивно
                if ($node_group_nodes->hasChildNodes()) {
                    $element_groups = $newdoc->createElement('Группы', '');
                    foreach ($node_group_nodes->childNodes as $node_sub_group_node) {
                        //self::$first_level++;
                        $list_nodes = CStructureCopy::IterationNode($newdoc,$node_sub_group_node);
                        $element_groups->appendChild( $list_nodes );
                        //self::$first_level--;

                    }
                    /*
                    if (!self::$first_level) {
                        $element_group = $element_groups;
                    } else {
                        $element_group->appendChild($element_groups);
                    }*/
                    $element_group->appendChild($element_groups);
                }
            }
        }

        return $element_group;

    } // IterationNode
}