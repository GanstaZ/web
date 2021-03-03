<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\acp;

/**
* DLS Web ACP module info
*/
class page_info
{
	public function module()
	{
		return [
			'filename' => '\dls\web\acp\page_module',
			'title'	   => 'ACP_DLS_PAGE_TITLE',
			'modes'	   => [
				'page' => ['title' => 'ACP_DLS_PAGE', 'auth' => 'ext_dls/web && acl_a_board', 'cat' => ['ACP_DLS_PAGE_TITLE'],
				],
			],
		];
	}
}
