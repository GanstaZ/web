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

use dls\web\core\plugins\astro\astro_interface;

/**
* DLS Web zodiac base class
*/
abstract class base implements astro_interface, zodiac_interface
{
	/**
	* Zodiac format
	* @var string
	*/
	protected $format;

	/** @var array zodiac types */
	protected $types = ['ZODIAC', 'TROPICAL', 'SIDEREAL', 'NATIVE', 'CELTIC', 'CHINESE'];

	/** @var array zodiac elements */
	protected $elements = ['TOTEM', 'FIRE', 'EARTH', 'AIR', 'WATER'];

	/**
	* {@inheritdoc}
	*/
	public function get_format()
	{
		return $this->format;
	}

	/**
	* Sets the format of the zodiac
	*
	* @param string	$val Set value to the given property
	*/
	public function set_format($format)
	{
		$this->format = $format;
	}
}
