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
	* @param string $section Name of the section
	* @param array	$data	 Block data
	* @return void
	*/
	public function set_data(string $section, array $data): void
	{
		$this->get($section) ? self::$data[$section] = array_merge(self::$data[$section], $data) : self::$data[$section] = $data;
	}

	/**
	* Get data from a given section
	*
	* @param string $section Name of the section
	* @return array
	*/
	public function get(string $section): array
	{
		return self::$data[$section] ?? [];
	}
}
