<?php

namespace jberall\jsonbehavior\helpers;

/**
 * Description of JsonBSearchHelper
 * Holds search helpers for JsonB in Postgresql
 * 
 * @author Jonathan Berall <jberall@gmail.com>
 */
class JsonBSearchHelper {
    /**
     * When searching for an attribute with an array inside JsonB use the following function
     * Where $tags = ['val1','val2'];
     * $query->andFilterWhere (['@>','json_data',($cond = JsonBHelper::jsonbExactArray($this,'tags')) ? '{"tags": ['.$cond .']}' : null]);
     * @param model $model
     * @param string $attribute
     * @return string || null
     */
    public function jsonbSearchExactArray($model,string $attribute,string $jsonAttribute) {
        if (!$model->$attribute) {
            return;
        } 
        $disp = '';
        foreach ($model->$attribute as $key => $value) {
            $disp .= '"'.$value.'",';
        }
        $disp = substr($disp,0,-1);
        //remove trailing comma
        return "{\"{$jsonAttribute}\": [{$disp}]}";
       
    } 
    
    /**
     * When searching for a node within a Json document and 1 attribute
     * Where $searchExactEmail = {"emails": [{"email": $emailValue}]}
     * $query->andFilterWhere(['@>','json_data',($this->searchExactEmail) ?'{"emails": [{ "email": "'.$this->searchExactEmail.'"}]}' : null]);
     * @param model $model
     * @param string $attribute
     * @param array $jsonAttribute
	 * @return string || null
     */
    public function jsonbSearchExactNode($model,string $attribute,array $jsonAttribute ){
        if (!$model->$attribute) {
            return;
        }
        $key = (key($jsonAttribute));
        $current = (current($jsonAttribute));
        return   "{\"{$key}\":[{\"{$current}\": \"{$model->$attribute}\"}]}";
    }
}
