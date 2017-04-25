<?php
namespace jberall\jsonbehavior;


use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\base\InvalidParamException;



class JsonBehavior extends \yii\base\Behavior
{
    /**
     * $attributes = ['value'=>null,'val2'=>'default val'];
     * @var array
     */
    public $attributes = [];
    
    /**
     *  $objects = ['objName'=>'\full\path\to\object', 'objName2'=>'\full\path\to\object2'];
     * @var array
     */
    public $objects = [];
    
    
    /**
     *
     * @var array
     */
    
    public $arrayObjects = [];
    
    /**
     * Json column in the database.
     * 
     * @var string $json_attribute
     */
    public $json_attribute;


    /**
     * @inheritdoc
     */
    public function events()
    {
//        die(__METHOD__);
        return [
            ActiveRecord::EVENT_INIT          => 'initialization',
            ActiveRecord::EVENT_AFTER_FIND    => 'decode',
            ActiveRecord::EVENT_BEFORE_INSERT => 'encode',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'encode',
            ActiveRecord::EVENT_AFTER_INSERT  => 'decode',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'decode',
        ];
    }

    
    public function assignAttribute($name,$value){
        if ($this->owner->hasProperty($name)) {
            $this->owner->$name = $value;
        } else {
            throw new InvalidParamException(get_class($this->owner) . ' has no attribute property named "' . $name . '".');
        }        
    }
    /**
     */
    public function initialization()
    {
        
       //check json_attribue exist
        if (!$this->owner->hasAttribute($this->json_attribute)) {
            throw new InvalidParamException(get_class($this->owner) . ' has no Json attribute property named "' . $this->json_attribute . '".');
        } 

        foreach ($this->attributes as $attribute=>$value) {
            $this->assignAttribute($attribute,$value);
        }

        foreach ($this->objects as $attribute => $value) {
            if (!class_exists($value)) {
                throw new InvalidParamException("Object Path $value does not exist.");
            }
            $this->assignAttribute($attribute,new $value());
        }
        
        foreach ($this->arrayObjects as $attribute => $value) {
            if (!class_exists($value)) {
                throw new InvalidParamException("Object Path $value does not exist.");
            }
            $this->assignAttribute($attribute,[new $value()]);
        }
//        if ($this->objects)exit;
    }

    
    /**
     */
    public function decode()
    {
        
        $arrJson = Json::decode($this->owner->{$this->json_attribute});
        
        foreach ($this->attributes as $attribute => $value) {
            $this->owner->$attribute = $arrJson[$attribute] ?? null;
        }
        
        foreach($this->objects as $attribute => $value) {
            if (isset($arrJson[$attribute])) {
                $this->owner->$attribute->load($arrJson[$attribute],'');
            }
        }
        
        foreach ($this->arrayObjects as $attribute => $value) {
            if (isset($arrJson[$attribute])){
                $models = null;  //reset
                foreach($arrJson[$attribute] as $i => $data) {
                    $models[$i] = new $value($data);
                }
            $this->owner->$attribute = $models;        

            }
        }

    }

    /**
     */
    public function encode()
    {
        $attributes = [];
        $objects = [];
        $arrayObjects = [];
        
        $arrJson = ($arr = Json::decode($this->owner->{$this->json_attribute})) ? $arr : [];

        foreach ($this->attributes as $attribute => $value) {
            $attributes[$attribute] = $this->owner->$attribute;
        }
          
        foreach ($this->objects as $attribute => $value) {
            $objects[$attribute] = $this->owner->$attribute;
        }

        foreach ($this->arrayObjects as $attribute => $value) {
            $arrayObjects[$attribute] = array_values($this->owner->$attribute);
        }
        
        $this->owner->{$this->json_attribute} = Json::encode(array_merge($arrJson,$attributes,$objects,$arrayObjects));
                
    }
}
