<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugins\astro\zodiac;

/**
* DLS Web zodiac interface
*/
interface zodiac_interface
{
	/**
	* Load the zodiac data (tropical, sidereal, chinese)
	*
	* @param string $format Format date string to (m-d, Y & so on)
	* @return array
	*/
	public function load(string $format);

	/**
	* Get the zodiac data
	*
	* @param array $row data
	* @return array zodiac data, must have keys
	*	 [stem, sign, animal, plant, gem, ruler, extra, dir, element, name]
	*/
	public function get_data(array $row);

	/**
	* Returns the format of the zodiac date
	*
	* @return string format
	*/
	public function get_format();
}
