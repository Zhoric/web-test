<?php

/*
 * ЗАГРУЗКА И СЧИТЫВАНИЕ АРХИВА ДОКУМЕНТА
 */


class DocxReader{

    private $fileData = false;
    private $errors = array();
    private $styles = array();

    private $html = '';

    private $_mainParser;

    private $_tableParser;
    private $_textParser;
    private $_graphicParser;
    private $_listParser;
    private $_paragraphParser;

    public function __construct()
    {
        $this->_paragraphParser = new ParagraphParser();
        $this->_graphicParser = new GraphicParser();
        $this->_textParser = new TextParser($this->_graphicParser);
        $this->_tableParser = new TableParser($this->_textParser, $this->_paragraphParser);
        $this->_listParser = new ListParser($this->_textParser, $this->_paragraphParser);

        $this->_mainParser = new MainParser($this->_tableParser, $this->_textParser,
            $this->_graphicParser, $this->_listParser, $this->_paragraphParser);
    }

    public function setFile($path) {
        $this->fileData = $this->load($path);
    }

    /**
     * Загрузка и открытие документ docx file, считывание файла со стилями, связями и основного файла
     * @param $file
     * @return mixed
     * @throws
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
            $this->setNumbering($zip);
            $data = $this->getMainXml($zip);
            $zip->close();
            return $data;
        }
        else throw new Exception('Файла не существует!');
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
            $this->styles = $this->_mainParser->parseStyle($children);
        }
        $this->_mainParser->setStyles($this->styles);
    }

     /**
     * Считывание файла word/_rels/document.xml.rels и и сохранение связей
     * @param $zip
     */
    private function setRelations($zip){
        $relations = array();
        if (($index = $zip->locateName('word/_rels/document.xml.rels')) !== false) {
            $data = $zip->getFromIndex($index);
            $xml = simplexml_load_string($data);
            foreach ($xml->Relationship as $rel => $val){
                $relations[(String)$val['Id']] = (String)$val['Target'];
            }
        }
        $this->_mainParser->setRelations($relations);
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
     * @throws
     */
    public function loadImages($file, $path){
        $this->_mainParser->setPath($path);
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
        else throw new Exception('File does not exist.');
    }


    private function setNumbering($zip){
        $numbering = array();
        if (($numberingIndex = $zip->locateName('word/numbering.xml')) !== false) {
            $numberXml = $zip->getFromIndex($numberingIndex);
            $xml = simplexml_load_string($numberXml);
            $namespaces = $xml->getNamespaces(true);
            $children = $xml->children($namespaces['w']);
            $numbering = $this->_mainParser->parseNumbering($children);
        }
        $this->_mainParser->setNumbering($numbering);
    }


    /**
     * Интерпретация строки, содержащей информацию файла, в XML-объект и передача его далее
     * Фомирование начальных и конечных тегов документа
     * @return string
     * @throws
     */
    public function toHtml() {
        if (!$this->fileData) {
            throw new Exception('Файл не загружен!');
            exit();
        }
        $xml = simplexml_load_string($this->fileData);
        $namespaces = $xml->getNamespaces(true);
        $children = $xml->children($namespaces['w']);
        $childrenBody = $children->body->children($namespaces['w']);

        $this->html = '<html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" /><title></title>'.
            '<style>div.inline-block { display: inline-block; white-space: pre-wrap; } body { padding-left: 100px; padding-right: 100px} ul {padding: 0}';

        foreach ($this->styles as $id => $style){
            foreach ($style['attrs'] as $styleAttr){
                $this->html .= ' .' . $id . " " . $styleAttr;
            }
        }
        $this->html .= '</style></head><body>';
        $this->html .= $this->_mainParser->convertDoc($childrenBody);
        $this->addEnd();
        return $this->html . '</body></html>';
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


}