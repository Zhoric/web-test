<?php

class DocxReader {

    private $fileData = false;
    private $errors = array();
    private $styles = array();

    private $html = '';
    private $style = '';

    private $startTags = array();
    private $startAttrs = array();
    private $tags = array();
    private $attrs = array();
    private $isList = false;
    private $relations = array();
    private $path;

    public function __construct() {
    }

    public function setFile($path) {
        $this->fileData = $this->load($path);
    }

    /*
     *
     * ЗАГРУЗКА И СЧИТЫВАНИЕ АРХИВА ДОКУМЕНТА
     *
     */


    /**
     * Загрузка и открытие документ docx file, считывание файла со стилями, связями и основного файла
     * @param $file
     * @return mixed
     *
     */
    private function load($file) {
        if (file_exists($file)) {
            $zip = new ZipArchive();
            $openedZip = $zip->open($file);
            if ($openedZip != true) {
                $this->getOpenedZipError($openedZip);
                exit();
            }
            $this->setStyles($zip);
            $this->setRelations($zip);
            $data = $this->getMainXml($zip);
            $zip->close();
            return $data;
        }
        else $this->errors[] = 'File does not exist.';
    }

    /**
     * Установка ошибок при открытии архива
     * @param $openedZip
     *
     */
    private function getOpenedZipError($openedZip){
        switch($openedZip) {
            case ZipArchive::ER_EXISTS:
                $this->errors[] = 'File exists.';
                break;
            case ZipArchive::ER_INCONS:
                $this->errors[] = 'Inconsistent zip file.';
                break;
            case ZipArchive::ER_MEMORY:
                $this->errors[] = 'Malloc failure.';
                break;
            case ZipArchive::ER_NOENT:
                $this->errors[] = 'No such file.';
                break;
            case ZipArchive::ER_NOZIP:
                $this->errors[] = 'File is not a zip archive.';
                break;
            case ZipArchive::ER_OPEN:
                $this->errors[] = 'Could not open file.';
                break;
            case ZipArchive::ER_READ:
                $this->errors[] = 'Read error.';
                break;
            case ZipArchive::ER_SEEK:
                $this->errors[] = 'Seek error.';
                break;
        }
    }

    /**
     * Установка общих стилей
     * @param $zip
     *
     */
    private function setStyles($zip){
        if (($styleIndex = $zip->locateName('word/styles.xml')) !== false) {
            $stylesXml = $zip->getFromIndex($styleIndex);
            $xml = simplexml_load_string($stylesXml);
            $namespaces = $xml->getNamespaces(true);
            $children = $xml->children($namespaces['w']);

            foreach ($children->style as $s) {
                $attr = $s->attributes('w', true);
                if (isset($attr['styleId'])) {
                    $tags = array();
                    $attrs = array();
                    foreach (get_object_vars($s->rPr) as $tag => $style) {
                        $att = $style->attributes('w', true);
                        switch ($tag) {
                            case "b":
                                $tags[] = 'strong';
                                break;
                            case "i":
                                $tags[] = 'em';
                                break;
                            case "color":
                                //echo (String) $att['val'];
                                $attrs[] = 'color:#' . $att['val'];
                                break;
                            case "sz":
                                $attrs[] = 'font-size:' . $att['val'] . 'px';
                                break;
                        }
                    }
                    $styles[(String)$attr['styleId']] = array('tags' => $tags, 'attrs' => $attrs);
                }
            }
            $this->styles = $styles;
        }
    }

    /**
     * Считывание файла word/_rels/document.xml.rels и и сохранение связей
     * @param $zip
     */
    private function setRelations($zip){
        if (($index = $zip->locateName('word/_rels/document.xml.rels')) !== false) {
            $data = $zip->getFromIndex($index);
            $xml = simplexml_load_string($data);
            foreach ($xml->Relationship as $rel => $val){
                $this->relations[(String)$val['Id']] = (String)$val['Target'];
            }
        }
    }

    /**
     * Считывание основного файла word/document.xml
     * @param $zip
     * @return string
     */
    private function getMainXml($zip){
        $data = null;
        if (($index = $zip->locateName('word/document.xml')) !== false) {
            $data = $zip->getFromIndex($index);
        }
        return $data;
    }

