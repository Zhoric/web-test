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
            $newAttr = $this->parseTextStyle($part);

            foreach ($part->drawing as $draw) {
                if ($className) $html .= '<div class="' . $className .'" style="' . implode(';', $attrs) . implode(';', $newAttr) . '">';
                else $html .= '<div style="' . implode(';', $attrs) . implode(';', $newAttr) . '">';
                $html .= $this->_graphicParser->parseDrawing($draw);
                $html .= "</div>";
            }

            $html .= '<span class="inline-block" style="' . implode(';', $attrs) . implode(';', $newAttr) . '">' . htmlentities($part->t) . '</span>';
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
        return $attrs;
    }

}