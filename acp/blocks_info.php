<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\acp;

/**
* DLS Web ACP module info
*/
class blocks_info
{
	public function module()
	{
		return [
			'filename' => '\dls\web\acp\blocks_module',
			'title'	   => 'ACP_DLS_BLOCKS_TITLE',
			'modes'	   => [
				'blocks' => ['title' => 'ACP_DLS_BLOCKS', 'auth' => 'ext_dls/web && acl_a_board', 'cat' => ['ACP_DLS_BLOCKS_TITLE'],
				],
			],
		];
	}
}
