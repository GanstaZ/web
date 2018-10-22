<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\block;

/**
* DLS Web blocks base class
*/
abstract class base implements blocks_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/**
	* {@inheritdoc}
	*/
	public function set_config($config)
	{
		$this->config = $config;
	}
}
