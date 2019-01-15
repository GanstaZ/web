<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dls.org/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugins\astro;

/**
* DLS Web interface for astro
*/
interface astro_interface
{
	/**
	* Get astro data
	*
	* @return array Astro data, must have keys type & name
	*		 ['type' => '', 'name' => '']
	*/
	public static function astro_data();
}
