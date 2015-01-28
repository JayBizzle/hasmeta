<?php namespace Jaybizzle\Hasmeta;

trait HasMetaTrait
{
	protected $metaData = NULL;
	
	protected function newMeta($meta, $extra = array())
	{
		$metaModel = new $this->meta_model();
		$metaModel->{$this->meta_key_name} = $meta[$this->meta_key_name];
		$metaModel->{$this->meta_value_name} = $meta[$this->meta_value_name];
		$metaModel->{$this->meta_foreign_key} = $this->{$this->meta_primary_key};

		// if(!empty($extra)) {
		// 	foreach($extra as $key => $value) {

		// 	}
		// }

		return $metaModel;
	}


	/**
	 * Get the data associated with this model
	 *
	 * @return hasOne
	 */
	public function meta()
	{
		return $this->hasMany($this->meta_model, $this->meta_foreign_key, $this->primaryKey);
	}

	/**
	 * Dynamically retrieve attributes on the model.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		$value = $this->getAttribute($key);

		if (is_null($value)) {
			try {
				if(isset($this->getMeta()->$key)) {
					$value = $this->getMeta()->$key->{$this->meta_value_name};
				} else {
					$value = NULL;
				}
			} catch (Exception $e) {
				$value = NULL;
			}
		}

		return $value;
	}

	/**
	 * Dynamically set attributes on the model.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		if (array_key_exists($key, $this->getAttributes())) {
			$this->setAttribute($key, $value);
			return;
		}

		try {
			if (isset($this->getMeta()->$key)) {
				$this->getMeta()->$key->{$this->meta_value_name} = $value;
				return;
			}
		} catch (Exception $e) { }

		if(\Config::get('database.default') === 'sqlite') {
			$columns = \DB::select('PRAGMA table_info('.$this->table.')');

			foreach ($columns as $column) {
				if ($column->name === $key) {
					$this->setAttribute($key, $value);
					return;
				}
			}
		} else {
			$columns = \DB::select('DESCRIBE '.$this->table);

			foreach ($columns as $column) {
				if ($column->Field === $key) {
					$this->setAttribute($key, $value);
					return;
				}
			}
		}

		$this->getMeta()->$key = $this->newMeta(array($this->meta_key_name => $key, $this->meta_value_name => $value));
	}


	public function __isset($key)
	{
		if ((isset($this->attributes[$key]) || isset($this->relations[$key])) || ($this->hasGetMutator($key) && ! is_null($this->getAttributeValue($key)))) {
			return true;
		}

		return isset($this->getMeta()->$key->{$this->meta_value_name});
	}

	public function push() 
	{
		return parent::push() && $this->deleteMeta() && $this->saveMeta();
	}

	public function save(array $options = array())
	{
		return parent::save() && $this->deleteMeta() && $this->saveMeta();
	}

	/**
	 * Get the data associated with this model in a usable format
	 *
	 * @return array
	 */
	protected function getMeta()
	{
		if (is_null($this->metaData)) {
			$primaryKey = $this->primaryKey;

			if (is_null($this->primaryKey)) {
				// If nothing has been set and there is no ID then there will be no data
				$this->metaData = (object)[];
			} else {
				$meta = $this->meta;

				$niceDataArray = array();

				foreach ($meta as $data) {
					$niceDataArray[$data->{$this->meta_key_name}] = $data;
				}

				$this->metaData = (object)$niceDataArray;
			}
		}
		return $this->metaData;
	}

	protected function saveMeta()
	{
		$primaryKey = $this->primaryKey;

		foreach ((array)$this->getMeta() as $data) {
			
			if (is_null($data->{$this->meta_foreign_key})) {
				$data->{$this->meta_foreign_key} = $this->$primaryKey;
			}
			
			if(!is_null($data->{$this->meta_value_name}))
				$data->save();
		}
		
		return true;
	}

	protected function deleteMeta() 
	{
		foreach ((array)$this->getMeta() as $data) {
			if(is_null($data->{$this->meta_value_name})) {
				$dataID = $this->meta_primary_key;
				$data->destroy($data->$dataID);
			}
		}
		
		return true;
	}
}
