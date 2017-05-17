<?php

class ListParser {
    /*
    * ОБРАБОТКА СПИСКОВ
    */

    private $_numbering;
    private $_textParser;
    private $_paragraphParser;

    private $_isList;

    public function __construct(TextParser $textParser, ParagraphParser $paragraphParser)
    {
        $this->_textParser = $textParser;
        $this->_paragraphParser = $paragraphParser;
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

    public function parseList($p, $currentHtml){
        //print_r($this->_numbering);
        $html = '';
        if ($p->pPr->numPr) {
            $this->_isList = true;
            $numId = (int)$p->pPr->numPr->numId['val'];
            $lvlId = (int)$p->pPr->numPr->ilvl['val'];

            if (!strrpos($currentHtml, '<ul id="list'. $numId . '"'))
                $html .= '<ul id="list'. $numId . '">';

            $attrs = $this->_textParser->parseTextStyle($p->pPr);
            $className = $this->_paragraphParser->parseParagraphStyle($p);

            if ($className)
                if(isset($this->_numbering[$numId]))
                    $html .= '<li class="' . $className .'" style="list-style-position: inside; ' . $this->_numbering[$numId][$lvlId] . implode(';', $attrs) . '">';
                else $html .= '<li class="' . $className .'" style="list-style-position: inside; list-style-type: none;' . implode(';', $attrs) .'">';
            else
                if(isset($this->_numbering[$numId]))
                    $html .= '<li style="list-style-position: inside; ' . $this->_numbering[$numId][$lvlId] . '">';
                else $html .= '<li style="list-style-position: inside; list-style-type: none;' . implode(';', $attrs) .'">';
        }
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
}