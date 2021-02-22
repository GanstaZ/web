<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\controller;

use Symfony\Component\DependencyInjection\ContainerInterface as container;
use phpbb\db\driver\driver_interface as driver;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use dls\web\core\blocks\manager;

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

	/** @var template */
	protected $template;

	/** @var manager */
	protected $manager;

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
	* @param template  $template  Template object
	* @param manager   $manager	  Data manager object
	*/
	public function __construct(
		container $container,
		driver $db,
		language $language,
		request $request,
		template $template,
		manager $manager
	)
	{
		$this->container = $container;
		$this->db		 = $db;
		$this->language	 = $language;
		$this->request	 = $request;
		$this->manager	 = $manager;
		$this->template	 = $template;
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

		/**
		* Add language
		*
		* @event dls.web.admin_block_add_language
		* @since 2.4.0-dev
		*/
		$this->container->get('dispatcher')->dispatch('dls.web.admin_block_add_language');

		// Get all blocks
		$sql = 'SELECT *
				FROM ' . $this->manager->blocks_data() . '
				ORDER BY id';
		$result = $this->db->sql_query($sql);

		$rowset = $count = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			if (!isset($count[$row['section']]['block']))
			{
				$count[$row['section']]['block'] = 0;
			}
			$count[$row['section']]['block']++;

			if (!isset($count[$row['section']]['position'][(int) $row['position']]))
			{
				$count[$row['section']]['position'][(int) $row['position']] = 0;
			}

			if ($row['active'])
			{
				$count[$row['section']]['position'][(int) $row['position']]++;
			}

			$rowset[$row['section']][] = [
				'section'  => $row['section'],
				'name'	   => $row['name'],
				'ext_name' => $row['ext_name'],
				'active'   => (bool) $row['active'],
				'position' => (int) $row['position'],
			];
		}
		$this->db->sql_freeresult($result);

		$data_ary = array_reduce($rowset, 'array_merge', []);

		// Run check for available/unavailable blocks
		$this->check($data_ary, $count);

		// Assign error message/s into template, if there are any
		if ($errors = $this->manager->get_error_log())
		{
			foreach ($errors as $error_service => $error)
			{
				$this->template->assign_block_vars('error', [
					'name'	   => $error_service ?? false,
					'ext_name' => $error['ext_name'] ?? false,
					'service'  => $error['service'] ?? false,
					'section'  => $error['section'] ?? false,
					'error'	   => $error['error'] ?? false,
				]);
			}
		}

		$data_ary = array_merge($data_ary, $this->status('add'));

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

		// Remove special section from section options
		$this->manager->remove_section(0);

		// Set output vars for display in the template
		$this->template->assign_block_vars_array('install', $this->status('add'));
		$this->assign_template_block_data($rowset, $count);

		unset($data_ary, $rowset, $count);

		// Set template vars
		$this->template->assign_vars([
			'U_ADD'	   => count($this->status('add')),
			'U_ERROR'  => count($errors),
			'U_PURGE'  => $this->status('purge') ?? false,
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
		foreach ($data_ary as $data)
		{
			$block = $this->request->variable($data['name'], (int) 0);

			$block_data = [
				'active'   => $this->request->variable($data['name'] . '_active', (int) 0),
				'position' => $this->request->variable($data['name'] . '_position', (int) 0),
				'section'  => $this->request->variable($data['name'] . '_section', (string) ''),
			];

			if ($block)
			{
				// Update selected/requested block data
				$this->db->sql_query('UPDATE ' . $this->manager->blocks_data() . ' SET ' .
					$this->db->sql_build_array('UPDATE', $block_data) . "
					WHERE name = '" . $this->db->sql_escape($data['name']) . "'"
				);
			}

			$new_block = $this->request->variable($data['name'] . '_new', (int) 0);

			// Add new block/service data into db.
			if ($new_block && in_array($data['name'], array_column($this->status('add'), 'name')))
			{
				$this->db->sql_query('INSERT INTO ' . $this->manager->blocks_data() . ' ' .
					$this->db->sql_build_array('INSERT', $data)
				);
			}

			// Purge removed block/service data from db. No confirm_box is needed! It is just a cleanup process :)
			if (in_array($data['name'], $this->status('purge')))
			{
				$this->db->sql_query('DELETE FROM ' . $this->manager->blocks_data() . "
					WHERE name = '" . $this->db->sql_escape($data['name']) . "'"
				);
			}
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
		foreach ($rowset as $section => $data)
		{
			// Set categories
			$this->template->assign_block_vars('section', [
				'section'  => strtoupper($section),
				'in_count' => count($data),
			]);

			// Add data to given categories
			foreach ($data as $block)
			{
				$this->template->assign_block_vars('section.block', [
					'name'		  => $block['name'],
					'active'	  => $block['name'] . '_active',
					'position'	  => $block['name'] . '_position',
					'section'	  => $block['name'] . '_section',
					's_section'	  => $block['section'],
					'S_SECTIONS'  => $this->manager->get_sections(),
					's_activate'  => $block['active'],
					's_duplicate' => ($count[$section]['position'][$block['position']] > 1) && $block['active'],
					's_options'	  => $count[$section]['block'],
					's_current'	  => $block['position'],
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
		// Check for new blocks & prepare for installation
		$this->prepare($this->manager->check_for_new_blocks($block_data, $this->container), $count);

		// Check for unavailable blocks & prepare for purge
		foreach ($block_data as $service)
		{
			if (!$this->container->has($this->manager->get_service_name($service['name'], $service['ext_name'])))
			{
				// Set our block/service as unavailable
				$this->status['purge'][] = $service['name'];
			}
		}
	}

	/**
	* Prepare data for installation
	*
	* @param array new_blocks
	* @param array $count
	* @return void
	*/
	protected function prepare(array $new_blocks, array $count): void
	{
		if (!$new_blocks)
		{
			return;
		}

		$this->status['add'] = [];
		foreach ($new_blocks as $data)
		{
			$position = 1;
			if (array_key_exists($data['section'], $count))
			{
				$position = end(array_keys($count[$data['section']]['position']));
				$count[$data['section']]['position'][] = $position++;
			}
			else if (in_array($data['section'], array_column($this->status['add'], 'section')))
			{
				$position++;
			}

			$this->status['add'][] = [
				'name'	   => $data['name'],
				'ext_name' => $data['ext_name'],
				'position' => $position,
				'active'   => 0,
				'section'  => $data['section'],
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
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return self
	*/
	public function set_page_url(string $u_action): self
	{
		$this->u_action = $u_action;

		return $this;
	}
}
