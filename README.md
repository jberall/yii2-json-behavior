Json Behavior
=============
Attaches behavior for a Postgresql jsonb column

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist jberall/yii2-json-behavior "*"
```

or add

```
"jberall/yii2-json-behavior": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by:
You need to identify the 
json_attribute as your column in the database.
Will check to see if model and attribute exist.


use jberall\jsonbehavior;

	//declare the variables.
	//attributes.
	public $some_attribute,$another_attribute;
	//objects
	public $dimension;
	//arrayObjects.
	public $emails, $phone_numbers;
	
	
	
    public function behaviors() {

        $behaviors = [
            'attributeJsonBehavior' =>[
                'class' => JsonBehavior::className(),
                'attributes' => ['some_attribute'=>null, 'another_attribute'=>null],
                'json_attribute'=> 'data_json',
            ],
			'objectJsonBehavior' => [
                'class' => JsonBehavior::className(),
                'arrayObjects' => [
                    'dimension'=>'\full\path\Dimension',
                    
                 ],
                'json_attribute'=> 'data_json',                
            ],
            'arrayObjectsJsonBehavior' => [
                'class' => JsonBehavior::className(),
                'arrayObjects' => [
                    'emails'=>\full\path\'Email',
                    'phone_numbers'=>'\full\path\PhoneNumber',
                 ],
                'json_attribute'=> 'data_json',                
            ],

			//OR Combine into one.
			'namedBehavior' => [
                'class' => JsonBehavior::className(),
                'attributes' => ['some_attribute'=>null, 'another_attribute'=>null],
				'arrayObjects' => [
                    'dimension'=>'\full\path\Dimension',
                    
                 ],
                'arrayObjects' => [
                    'emails'=>\full\path\'Email',
                    'phone_numbers'=>'\full\path\PhoneNumber',
                 ],				 
                'json_attribute'=> 'data_json',			
			],
        ];
        return ArrayHelper::merge(parent::behaviors(),$behaviors);

    }