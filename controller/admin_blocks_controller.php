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

use Symfony\Component\DependencyInjection\ContainerInterface;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\request\request;
use dls\web\core\blocks\manager;
use dls\web\core\helper;

/**
* DLS Web admin blocks controller
*/
class admin_blocks_controller
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \dls\web\core\blocks\manager */
	protected $manager;

	/** @var \dls\web\core\helper */
	protected $helper;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param ContainerInterface $container A container
	* @param \phpbb\db\driver\driver_interface $db Db object
	* @param \phpbb\language\language $language Language object
	* @param \phpbb\request\request $request Request object
	* @param \dls\web\core\blocks\manager $manager Data manager object
	* @param \dls\web\core\helper $helper Data helper object
	* @param \phpbb\cache\service $cache A cache instance or null
	*/
	public function __construct(ContainerInterface $container, driver_interface $db, language $language, request $request, manager $manager, helper $helper, \phpbb\cache\service $cache = null)
	{
		$this->cache = $cache;
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
	* @access public
	*/
	public function display_blocks()
	{
		// Add form key for form validation checks
		add_form_key('dls/blocks');

		$this->language->add_lang('acp_blocks', 'dls/web');

		// Get all blocks
		$sql = 'SELECT *
				FROM ' . $this->manager->blocks_data() . '
				ORDER BY block_id';
		$result = $this->db->sql_query($sql);

		$data_ary = $rowset = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$data_ary[] = $row['block_name'];
			// Check for unavailable data
			$this->manager->status_unavailable($this->container, $row);

			$rowset[$row['cat_name']][$row['block_name']] = [
				'cat_name'	 => $row['cat_name'],
				'block_name' => $row['block_name'],
				'active'	 => $row['active'],
				'position'	 => (int) $row['position'],
			];
		}
		$this->db->sql_freeresult($result);

		// Check for new blocks
		$new_block = $this->manager->status_available($rowset);

		$u_update = 'Nothing to update';
		if ($status = $this->manager->check_status())
		{
			$u_update = implode($this->language->lang('COMMA_SEPARATOR'), $this->manager->info($status));
		}

		echo '<pre>' . print_r($this->manager->info($status), true) . '</pre>';

		// Is the form submitted
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('dls/blocks'))
			{
				trigger_error('FORM_INVALID');
			}

			// If the form has been submitted, set all data and save it
			$this->update_data($data_ary, $new_block);

			// Show user confirmation of success and provide link back to the previous screen
			trigger_error($this->language->lang('ACP_DLS_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		// Set output vars for display in the template
		$this->assign_template_block_data($rowset);

		// Set template vars
		$this->helper->assign('vars', [
			'S_UPDATE' => ($status) ? true : false,
			'U_UPDATE' => $this->language->lang(strtoupper($status) . '_BLOCKS', $u_update),
			'U_ACTION' => $this->u_action,
		]);
	}

	/**
	* Update data
	*
	* @param array $data_ary Array of block names
	* @param array $new_block Blocks data
	* @return null
	*/
	public function update_data($data_ary, $new_block)
	{
		foreach ($data_ary as $block_name)
		{
			$block_data = [
				'active'   => $this->request->variable($block_name, (int) 0),
				'position' => $this->request->variable($block_name . '_b', (int) 0),
			];

			$this->db->sql_query('UPDATE ' . $this->manager->blocks_data() . ' SET ' . $this->db->sql_build_array('UPDATE', $block_data) . "
				WHERE block_name = '" . $this->db->sql_escape($block_name) . "'"
			);

			if (in_array($block_name, $this->manager->info('remove')))
			{
				$this->db->sql_query('DELETE FROM ' . $this->manager->blocks_data() . "
				WHERE block_name = '" . $this->db->sql_escape($block_name) . "'");
			}
		}

		if ($new_block)
		{
			$this->db->sql_multi_insert($this->manager->blocks_data(), $new_block);
		}

		$this->cache->purge();
	}

	/**
	* Assign template block data for blocks
	*
	* @param array $rowset Blocks data is stored here
	* @return null
	*/
	protected function assign_template_block_data(array $rowset)
	{
		foreach ($rowset as $category => $data)
		{
			$l_category = $this->language->lang(strtoupper($category));
			$count_blocks = $this->helper->count($data, 'cat_name', $category);

			// Set categories
			$this->helper->assign('block_vars', 'category', ['cat_name' => $l_category,]);

			// Add data to given categories
			foreach ($data as $block)
			{
				$block_options = $this->helper->get_options(range(0, $count_blocks), $block['position']);
				$count_position = $this->helper->count($data, 'position', $block['position']);
				//print_r($count_position);
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
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return void
	* @access public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
