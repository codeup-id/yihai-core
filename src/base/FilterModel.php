<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;


class FilterModel extends DynamicModel
{
    private $_attributeLabels = [];
    /**
     * create new FilterModel class from array rules
     * @param array $rules
     * @param array $config
     * @return FilterModel
     */
    public static function newFromRules($rules, $config = [])
    {
        $attributes = [];

        foreach ($rules as $i => $rule) {
            static::collectAttributes($rule, $attributes);
        }
        $cls = new static($attributes, $config);

        foreach ($rules as $i => $rule) {
            if(isset($rule[0]) && isset($rule[1])){
                $cls->addRule($rule[0], $rule[1]);
            }
        }
        return $cls;
    }
    private static function collectAttributes($rule, &$attributes){
        if(!isset($rule[0])) return;

        if(is_string($rule[0])){
            if(!in_array($rule[0], $attributes)){
                $attributes[] = $rule[0];
            }
        }elseif(is_array($rule[0])){
            foreach($rule[0] as $attribute){
                if(!in_array($attribute, $attributes)){
                    $attributes[] = $attribute;
                }
            }
        }
    }

    public function addAttributeLabel($key, $val)
    {
        $this->_attributeLabels[$key] = $val;
    }

    public function attributeLabels()
    {
        return $this->_attributeLabels;
    }
}