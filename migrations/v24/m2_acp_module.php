<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\migrations\v24;

class m2_acp_module extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	static public function depends_on()
	{
		return ['\dls\web\migrations\v24\m1_dls_main'];
	}

	/**
	* Add the initial data in the database
	*
	* @return array Array of table data
	* @access public
	*/
	public function update_data()
	{
		return [
			// Add a parent module (ACP_DLS_WEB_TITLE) to the Extensions tab (ACP_CAT_DOT_MODS)
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_DLS_WEB_TITLE'
			]],
			// Add our main_modules to the parent module (ACP_DLS_WEB_TITLE)
			['module.add', [
				'acp',
				'ACP_DLS_WEB_TITLE',
				[
					'module_basename' => '\dls\web\acp\web_module', 'modes' => ['settings'],
				],
			]],
			['module.add', [
				'acp',
				'ACP_DLS_WEB_TITLE',
				[
					'module_basename' => '\dls\web\acp\blocks_module', 'modes' => ['blocks'],
				],
			]],
			['module.add', [
				'acp',
				'ACP_DLS_WEB_TITLE',
				[
					'module_basename' => '\dls\web\acp\page_module', 'modes' => ['page'],
				],
			]],
			['module.add', [
				'acp',
				'ACP_DLS_WEB_TITLE',
				[
					'module_basename' => '\dls\web\acp\plugin_module', 'modes' => ['plugin'],
				],
			]],
		];
	}
}
