<?php

class ParagraphParser {
    /*
    * ОБРАБОТКА ПАРАГРАФОВ
    */

    private $_styles;

    public function __construct()
    {
    }

    public function setStyles($styles){
        $this->_styles = $styles;
    }

    public function parseParagraphStyle($p){
        $className = null;
        if ($p->pPr->pStyle) {
            $objectAttrs = $p->pPr->pStyle->attributes('w', true);
            $objectStyle = (String)$objectAttrs['val'];
            if (isset($this->_styles[$objectStyle])) {
                $className = $objectStyle;
            }
        }
        return $className;
    }

    public function parseProperties($p){
        $attrs = array();
        if ($p->pPr) {
            if ($p->pPr->spacing) {
                $att = $p->pPr->spacing->attributes('w', true);
                if (isset($att['before'])) {
                    $attrs[] = 'padding-top:' . ($att['before'] / 10) . 'px;';
                }
                if (isset($att['after'])) {
                    $attrs[] = 'padding-bottom:' . ($att['after'] / 10) . 'px;';
                }
                if (isset($att['line'])) {
                    $attrs[] = 'line-height:' . ($att['line'] / 10) . 'px;';
                }
                else $attrs[] = 'line-height: initial;';

                //$attrs[] = 'display: inline-block;';
            }
            if ($p->pPr->jc) {
                $att = $p->pPr->jc->attributes('w', true);
                if ($att['val'] == 'both')
                    $attrs[] = 'text-align: justify;';
                else $attrs[] = 'text-align:' . $att['val'] . ';';
            }
            else $attrs[] = 'text-align: inherit;';
            if ($p->pPr->ind) {
                $att = $p->pPr->ind->attributes('w', true);
                $attrs[] = 'text-indent:' . ($att['firstLine'] / 10) . 'px;';
            } else $attrs[] = 'text-indent: 0px;';

            //$this->html .= '<div class="block" style="' . $this->style . '">';
            //$attrs[] = 'display: inline-block;';
        }
        return $attrs;
    }
}