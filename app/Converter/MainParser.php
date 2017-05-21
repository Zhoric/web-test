<?php


class MainParser {

    private $_tableParser;
    private $_textParser;
    private $_graphicParser;
    private $_listParser;
    private $_paragraphParser;


    public function __construct(TableParser $tableParser, TextParser $textParser, GraphicParser $graphicParser,
                                ListParser $listParser, ParagraphParser $paragraphParser) {
        $this->_tableParser = $tableParser;
        $this->_textParser = $textParser;
        $this->_graphicParser = $graphicParser;
        $this->_listParser = $listParser;
        $this->_paragraphParser = $paragraphParser;
    }

    public function parseStyle($children){
        $styles = array();
        foreach ($children->style as $style) {
            $attr = $style->attributes('w', true);
            if (isset($attr['styleId'])) {
                $styleId = (String)$attr['styleId'];
                if (is_numeric($styleId)) $styleId = 'id' . $styleId;
                $styleType = $attr['type'];

                $tags = array();
                $attrs = array();

                if ($style->basedOn) {
                    $attr = $style->basedOn->attributes('w', true);
                    $id = (String)$attr['val'];
                    if (is_numeric($id)) $id = 'id' . $id;
                    $basedStyle = $styles[$id];
                    $tags = $basedStyle['tags'];
                    $attrs = $basedStyle['attrs'];
                }

                if ($styleType == 'table') {
                    $newAttrs = $this->_tableParser->parseTableProperties($style->tblPr);
                    if ($newAttrs)
                        foreach ($newAttrs as $newAttr)
                            $attrs[] = '{ ' . $newAttr . ' }';;

                    $newAttrs = $this->_tableParser->parseTableCellMar($style->tblPr);
                    if ($newAttrs)
                        foreach ($newAttrs as $newAttr)
                            $attrs[] = $newAttr;

                    $newAttrs = $this->_tableParser->parseTableBorders($style->tblPr);
                    if ($newAttrs)
                        foreach ($newAttrs as $newAttr)
                            if (strrpos($newAttr, '{')) $attrs[] = $newAttr;
                            else $attrs[] = '{ ' . $newAttr . ' }';
                }

                if ($styleType == 'paragraph') {
                    $newAttrs = $this->_paragraphParser->parseProperties($style);
                    if ($newAttrs)
                        foreach ($newAttrs as $newAttr)
                            $attrs[] = '{ ' . $newAttr . ' }';

                    $newAttrs = $this->_textParser->parseTextStyle($style);
                    if (!in_array('font-weight: bold;', $newAttrs)) $newAttrs[] = 'font-weight: normal;';
                    if (!in_array('font-style: italic;', $newAttrs)) $newAttrs[] = 'font-style: normal;';
                    if ($newAttrs)
                        foreach ($newAttrs as $newAttr)
                            $attrs[] = '{ ' . $newAttr . ' }';
                }

                $styles[$styleId] = array('tags' => $tags, 'attrs' => $attrs);
            }
        }
        return $styles;
    }

    public function parseStyleList($children){
        $styleList = array();
        foreach ($children->style as $style) {
            $attr = $style->attributes('w', true);
            if (isset($attr['styleId']) && isset($style->pPr->numPr)) {
                $styleId = (String)$attr['styleId'];
                if (is_numeric($styleId)) $styleId = 'id' . $styleId;

                $numId = (int)$style->pPr->numPr->numId['val'];
                if (isset($style->pPr->numPr->ilvl))
                    $ilvl = (int)$style->pPr->numPr->ilvl['val'];
                else $ilvl = null;
                $styleList[$styleId] = array('numId' => $numId, 'ilvl' => $ilvl);
            }
        }
        return $styleList;
    }

    public function parseNumbering($children){
        $numbering = array();
        $abstractNums = array();

        foreach ($children->num as $num)
            $abstractNums[(int)$num['numId']] = $num->abstractNumId['val'];

        foreach ($children->abstractNum as $absNum){
            $numStyle = array();
            foreach ($absNum->lvl as $lvl){
                $numStyle[(int)$lvl['ilvl']] = 'list-style-type:' . $this->_listParser->getListStyle($lvl->numFmt['val']) .'; ';
            }
            foreach ($abstractNums as $numId => $absId){
                if ((int)$absId == (int)$absNum['abstractNumId']) $numbering[$numId] = $numStyle;
            }
        }
        return $numbering;
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

    /**
     * Общая обработка документа
     * @param $children - вложенные теги body XML-объекта
     * @return string
     */
    public function convertDoc($children){
        $html = '';
        $this->_listParser->getListsCount($children);
        foreach ($children as $child) {
            $html .= $this->_tableParser->parseTable($child);
            $html .= $this->_listParser->parseList($child, $html);
            $isList = $this->_listParser->getIsList();
            $isUlEnd = $this->_listParser->getIsUlEnd();

            $className = $this->_paragraphParser->parseParagraphStyle($child);
            $attrs = $this->_paragraphParser->parseProperties($child);
            $divAttrs = array_merge($this->_paragraphParser->parseProperties($child), $this->_paragraphParser->parseDivProperties($child));

            if ($className)
                $html.='<div class="' . $className .'" style="' . implode(';', $divAttrs) . '">' ;
            else $html.='<div style="' . implode(';', $divAttrs) . '">' ;

            // если есть текст или изображение, то обработать их;
            // иначе пустая строка
            if (isset($child->r->t) || isset($child->r->drawing)) {
                $html .= $this->_textParser->parseText($child->r, $className, $attrs, $isList, $isUlEnd);
            }
            else if (isset($child->hyperlink->r->t)){
                $html .= $this->_textParser->parseText($child->hyperlink->r, $className, $attrs, $isList, $isUlEnd);
            }
            else $html .= '<br>';
            $html.='</div>';
        }
        return $html;
    }

    public function setStyles($styles){
        $this->_paragraphParser->setStyles($styles);
    }

    public function setRelations($relations){
        $this->_graphicParser->setRelations($relations);
    }

    public function setNumbering($numbering){
        $this->_listParser->setNumbering($numbering);
    }

    public function setPath($path){
        $this->_graphicParser->setPath($path);
    }

    public function setStyleList($styleList){
        $this->_listParser->setStyleList($styleList);
    }
}