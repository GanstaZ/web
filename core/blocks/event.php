<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks;

/**
* DLS Web blocks event class
*/
class event
{
	/** @var array Contains validated blocks data */
	protected static $data;

	/**
	* Set template data
	*
	* @param string $name Name of the category
	* @param array	$data Block data
	* @return void
	*/
	public function set_data(string $name, array $data): void
	{
		if (!$this->get($name))
		{
			self::$data[$name] = $data;
		}

		self::$data[$name] = array_merge(self::$data[$name], $data);
	}

	/**
	* Get data from a given category
	*
	* @param string $category Category name
	* @return array
	*/
	public function get(string $category): array
	{
		return self::$data[$category] ?? [];
	}
}
