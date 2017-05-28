<?php

class GraphicParser{
    /*
     *  ОБРАБОТКА ГРАФИКИ
     */

    private $_relations;
    private $_path;

    public function __construct()
    {
    }

    public function setRelations($relations) {
        $this->_relations = $relations;
    }

    public function setPath($path) {
        $this->_path = $path;
    }

    public function parseDrawing($draw){
        $html = '';
        $namespaces = $draw->getNamespaces(true);
        $wp = $draw->children($namespaces['wp']);
        if ($wp->inline){
            $a = $wp->inline->children($namespaces['a']);
            if ($a->graphic)
                $html .= $this->parseGraphic($a->graphic);
        }
        if ($wp->anchor){
            $a = $wp->anchor->children($namespaces['a']);
            if ($a->graphic)
                $html .= $this->parseGraphic($a->graphic);
        }
        return $html;
    }

    public function parseGraphic($graphic){
        $html = '';
        $namespaces = $graphic->getNamespaces(true);
        $pic = $graphic->graphicData->children($namespaces['pic']);
        $picFill = $pic->pic->blipFill->children($namespaces['a']);
        $relNameAttr = $picFill->blip->attributes('r', true);
        $relName = (String)$relNameAttr['embed'];
        foreach ($this->_relations as $rel => $path){
            if ($rel == $relName) $html .= '<img src="http://' . $_SERVER['HTTP_HOST'] . '/' . $this->_path . '/word/' . $path . '"> ';
        }
        return $html;
    }

}