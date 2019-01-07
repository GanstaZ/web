<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\controller;

use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\request\request;
use dls\web\core\blocks\manager;
use dls\web\core\helper;
use phpbb\cache\service as cache;

/**
* DLS Web admin block controller
*/
class admin_block_controller
{
	/** @var cache */
	protected $cache;

	/** @var driver_interface */
	protected $db;

	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var manager */
	protected $manager;

	/** @var helper */
	protected $helper;

	/** @var array Contains info about current status */
	protected $status;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param driver_interface $db		Database object
	* @param language		  $language Language object
	* @param request		  $request	Request object
	* @param manager		  $manager	Data manager object
	* @param helper			  $helper	Data helper object
	* @param cache			  $cache	A cache instance or null
	*/
	public function __construct(driver_interface $db, language $language, request $request, manager $manager, helper $helper, cache $cache = null)
	{
		$this->cache = $cache;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->manager = $manager;
		$this->helper = $helper;
	}

	/**
	* Display blocks
	*
	* @return void
	*/
	public function display_blocks(): void
	{
		// Add form key for form validation checks
		add_form_key('dls/blocks');

		$this->language->add_lang('acp_blocks', 'dls/web');

		// Get all blocks
		$sql = 'SELECT *
				FROM ' . $this->manager->blocks_data() . '
				ORDER BY block_id';
		$result = $this->db->sql_query($sql);

		$data_ary = $rowset = $count = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			// Check for unavailable data
			$this->check($row['block_name']);

			$count[$row['cat_name']]['block']++;
			$count[$row['cat_name']]['position'][(int) $row['position']]++;
			if ($count[$row['cat_name']]['position'][(int) $row['position']] > 1 && !$row['active'])
			{
				$count[$row['cat_name']]['position'][(int) $row['position']]--;
			}

			$data_ary[] = $row['block_name'];
			$rowset[$row['cat_name']][$row['block_name']] = [
				'cat_name'	 => $row['cat_name'],
				'block_name' => $row['block_name'],
				'active'	 => $row['active'],
				'position'	 => (int) $row['position'],
			];
		}
		$this->db->sql_freeresult($result);

		// Check for new blocks
		$this->check($data_ary, $count);

		if ($s_status = $this->get_status())
		{
			$u_update = implode($this->language->lang('COMMA_SEPARATOR'), $this->status($s_status));
		}

		// Is the form submitted
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('dls/blocks'))
			{
				trigger_error('FORM_INVALID');
			}

			// If the form has been submitted, set all data and save it
			$this->update_data($data_ary);

			// Show user confirmation of success and provide link back to the previous screen
			trigger_error($this->language->lang('ACP_DLS_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		// Set output vars for display in the template
		$this->assign_template_block_data($rowset, $count);

		unset($data_ary, $rowset, $count);

		// Set template vars
		$this->helper->assign('vars', [
			'S_UPDATE' => ($s_status) ? true : false,
			'U_UPDATE' => $this->language->lang(strtoupper("{$s_status}_BLOCKS"), $u_update),
			'U_ACTION' => $this->u_action,
		]);
	}

	/**
	* Update data
	*
	* @param array $data_ary Array of block names
	* @return void
	*/
	public function update_data(array $data_ary): void
	{
		foreach ($data_ary as $block_name)
		{
			$block_data = [
				'active'   => $this->request->variable($block_name, (int) 0),
				'position' => $this->request->variable($block_name . '_b', (int) 0),
			];

			// Update selected/requested block data
			$this->db->sql_query('UPDATE ' . $this->manager->blocks_data() . ' SET ' . $this->db->sql_build_array('UPDATE', $block_data) . "
				WHERE block_name = '" . $this->db->sql_escape($block_name) . "'"
			);

			// Purge removed block/service data from db. No confirm_box is needed! It is just a cleanup process :)
			if (in_array($block_name, $this->status('purge')))
			{
				$this->db->sql_query('DELETE FROM ' . $this->manager->blocks_data() . "
				WHERE block_name = '" . $this->db->sql_escape($block_name) . "'");
			}
		}

		// Add new block
		if ($add_new_block = $this->status('add'))
		{
			$this->db->sql_multi_insert($this->manager->blocks_data(), $add_new_block);
		}

		$this->cache->purge();
	}

	/**
	* Assign template block data for blocks
	*
	* @param array $rowset Block data is stored here
	* @param array $count  Array of counted data [quantity of blocks and positions]
	* @return void
	*/
	protected function assign_template_block_data(array $rowset, array $count): void
	{
		foreach ($rowset as $category => $data)
		{
			$l_category = $this->language->lang(strtoupper($category));
			$count_blocks = $count[$category]['block'];

			// Set categories
			$this->helper->assign('block_vars', 'category', ['cat_name' => $l_category,]);

			// Add data to given categories
			foreach ($data as $block)
			{
				$block_options = $this->helper->get_options(range(1, $count_blocks), $block['position']);
				$count_position = $count[$category]['position'][$block['position']];

				$this->helper->assign('block_vars', 'category.block', [
					'name' => $block['block_name'],
					'position' => $block['block_name'] . '_b',
					'active' => $block['active'],
					'lang' => $this->language->lang(strtoupper($block['block_name'])),
					'duplicate' => ($count_position > 1) ? true : false,
					's_block_options' => $block_options,
				]);
			}
		}
	}

	/**
	* Check conditioning
	*
	* @param string|array $block_data
	* @param null|array $count
	* @return void
	*/
	public function check($block_data, $count = null): void
	{
		// Check for new blocks
		if (is_array($block_data))
		{
			$this->prepare(array_diff_key($this->manager->get(), array_flip($block_data)), $count);
		}
		else if (is_string($block_data) && !$this->manager->get($block_data))
		{
			// Set our block/service as unavailable
			$this->status['purge'][] = $block_data;
		}
	}

	/**
	* Prepare data for installation
	*
	* @param array $block_data
	* @param array $count
	* @return void
	*/
	protected function prepare(array $block_data, array $count): void
	{
		foreach ($block_data as $data)
		{
			$position = 1;
			if ($count[$data['cat_name']])
			{
				$position = end(array_keys($count[$data['cat_name']]['position']));
				$count[$data['cat_name']]['position'][] = $position++;
			}

			$this->status['update'][] = $data['block_name'];
			$this->status['add'][] = [
				'block_name' => $data['block_name'],
				'ext_name'	 => $data['ext_name'],
				'position'	 => $position,
				'active'	 => 0,
				'cat_name'	 => $data['cat_name'],
			];
		}
	}

	/**
	* Get status
	*
	* @param string $status
	* @return array
	*/
	public function status(string $status): array
	{
		return $this->status[$status] ?? [];
	}

	/**
	* Check for update/purge status
	*
	* @return string $status
	*/
	public function get_status(): ?string
	{
		if (!$this->status)
		{
			return null;
		}
		else if ($this->status('update'))
		{
			$status = 'update';
		}
		else if ($this->status('purge'))
		{
			$status = 'purge';
		}

		return $status;
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return void
	*/
	public function set_page_url(string $u_action): void
	{
		$this->u_action = $u_action;
	}
}
