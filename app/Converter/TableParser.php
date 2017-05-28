<?php

class TableParser {

    /*
    * ОБРАБОТКА ТАБЛИЦ
    */

    private $_vMerge;
    private $_paragraphParser;
    private $_textParser;

    public function __construct(TextParser $textParser, ParagraphParser $paragraphParser)
    {
        $this->_textParser = $textParser;
        $this->_paragraphParser = $paragraphParser;
    }

    public function parseTable($table)
    {
        $html = '';
        if (isset($table->tblPr)) {
            $this->_vMerge = 1;
            $tableId = $this->getTableId($table->tblPr);
            $properties = $this->parseTableProperties($table->tblPr);

            if ($tableId)
                $html .= '<table class="' . $tableId . '"';
            else $html .= '<table ';

            if ($properties) {
                $html .= ' style = "';
                foreach ($properties as $prop)
                    $html .= $prop;
                $html .= '"';
            }
            $html .= '>';

            foreach ($table->tr as $tr)
                $html .= $this->parseTableRow($tr);
            $html = $this->checkRowColSpan($html);
            $html .= '</table>';
        }
        return $html;
    }

    public function getTableId($tblPr)
    {
        if ($tblPr->tblStyle) {
            $att = $tblPr->tblStyle->attributes('w', true);
            $styleId = (String)$att['val'];
            return $styleId;
        }
        return null;
    }

    public function parseTableProperties($tblPr)
    {
        $attrs = array();
        if ($tblPr->tblInd) {
            $att = $tblPr->tblInd->attributes('w', true);
            $attrs[] = 'margin-left: ' . ($att['w'] / 10) . 'px; ';
        }
        $attrs[] = 'border-collapse: collapse; ';
        if (empty($attrs)) return null;
        return $attrs;
    }

    public function parseTableRow($tr)
    {
        $html = '<tr>';
        foreach ($tr->tc as $tc) {
            if ($tc->tcPr->vMerge['val'] == 'continue') {
                $this->_vMerge++;
            } else {
                $cellStyle = $this->parseCellStyle($tc->tcPr);
                $cellAttribute = $this->parseCellAttribute($tc->tcPr);
                $html .= '<td ' . $cellAttribute . $cellStyle . ' >';
                foreach ($tc->p as $p) {
                    $className = $this->_paragraphParser->parseParagraphStyle($p);
                    $attrs = $this->_paragraphParser->parseProperties($p);
                    $html .= $this->_textParser->parseText($p->r, $className, $attrs);
                }
                $html .= '</td>';
            }
        }
        $html .= '</tr>';
        return $html;
    }

    public function checkRowColSpan($cellStyle)
    {
        $index = strrpos($cellStyle, 'rowspan=;');
        $style = $cellStyle;
        if ($index) {
            if ($cellStyle[$index + 8] == ';') {
                $firstHalf = substr($cellStyle, 0, $index + 7);
                $secondHalf = substr($cellStyle, $index + 8);
                $firstHalf .= '=' . $this->_vMerge;
                $style = $firstHalf . $secondHalf;
            }
        }
        return $style;

    }

    public function parseCellAttribute($cell)
    {
        $attr = "";
        foreach (get_object_vars($cell) as $att => $val) {
            switch ($att) {
                case "vMerge":
                    if ($val['val'] == "restart") {
                        $this->_vMerge = 1;
                        $attr .= 'rowspan=;';
                    } else if ($val['val'] == "continue") {
                        $this->_vMerge++;
                    }
                    break;
                case "gridSpan":
                    $attr .= ' colspan=' . $val['val'];
                    break;
            }
        }
        return $attr;
    }

    public function parseCellStyle($cellStyle)
    {
        $style = " style='";
        foreach (get_object_vars($cellStyle) as $att => $val) {
            switch ($att) {
                case "tcW":
                    $style .= 'width:' . ($val['w'] / 10) . 'px;';
                    break;
                case "vAlign":
                    if ($val['val'] == 'center') $cellStyle .= 'vertical-align: middle;';
                    else $style .= 'vertical-align:' . $val['val'];
                    break;
                case "tcBorders":
                    $attrs = $this->parseTableBorders($cellStyle);
                    if (!empty($attrs)) {
                        foreach ($attrs as $borderAtt)
                            $style .= $borderAtt . ';';
                    }
                    break;
            }
        }
        $style .= "'";
        return $style;
    }

