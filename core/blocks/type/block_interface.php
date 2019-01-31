<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dls.org/
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
	* Is load method required [Default is true]
	*
	* @return bool
	*/
	public function is_load_active();

	/**
	* Set load [Default should be true]
	*
	* @param bool $set to true or false
	* @return void
	*/
	public function loading(bool $set);

	/**
	* Load block
	*
	* @return void
	*/
	public function load();
}