    /**
     * Загрузка изображений документа file в папку path и установка на них прав
     * @param $file
     * @param $path
     *
     */
    public function loadImages($file, $path){
        $this->path = $path;
        if (file_exists($file)) {
            $files = array();
            $zip = new ZipArchive();
            $openedZip = $zip->open($file);
            if ($openedZip != true) {
                $this->getOpenedZipError($openedZip);
                exit();
            }
            // поиск папки с изображениями в архиве и сохранение её содержимого в files
            for($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (strpos($entry, "/media/")) {
                    $files[] = $entry;
                }
            }
            if ($zip->extractTo($path, $files) === true) {
                exec ("find " . $path . " -type d -exec chmod 0777 {} +"); //for sub directory
                exec ("find " . $path . " -type f -exec chmod 0777 {} +"); //for files inside directory
            }
            $zip->close();
            }
        else $this->errors[] = 'File does not exist.';
    }

    /*
     *
     * ОБЩАЯ ОБРАБОТКА ДОКУМЕНТА
     *
     */

    /**
     * Интерпретация строки, содержащей информацию файла, в XML-объект и передача его далее
     * Фомирование начальных и конечных тегов документа
     * @return string
     */
    public function toHtml() {
        if (!$this->fileData) {
            $this->errors[] = 'Файл не загружен!';
            exit();
        }
        $xml = simplexml_load_string($this->fileData);
        $namespaces = $xml->getNamespaces(true);
        $children = $xml->children($namespaces['w']);
        $childrenBody = $children->body->children($namespaces['w']);

        $this->html = '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" /><title></title>
        <style>span.block { display: block; } body { padding-left: 100px; padding-right: 100px}</style></head><body>';

        $this->convertDoc($childrenBody);
        $this->addEnd();
        return $this->html . '</body></html>';
    }


    /**
     * Общая обработка документа
     * @param $children - вложенные теги body XML-объекта
     */
    private function convertDoc($children){
        foreach ($children as $child) {
            $this->style = '';
            $this->isList = false;

            $this->parseTable($child);
            $this->parseParagraphStyle($child);
            $this->parseProperties($child);
            $this->parseList($child);

            // если есть текст, то обработать его;
            // иначе пустая строка
            if (isset($child->r)) {
                $this->parseText($child->r);
            }
            else $this->html .= '<br>';
        }
    }

    /*
     * ОБРАБОТКА ТАБЛИЦ
     */

    private function parseTable($table){
        if (isset($table->tblPr)) {
            $this->html .= '<table>';
            $this->parseTableProperties($table->tblPr);
            foreach ($table->tr as $tr) $this->parseTableRow($tr);
            $this->html .= '</table>';
        }
    }

    private function parseTableProperties($tblPr){
        //$att = $tblPr->attributes('w', true);
    }

    private function parseTableRow($tr){
        $this->html.= '<tr>';
        foreach ($tr->tc as $tc) {
            $cellStyle = $this->parseCellStyle($tc->tcPr);
           // dd($cellStyle);
            $this->html.= '<td ' . $cellStyle . ' >';
            foreach ($tc->p as $p) {
                $this->style = '';
                $this->parseProperties($p);
                $this->parseText($p->r);
            }
            $this->html .= '</td>';
        }
        $this->html .= '</tr>';


    }

    private function parseCellStyle($cellStyle){
        $style = " style='";
        foreach (get_object_vars($cellStyle) as $att => $val) {
            switch ($att) {
                case "tcW":
                    $style .= 'width:' . ($val['w']/10) . 'px;' ;
                    break;
                case "vAlign":
                    if ($val['val'] == 'center') $cellStyle .= 'vertical-align: middle;';
                    else $style .= 'vertical-align:' . $val['val'];
                    break;
            }
        }
        $style .= "'";
        return $style;
    }


    /*
     * ОБРАБОТКА ПАРАГРАФОВ
     */

    private function parseParagraphStyle($p){
        if ($p->pPr->pStyle) {
            $objectAttrs = $p->pPr->pStyle->attributes('w', true);
            $objectStyle = (String)$objectAttrs['val'];
            if (isset($this->styles[$objectStyle])) {
                $this->startTags = $this->styles[$objectStyle]['tags'];
                $this->startAttrs = $this->styles[$objectStyle]['attrs'];
            }
        }
    }

