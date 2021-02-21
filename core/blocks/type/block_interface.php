<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks\type;

/**
* DLS Web interface for blocks
*/
interface block_interface
{
	/**
	* Get block data
	*	['section' => '','ext_name' => '',]
	*
	* @return array
	*/
	public function get_block_data();

	/**
	* Set as special to access block in controller [Default should be true, if block load function is not empty]
	*
	* @param bool $set to true or false
	* @return void
	*/
	public function set_special(bool $set);

	/**
	* Check if block is allowed in controller [Default is false]
	*
	* @return bool
	*/
	public function is_load_special();

	/**
	* Set load to active [Default should be true, if block is not special & load function is not empty]
	*
	* @param bool $set to true or false
	* @return void
	*/
	public function set_active(bool $set);

	/**
	* Check if load method required [Default is true]
	*
	* @return bool
	*/
	public function is_load_active();

	/**
	* Load block
	*
	* @return void
	*/
	public function load();
}
