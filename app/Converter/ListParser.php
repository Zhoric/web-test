<?php

class ListParser {
    /*
    * ОБРАБОТКА СПИСКОВ
    */

    private $_textParser;
    private $_paragraphParser;

    private $_numbering;
    private $_styleList;
    private $_counts;
    private $_maxCounts;

    private $_isList;
    private $_isUlEnd;

    public function __construct(TextParser $textParser, ParagraphParser $paragraphParser)
    {
        $this->_textParser = $textParser;
        $this->_paragraphParser = $paragraphParser;
    }

    public function setStyleList($styleList){
        $this->_styleList = $styleList;
    }

    public function setNumbering($numbering){
        $this->_numbering = $numbering;
    }

    public function setIsList($isList){
        $this->_isList = $isList;
    }
    public function getIsList(){
        return $this->_isList;
    }
    public function getIsUlEnd(){
        return $this->_isUlEnd;
    }

    public function parseList($p, $currentHtml){
        $html = '';
        $this->_isUlEnd = false;
        if ($p->pPr->pStyle){
            $styleId = (String)$p->pPr->pStyle['val'];
            if (is_numeric($styleId)) $styleId = 'id' . $styleId;
            foreach ($this->_styleList as $style => $val){
                if ($style == $styleId) {
                    $numId = $val['numId'];
                    if (isset($val['ilvl'])) $lvlId = $val['ilvl'];
                    else $lvlId = 0;
                    if ($this->_counts[$numId][$lvlId] > 0)
                        $this->_counts[$numId][$lvlId]--;
                    else {
                        $this->_isUlEnd = true;
                        $this->_counts[$numId][$lvlId]--;
                    }
                    $html = $this->getListHtml($p, $currentHtml, $numId, $lvlId, $this->_counts[$numId][$lvlId]);
                }
            }
        }
        if ($p->pPr->numPr) {
            $numId = (int)$p->pPr->numPr->numId['val'];
            $lvlId = (int)$p->pPr->numPr->ilvl['val'];
            if ($this->_counts[$numId][$lvlId] > 0)
                $this->_counts[$numId][$lvlId]--;
            else {
                $this->_isUlEnd = true;
                $this->_counts[$numId][$lvlId]--;
            }
            $html = $this->getListHtml($p, $currentHtml, $numId, $lvlId, $this->_counts[$numId][$lvlId]);
        }

        return $html;
    }

    private function getListHtml($p, $currentHtml, $numId, $lvlId = null, $value){
        $html = '';
        $this->_isList = true;
        $isDecimalList = false;

        $attrs = array_merge($this->_textParser->parseTextStyle($p->pPr), $this->_textParser->parseTextStyle($p->r));
        $className = $this->_paragraphParser->parseParagraphStyle($p);

        if ($numId == 0) {
            if ($className) $html .= '<ul> <li class="' . $className .'"style="list-style-position: inside; list-style-type: none;' . implode(';', $attrs) .'"></li></ul>';
            else $html .= '<ul> <li style="list-style-position: inside; list-style-type: none;' . implode(';', $attrs) .'"></li></ul>';
            return $html;
        }

        if (!strrpos($currentHtml, '<ol id="list' . $numId . '"'))
            $html .= '<ol id="list' . $numId . '">';

        /*if (strrpos($this->_numbering[$numId][$lvlId], 'decimal') || strrpos($this->_numbering[$numId][$lvlId], 'decimal-leading-zero')) {
            $className .= " decimal-list";
        } */

        $currentValue = $this->_maxCounts[$numId][$lvlId] - $value;

        if ($className)
            if(isset($this->_numbering[$numId]))
                $html .= '<li value="' . $currentValue . '" class="' . $className .'" style="list-style-position: inside; ' . $this->_numbering[$numId][$lvlId] . implode(';', $attrs) . '">';
            else $html .= '<li value="' . $currentValue . '" class="' . $className .'" style="list-style-position: inside; list-style-type: none;' . implode(';', $attrs) .'">';
        else
            if(isset($this->_numbering[$numId]))
                $html .= '<li value="' . $currentValue . '" style="list-style-position: inside; ' . $this->_numbering[$numId][$lvlId] . '">';
            else $html .= '<li value="' . $currentValue . '" style="list-style-position: inside; list-style-type: none;' . implode(';', $attrs) .'">';
        return $html;
    }

    public function getListStyle($style){
        switch ($style) {
            case "bullet": return 'disc'; break;
            case "decimal": return 'decimal'; break;
            case "decimalZero": return 'decimal-leading-zero'; break;
            case "lowerLetter": return 'lower-alpha'; break;
            case "lowerRoman": return 'lower-roman'; break;
            case "upperLetter": return 'upper-alpha'; break;
            case "upperRoman": return 'upper-roman'; break;
            default: return 'none'; break;
        }
    }

    public function getListsCount($children){
        $this->counts = array();
        foreach ($children as $child) {
            if ($child->pPr->numPr) {
                $numId = (int)$child->pPr->numPr->numId['val'];
                $ilvl = (int)$child->pPr->numPr->ilvl['val'];

                if (isset($this->_counts[$numId][$ilvl])) $this->_counts[$numId][$ilvl]++;
                else  $this->_counts[$numId][$ilvl] = 0;
            }
            else if ($child->pPr->pStyle) {
                $styleId = (String)$child->pPr->pStyle['val'];
                if (is_numeric($styleId)) $styleId = 'id' . $styleId;
                foreach ($this->_styleList as $style => $val){
                    if ($style == $styleId) {
                        $numId = $val['numId'];
                        if (isset($val['ilvl'])) $ilvl = $val['ilvl'];
                        else $ilvl = 0;
                        if (isset($this->_counts[$numId][$ilvl])) $this->_counts[$numId][$ilvl]++;
                        else  $this->_counts[$numId][$ilvl] = 0;
                    }
                }
            }
        }
        $this->getMaxCounts();
    }

    public function getMaxCounts(){
        foreach ($this->_counts as $numId => $elem){
            foreach ($elem as $lvlId => $val){
                $this->_maxCounts[$numId][$lvlId] = $val++;
            }
        }
    }
}