    private function parseProperties($p){
        if ($p->pPr) {
            if ($p->pPr->spacing) {
                $att = $p->pPr->spacing->attributes('w', true);
                if (isset($att['before'])) {
                    $this->style .= 'padding-top:' . ($att['before'] / 10) . 'px;';
                }
                if (isset($att['after'])) {
                    $this->style .= 'padding-bottom:' . ($att['after'] / 10) . 'px;';
                }
            }
            if ($p->pPr->jc) {
                $att = $p->pPr->jc->attributes('w', true);
                if ($att['val'] == 'both')
                    $this->style .= 'text-align: justify;';
                else $this->style .= 'text-align:' . $att['val'] . ';';
            }
            if ($p->pPr->ind) {
                $att = $p->pPr->ind->attributes('w', true);
                $this->style .= 'text-indent:' . ($att['firstLine'] / 10) . 'px;';
            } else $this->style .= 'text-indent: 0px;';

            $this->html .= '<span class="block" style="' . $this->style . '">';
        }
    }

    /*
     * ОБРАБОТКА СПИСКОВ
     */

    private function parseList($p){
        if ($p->pPr->numPr) {
            $this->isList = true;
            if ($p->pPr->numPr->ilvl) {
                $att = $p->pPr->numPr->ilvl->attributes('w', true);
                if ($att['val'] == 1) $this->html .= '<li>';
            }
            if ($p->pPr->numPr->numId) {
                $att = $p->pPr->numPr->numId->attributes('w', true);
                if ($att['val'] == 1) $this->html .= '<li style="list-style-type:decimal;">';
            }
        }
    }


    /*
     * ОБРАБОТКА ТЕКСТА
     */

    private function parseText($paragraph){
        foreach ($paragraph as $part) {
            $this->tags = $this->startTags;
            $this->attrs = $this->startAttrs;

            foreach (get_object_vars($part->pPr) as $k => $v) {
                if ($k = 'numPr') {
                    $this->tags[] = 'li';
                }
            }

            foreach (get_object_vars($part->rPr) as $tag => $tagStyle) {
                $this->parseTextStyle($tag, $tagStyle);
            }

            foreach ($part->drawing as $draw) {
                $this->parseDrawing($draw);
            }

            $openTags = '';
            $closeTags = '';
            foreach ($this->tags as $tag) {
                $openTags.='<' . $tag . '>';
                $closeTags.='</' . $tag . '>';
            }
            $this->html.='<span style="' . implode(';', $this->attrs) . '">' . $openTags . $part->t . $closeTags . '</span>';
        }
        if ($this->isList) {
            $this->html.='</li>';
        }
        $this->html.="</span>";
    }

    private function parseTextStyle($tag, $tagStyle){
        $att = $tagStyle->attributes('w', true);
        switch ($tag) {
            case "b":
                $this->tags[] = 'strong';
                break;
            case "i":
                $this->tags[] = 'em';
                break;
            case "color":
                $this->attrs[] = 'color:#' . $att['val'];
                break;
            case "sz":
                $this->attrs[] = 'font-size:' . $att['val'] . 'px';
                break;
        }
    }

    /*
     * ОБРАБОТКА ГРАФИКИ
     */

    private function parseDrawing($draw){
        $namespaces = $draw->getNamespaces(true);
        $wp = $draw->children($namespaces['wp']);
        if ($wp->inline){
            $this->parseInlineWP($wp->inline);
        }
        //if ($wp->anchor)
        $a = $wp->inline->children($namespaces['a']);
        $this->parseGraphic($a->graphic);
    }

    private function parseInlineWP($wp){
    //EMU = pixel * 914400 / 96

    }

    private function parseGraphic($graphic){
        $namespaces = $graphic->getNamespaces(true);
        $pic = $graphic->graphicData->children($namespaces['pic']);
        $picFill = $pic->pic->blipFill->children($namespaces['a']);
        $relNameAttr = $picFill->blip->attributes('r', true);
        $relName = (String)$relNameAttr['embed'];
        foreach ($this->relations as $rel => $path){
            if ($rel == $relName) $this->html .= '<img src="http://' . $_SERVER['HTTP_HOST'] . '/' . $this->path . '/word/' . $path . '"> ';
        }
    }



    private function addEnd(){
        //Trying to weed out non-utf8 stuff from the file:
        $regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;
        preg_replace($regex, '$1', $this->html);
    }

    public function getErrors() {
        return $this->errors;
    }

    private function getStyles() {

    }

}