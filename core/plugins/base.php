<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugins;

/**
* DLS Web plugin base class
*/
abstract class base implements plugin_interface
{
	/**
	* Plugin name
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
	* Sets the name of the plugin
	*
	* @param string	$name Name of the plugin
	*/
	public function set_name($name)
	{
		$this->name = $name;
	}
}
