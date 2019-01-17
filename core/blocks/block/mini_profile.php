<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks\block;

/**
* DLS Web Mini Profile block
*/
class mini_profile implements block_interface
{
	/**
	* Constructor
	*
	*/
	public function __construct()
	{
	}

	/**
	* {@inheritdoc}
	*/
	public function get_data(): array
	{
		return [
			'block_name' => 'dls_mini_profile',
			'cat_name' => 'right',
			'ext_name' => 'dls_web',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
	}
}
