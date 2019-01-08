<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
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
	* Load zodiac data (tropical, sidereal, chinese)
	*
	* @param object $format Format date string to (m-d, Y & so on)
	* @return array
	*/
	public function load($format);

	/**
	* Get the zodiac data
	*
	* @param array $row Zodiac data
	* @return array Zodiac data, must have keys sign (clean name), info (element) e.g.
	*		 ['sign' => '', 'info' => '', 'name' => '']
	*/
	public function get_data($row);

	/**
	* Returns the format of the zodiac date
	*
	* @return string Format of the zodiac date
	*/
	public function get_format();
}
