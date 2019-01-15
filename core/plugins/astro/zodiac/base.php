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

use dls\web\core\plugins\plugin_interface;

/**
* DLS Web zodiac base class
*/
abstract class base implements plugin_interface, zodiac_interface
{
	/** @var string zodiac date format */
	protected $format;

	/** @var array zodiac types */
	protected $types = ['ZODIAC', 'TROPICAL', 'SIDEREAL', 'NATIVE', 'CELTIC', 'CHINESE', 'MYANMAR',];

	/** @var array zodiac elements */
	protected $elements = [
		1 => 'FIRE',
		2 => 'EARTH',
		3 => 'AIR',
		4 => 'WATER',
		5 => 'WOOD',
		6 => 'METAL',
		7 => 'TOTEM',
	];

	/** @var array cardinal directions */
	protected $direction = [
		1 => 'NORTH',
		2 => 'EAST',
		3 => 'SOUTH',
		4 => 'WEST',
		5 => 'NORTHEAST',
		6 => 'SOUTHEAST',
		7 => 'SOUTHWEST',
		8 => 'NORTHWEST',
		9 => 'CENTER',
	];

	/**
	* {@inheritdoc}
	*/
	public function get_data(array $row): array
	{
		return [
			'stem'	  => (int) $row['snr'],
			'sign'	  => (string) $row['sign'],
			'symbol'  => (string) $row['symbol'],
			'plant'	  => (string) $row['plant'],
			'gem'	  => (string) $row['gem'],
			'ruler'	  => (string) $row['ruler'],
			'extra'	  => (string) $row['ext'],
			'dir'	  => $this->direction[(int) $row['dir']],
			'element' => $this->elements[(int) $row['enr']],
			'name'	  => $this->types[(int) $row['type']],
		];
	}

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
	* @param string	$format Set value to the given property
	*/
	public function set_format($format)
	{
		$this->format = $format;
	}
}
