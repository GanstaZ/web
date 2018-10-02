<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugin\astro;

/**
* DLS Web astro manager
*/
class manager extends \dls\web\core\plugin\base
{
	/**
	* Array that contains all available astro types
	* @var array
	*/
	protected $astro;

	/**
	* Constructor
	*
	* @param @param \phpbb\di\service_collection $astro_types Astro types passed via the service container
	*/
	public function __construct(\phpbb\di\service_collection $astro_types)
	{
		$this->register_astro_types($astro_types);
	}

	/**
	* Get astro type by name
	*	 For zodiac $this->get_astro_type($name)[0]->load($date->format('...'))
	*
	* @param string $name Name of the astro type
	*
	* @return object
	*/
	public function get_astro_type($name)
	{
		return isset($this->astro[$name]) ? $this->astro[$name] : [];
	}

	/**
	* Get available types
	*
	* @return array
	*/
	public function get_all()
	{
		return $this->astro;
	}

	/**
	* Remove type
	*
	* @param string $name Name of the type service we want to remove
	*
	* @return void
	*/
	public function remove($name)
	{
		if (isset($this->astro[$name]) || array_key_exists($name, $this->astro))
		{
			unset($this->astro[$name]);
		}
	}

	/**
	* Register all available astro types
	*
	* @param array $astro_types Array of available astro types
	*/
	protected function register_astro_types($astro_types)
	{
		if (!empty($astro_types))
		{
			foreach ($astro_types as $type)
			{
				$this->astro[$type->get_name()][] = $type;
			}
		}
	}

	/**
	* Get astro data
	*
	* @param string		 $name Name of the astro type
	* @param null|object $date Format date string to (m-d, Y & so on)
	*
	* @return array|object
	*/
	public function get_data($name, $date = null)
	{
		if (!$this->is_valid_input($name, $date))
		{
			return;
		}

		$array = [];
		foreach ($this->get_astro_type($name) as $service)
		{
			$array = array_merge($array, $service->load($date->format($service->get_format())));
		}

		return $array;
	}

	/**
	* Check if our input data is valid
	*
	* @param string		 $name Name of the astro type
	* @param null|object $date Format date string to (m-d, Y & so on)
	*
	* @return bool true|false
	*/
	private function is_valid_input($name, $date)
	{
		return isset($this->astro[$name]) || (!is_null($date) && !is_object($date));
	}
}
