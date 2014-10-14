HasMeta
=======

A Laravel trait to access model meta data as if it was a property on your model

Installation
============

Run `composer require jaybizzle/hasmeta` or add `"jaybizzle/hasmeta": "dev-master"` to your `composer.json` file

In the Model that you want to utilise `HasMeta` add the following properties

```PHP
	use Jaybizzle\Hasmeta\HasMetaTrait;

	protected $meta_model       = 'ModelName'; // the name of your meta data model
	protected $meta_foreign_key = 'id'; // the primary key of your main model
	protected $meta_primary_key = 'meta_id'; // the primary key of you meta data model
	protected $meta_key_name    = 'dataName'; // the column name that stores your meta data key name
	protected $meta_value_name  = 'dataValue'; // the column name that stores you meta data value
```

Finally, add the following relationship

```php
	public function meta() { // has to be called 'meta'
		return $this->hasMany('modelName', 'id', 'meta_id');
	}
```