    public function parseTableBorders($tblPr)
    {
        $isCell = false;
        $attrs = array();
        $collapse = false;

        if ($tblPr->tblBorders) {
            $bordersStyle = $tblPr->tblBorders;
        } else if ($tblPr->tcBorders) {
            $bordersStyle = $tblPr->tcBorders;
            $isCell = true;
        } else return null;

        foreach (get_object_vars($bordersStyle) as $tag => $style) {
            $att = $style->attributes('w', true);
            if ($att['space'] == 0) $collapse = true;
            if ($att['color'] == 'auto') $color = 'black';
            else $color = '#' . $att['color'];

            switch ($tag) {
                case "top":
                    $attrs[] = ' border-top: ' . ($att['sz'] / 10) . 'px ' . $this->getBorderStyle($att['val']) . ' ' . $color;
                    break;
                case "bottom":
                    $attrs[] = ' border-bottom: ' . ($att['sz'] / 10) . 'px ' . $this->getBorderStyle($att['val']) . ' ' . $color;
                    break;
                case "right":
                    $attrs[] = ' border-right: ' . ($att['sz'] / 10) . 'px ' . $this->getBorderStyle($att['val']) . ' ' . $color;
                    break;
                case "left":
                    $attrs[] = ' border-left: ' . ($att['sz'] / 10) . 'px ' . $this->getBorderStyle($att['val']) . ' ' . $color;
                    break;
                case "insideH":
                    if (!$isCell)
                        $attrs[] = ' td {border-top: ' . ($att['sz'] / 10) . 'px ' . $this->getBorderStyle($att['val']) . ' ' . $color . ' ;' .
                            'border-bottom: ' . ($att['sz'] / 10) . 'px ' . $this->getBorderStyle($att['val']) . ' ' . $color . ' } ';
                    break;
                case "insideV":
                    if (!$isCell)
                        $attrs[] = ' td {border-right: ' . ($att['sz'] / 10) . 'px ' . $this->getBorderStyle($att['val']) . ' ' . $color . ' ;' .
                            'border-left: ' . ($att['sz'] / 10) . 'px ' . $this->getBorderStyle($att['val']) . ' ' . $color . ' } ';
                    break;
            }
        }
        if (!$isCell)
            if ($collapse) $attrs[] = 'table {border-collapse: collapse} ';
            else $attrs[] = 'table {border-collapse: separate} ';
        return $attrs;

    }

    public function getBorderStyle($style)
    {
        switch ($style) {
            case "single":
                return 'solid';
                break;
            case "nil":
                return 'none';
                break;
            case "none":
                return 'none';
                break;
            case "dotted":
                return 'dotted';
                break;
            case "dashed":
                return 'dashed';
                break;
            case "double":
                return 'double';
                break;
            case "outset":
                return 'outset';
                break;
            case "inset":
                return 'inset';
                break;
            default:
                return 'solid';
                break;
        }
    }

    public function parseTableCellMar($tblPr){
        if ($tblPr->tblCellMar) {
            $attrs = array();
            foreach (get_object_vars($tblPr->tblCellMar) as $tag => $style) {
                $att = $style->attributes('w', true);
                switch ($tag) {
                    case "top":
                        $attrs[] = 'td {padding-top: ' . ($att['w'] / 10) . 'px} ';
                        break;
                    case "bottom":
                        $attrs[] = 'td {padding-bottom: ' . ($att['w'] / 10) . 'px} ';
                        break;
                    case "right":
                        $attrs[] = 'td {padding-right: ' . ($att['w'] / 10) . 'px} ';
                        break;
                    case "left":
                        $attrs[] = 'td {padding-left: ' . ($att['w'] / 10) . 'px} ';
                        break;
                }
            }
            return $attrs;
        }
        return null;
    }
}