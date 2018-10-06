<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dls.org/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\block;

/**
* DLS Web interface for blocks
*/
interface blocks_interface
{
	/**
	* Set config
	*
	* @return void
	*/
	public function set_config($config);

	/**
	* Load block
	*
	* @return mixed
	*/
	public function load();

	/**
	* Get block data
	*
	* @return array Block data, must have keys block_name, cat_name & vendor.
	*		 ['block_name' => '', 'cat_name' => '', 'vendor' => '']
	*/
	public function get_data();
}
