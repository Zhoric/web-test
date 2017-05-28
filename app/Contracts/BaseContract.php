<?php


class BaseContract
{


    public function fillFromJson($json){
        $jsonArray = $json;
        foreach($jsonArray as $key=>$value){
            $this->$key = $value;
        }
    }

}