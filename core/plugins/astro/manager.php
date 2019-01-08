<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugins\astro;

use phpbb\di\service_collection;
use dls\web\core\plugins\base;

/**
* DLS Web astro manager
*/
class manager extends base
{
	/** @var array Contains all available astro types */
	protected static $astro = false;

	/**
	* Constructor
	*
	* @param service_collection $astro_types Service collection
	*/
	public function __construct(service_collection $astro_types)
	{
		$this->register_astro_types($astro_types);
	}

	/**
	* Register all available astro types
	*
	* @param Service collection of astro types
	*/
	protected function register_astro_types($astro_types): void
	{
		if (!empty($astro_types))
		{
			self::$astro = [];
			foreach ($astro_types as $type)
			{
				$data = $type::astro_data();

				self::$astro[$data['type']][$data['name']] = $type;
			}
		}
	}

	/**
	* Get astro type by name
	*	 For zodiac $this->get_astro_type($name)['service_name']->load($date->format('...'))
	*
	* @param string $name Name of the astro type
	* @return array
	*/
	public function get_astro_type($name): array
	{
		return self::$astro[$name] ?? [];
	}

	/**
	* Get astro data
	*
	* @param string		 $name Name of the astro type
	* @param null|object $date Format date string to (m-d, Y & so on)
	* @return array
	*/
	public function get_data(string $name, $date = null): ?array
	{
		if (!$this->is_valid_input($name, $date))
		{
			return null;
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
	* @return bool
	*/
	private function is_valid_input($name, $date): bool
	{
		return $this->get_astro_type($name) || (null !== $date && !is_object($date));
	}
}
