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
* DLS Web astro base class
*/
abstract class base implements astro_interface
{
	/**
	* Astro name
	* @var string
	*/
	protected $name;

	/**
	* {@inheritdoc}
	*/
	public function get_name()
	{
		return $this->name;
	}

	/**
	* Sets the name of the astro type
	*
	* @param string	$val Set value to the given property
	*/
	public function set_name($name)
	{
		$this->name = $name;
	}
}
