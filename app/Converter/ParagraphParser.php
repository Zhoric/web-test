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
            if (is_numeric($objectStyle)) $objectStyle = 'id' . $objectStyle;
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
                    if (($att['line'] / 10) >= 34) $attrs[] = 'line-height:' . ($att['line'] / 10) . 'px;';
                    else $attrs[] = 'line-height: 34px;';
                }
                else $attrs[] = 'line-height: initial;';
            }
            if ($p->pPr->jc) {
                $att = $p->pPr->jc->attributes('w', true);
                if ($att['val'] == 'both')
                    $attrs[] = 'text-align: justify;';
                else $attrs[] = 'text-align:' . $att['val'] . ';';
            }
            if ($p->pPr->ind) {
                $att = $p->pPr->ind->attributes('w', true);
                if (isset($att['firstLine'])) $attrs[] = 'text-indent:' . ($att['firstLine'] / 10) . 'px;';

            } else $attrs[] = 'text-indent: 0px;';

        }
        return $attrs;
    }

    public function parseDivProperties($p){
        $attrs = array();
        if ($p->pPr) {

            if ($p->pPr->ind) {
                $att = $p->pPr->ind->attributes('w', true);
                if (isset($att['firstLine'])) $attrs[] = 'text-indent:' . ($att['firstLine'] / 10) . 'px;';
                if (isset($att['left'])) $attrs[] = 'margin-left:' . ($att['left'] / 10) . 'px;';
                if (isset($att['right'])) $attrs[] = 'margin-right:' . ($att['right'] / 10) . 'px;';

            } else $attrs[] = 'text-indent: 0px;';

            if ($p->pPr->shd) {
                $att = $p->pPr->shd->attributes('w', true);
                $attrs[] = 'background-color: #' . $att['fill'];
            }

        }
        return $attrs;
    }
}