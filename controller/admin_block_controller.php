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

use Symfony\Component\DependencyInjection\ContainerInterface as container;
use phpbb\db\driver\driver_interface as driver;
use phpbb\language\language;
use phpbb\request\request;
use dls\web\core\blocks\manager;
use dls\web\core\helper;

/**
* DLS Web admin block controller
*/
class admin_block_controller
{
	/** @var container */
	protected $container;

	/** @var driver */
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
	* @param container $container A container
	* @param driver	   $db		  Database object
	* @param language  $language  Language object
	* @param request   $request	  Request object
	* @param manager   $manager	  Data manager object
	* @param helper	   $helper	  Data helper object
	*/
	public function __construct(container $container, driver $db, language $language, request $request, manager $manager, helper $helper)
	{
		$this->container = $container;
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
			$count[$row['cat_name']]['block']++;
			$count[$row['cat_name']]['position'][(int) $row['position']]++;
			if ($count[$row['cat_name']]['position'][(int) $row['position']] > 1 && !$row['active'])
			{
				$count[$row['cat_name']]['position'][(int) $row['position']]--;
			}

			$data_ary[$row['block_name']] = $row['ext_name'];
			$rowset[$row['cat_name']][] = [
				'cat_name'	 => $row['cat_name'],
				'block_name' => $row['block_name'],
				'ext_name'	 => $row['ext_name'],
				'active'	 => $row['active'],
				'position'	 => (int) $row['position'],
			];
		}
		$this->db->sql_freeresult($result);

		// Run check for available/unavailable blocks
		$this->check($data_ary, $count);

		if ($s_status = $this->get_status())
		{
			$u_update = $s_status === 'add' ? array_column($this->status('add'), 'block_name') : $this->status($s_status);
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

			$this->container->get('cache')->purge();

			// Show user confirmation of success and provide link back to the previous screen
			trigger_error($this->language->lang('ACP_DLS_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		// Set output vars for display in the template
		$this->assign_template_block_data($rowset, $count);

		unset($data_ary, $rowset, $count);

		// Set template vars
		$this->helper->assign('vars', [
			'S_UPDATE' => $s_status,
			'U_UPDATE' => $u_update,
			'U_ACTION' => $this->u_action,
		]);
	}

	/**
	* Update data
	*
	* @param array $data_ary Array of blocks data
	* @return void
	*/
	public function update_data(array $data_ary): void
	{
		foreach ($data_ary as $block_name => $ext_name)
		{
			$block_data = [
				'active'   => $this->request->variable($block_name, (int) 0),
				'position' => $this->request->variable($block_name . '_' . $ext_name, (int) 0),
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

		// Add new blocks
		if ($add_blocks = $this->status('add'))
		{
			$this->db->sql_multi_insert($this->manager->blocks_data(), $add_blocks);
		}
	}

	/**
	* Assign template data for blocks
	*
	* @param array $rowset Block data is stored here
	* @param array $count  Array of counted data [quantity of blocks and positions]
	* @return void
	*/
	protected function assign_template_block_data(array $rowset, array $count): void
	{
		foreach ($rowset as $category => $data)
		{
			$count_blocks = $count[$category]['block'];

			// Set categories
			$this->helper->assign('block_vars', 'category', ['cat_name' => strtoupper($category),]);

			// Add data to given categories
			foreach ($data as $block)
			{
				$block_options = $this->helper->get_options(range(1, $count_blocks), $block['position']);
				$count_position = $count[$category]['position'][$block['position']];

				$this->helper->assign('block_vars', 'category.block', [
					'name'		=> $block['block_name'],
					'position'	=> $block['block_name'] . '_' . $block['ext_name'],
					'active'	=> $block['active'],
					'lang'		=> strtoupper($block['block_name']),
					'duplicate' => $count_position > 1 ?? false,
					's_block_options' => $block_options,
				]);
			}
		}
	}

	/**
	* Check conditioning
	*
	* @param array $block_data
	* @param array $count
	* @return void
	*/
	public function check(array $block_data, array $count): void
	{
		$add_blocks = [];

		/**
		* Event to add blocks
		*
		* @event dls.web.admin_add_blocks
		* @var array add_blocks Contains blocks data
		* @since 2.4.0-RC1
		*/
		$vars = ['add_blocks'];
		extract($this->container->get('dispatcher')->trigger_event('dls.web.admin_add_blocks', compact($vars)));

		// Check for new blocks
		$this->prepare(array_diff_key($add_blocks, array_flip(array_keys($block_data))), $count);

		// Check for unavailable blocks
		foreach ($block_data as $service => $ext_name)
		{
			if (!$this->is_available(['block_name' => $service, 'ext_name' => $ext_name]))
			{
				// Set our block/service as unavailable
				$this->status['purge'][] = $service;
			}
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
			if (!$this->is_valid($data))
			{
				continue;
			}

			$position = 1;
			if ($count[$data['cat_name']])
			{
				$position = end(array_keys($count[$data['cat_name']]['position']));
				$count[$data['cat_name']]['position'][] = $position++;
			}
			else if (in_array($data['cat_name'], array_column($this->status['add'], 'cat_name')))
			{
				$position++;
			}

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
	* Is valid data
	*
	* @param array $row
	* @return bool
	*/
	protected function is_valid(array $row): bool
	{
		return $this->manager->is_valid_name($row) && $this->is_available($row);
	}

	/**
	* Is service available
	*
	* @param array $row
	* @return bool
	*/
	protected function is_available(array $row): bool
	{
		return $this->container->has($this->manager->get_service($row['block_name'], $row['ext_name']));
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
	* @return string
	*/
	public function get_status(): ?string
	{
		if (!$this->status)
		{
			return null;
		}
		else if ($this->status('add'))
		{
			$status = 'add';
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
