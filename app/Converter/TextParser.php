<?php


class TextParser {
    /*
    * ОБРАБОТКА ТЕКСТА
    */

    private $_graphicParser;

    public function __construct(GraphicParser $graphicParser)
    {
        $this->_graphicParser = $graphicParser;
    }

    public function parseText($paragraph, $className, $attrs, $isList = null, $isUlEnd = null){
        $html = '';
        foreach ($paragraph as $part) {
            $tags = array();

            foreach (get_object_vars($part->pPr) as $k => $v) {
                if ($k = 'numPr') {
                    $tags[] = 'li';
                }
            }

            $newAttr = $this->parseTextStyle($part);

            foreach ($part->drawing as $draw) {
                if ($className) $html .= '<div class="' . $className .'" style="' . implode(';', $attrs) . implode(';', $newAttr) . '">';
                else $html .= '<div style="' . implode(';', $attrs) . implode(';', $newAttr) . '">';
                $html .= $this->_graphicParser->parseDrawing($draw);
                $html .= "</div>";
            }

            $openTags = '';
            $closeTags = '';
            foreach ($tags as $tag) {
                $openTags .= '<' . $tag . '>';
                $closeTags .= '</' . $tag . '>';
            }

            $html .= '<span class="inline-block" style="' . implode(';', $attrs) . implode(';', $newAttr) . '">' . $openTags . $part->t . $closeTags . '</span>';

        }

        if ($isList) {
            $html .= '</li>';
        }
        if ($isUlEnd){
            $html .= '</ol>';
        }
        return $html;
    }

    public function parseTextStyle($part){
        $attrs = array();
        $namespaces = $part->getNamespaces(true);
        if (isset($part->rPr)) {
            foreach ($part->rPr->children($namespaces['w']) as $tag => $tagStyle) {
                switch ($tag) {
                    case "b":
                        $attrs[] = 'font-weight: bold;';
                        break;
                    case "i":
                        $attrs[] = 'font-style: italic;';
                        break;
                    case "color":
                        $attrs[] = 'color:#' . $tagStyle['val'] . ';';
                        break;
                    case "sz":
                        $attrs[] = 'font-size:' . $tagStyle['val'] . 'px;';
                        break;
                    case "szCs":
                        $attrs[] = 'font-size:' . $tagStyle['val'] . 'px;';
                        break;
                }
            }
        }
        //if (!in_array('font-weight: bold;', $attrs)) $attrs[] = 'font-weight: normal;';
        //if (!in_array('font-style: italic;', $attrs)) $attrs[] = 'font-style: normal;';
        return $attrs;
    }
}