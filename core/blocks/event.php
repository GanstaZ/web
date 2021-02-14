<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
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
	* @param string $category Name of the category
	* @param array	$data	  Block data
	* @return void
	*/
	public function set_data(string $category, array $data): void
	{
		$this->get($category) ? self::$data[$category] = array_merge(self::$data[$category], $data) : self::$data[$category] = $data;
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
