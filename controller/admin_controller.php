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

/**
* DLS Web admin controller
*/
class admin_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $db_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \dls\web\core\block\data */
	protected $data;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param \phpbb\config\config			   $config	 Config object
	* @param \phpbb\config\db_text			   $db_text	 Config text object
	* @param \phpbb\db\driver\driver_interface $db		 Db object
	* @param \phpbb\language\language		   $language Language object
	* @param \phpbb\request\request			   $request	 Request object
	* @param \phpbb\template\template		   $template Template object
	* @param \dls\web\core\block\data		   $data	 Blocks data object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\config\db_text $db_text, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template, \dls\web\core\block\data $data)
	{
		$this->config	= $config;
		$this->db_text	= $db_text;
		$this->db = $db;
		$this->language = $language;
		$this->request	= $request;
		$this->template = $template;
		$this->data = $data;
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

		// Is the form submitted
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('dls/blocks'))
			{
				trigger_error('FORM_INVALID');
			}

			// If the form has been submitted, set all data and save it
			foreach ($this->data->get('acp') as $block_val)
			{
				$this->config->set($block_val['name'], $this->request->variable($block_val['name'], (bool) 0));
				$this->config->set($block_val['name'] . '_b', $this->request->variable($block_val['name'] . '_b', (int) 0));
			}

			$this->config->set('dls_mini_profile', $this->request->variable('dls_mini_profile', (bool) 0));

			// Show user confirmation of success and provide link back to the previous screen
			trigger_error($this->language->lang('ACP_DLS_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		// Set output vars for display in the template
		$this->assign_template_block_data($this->data->get());

		// Set template vars
		$this->template->assign_vars([
			'S_MINI_PROFILE' => $this->config['dls_mini_profile'],
			'U_ACTION' => $this->u_action,
		]);
	}

	/**
	* Assign template block data for blocks
	*
	* @param  array $block_data Blocks data is stored here
	* @return void
	*/
	protected function assign_template_block_data(array $block_data)
	{
		foreach ($block_data as $category => $data)
		{
			$l_category = $this->language->lang(strtoupper($category));
			$count_blocks = $this->data->count($data, 'cat', $category);

			// Set categories
			$this->template->assign_block_vars('type', ['category' => $l_category,]);

			// Add data to given categories
			foreach ($data as $block)
			{
				$block_options = $this->data->get_options(range(1, $count_blocks), $block['pos']);
				$count_position = $this->data->count($data, 'pos', $block['pos']);
				$this->template->assign_block_vars('type.block', [
					'name'	  => $block['name'],
					'b_name'  => $block['name'] . '_b',
					's_block' => $this->config[$block['name']],
					'l_block' => $this->language->lang(strtoupper($block['name'])),
					'd_block' => ($count_position > 1) ? true : false,
					's_block_options' => $block_options,
				]);
			}
		}
	}

	/**
	* Get options as forum_ids
	*
	* @param  int	$fid Current forum_id
	* @return array
	*/
	protected function get_ids($fid)
	{
		$sql = 'SELECT forum_id
				FROM ' . FORUMS_TABLE . '
				WHERE forum_type = ' . FORUM_POST;
		$result = $this->db->sql_query($sql, 3600);

		$forum_ids = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$forum_ids[] = (int) $row['forum_id'];
		}

		$this->db->sql_freeresult($result);

		// Merge default value 0 with forum_ids
		$forum_ids = array_merge([0], $forum_ids);

		return $this->data->get_options($forum_ids, (int) $this->config[$fid]);
	}

	/**
	* Display web settings
	*
	* @return void
	* @access public
	*/
	public function display_web()
	{
		// Add form key for form validation checks
		add_form_key('dls/web');

		$this->language->add_lang('acp_web', 'dls/web');

		$points = json_decode($this->db_text->get('dls_points'), true);

		// Is the form submitted
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('dls/web'))
			{
				trigger_error('FORM_INVALID');
			}

			// If the form has been submitted, set all data and save it
			$this->set_options();

			foreach ($points as $key => $val)
			{
				$points[$key] = $this->request->variable('pts_' . $val, (int) 0);
				$this->db_text->set('dls_points', json_encode($points));
			}

			// Show user confirmation of success and provide link back to the previous screen
			trigger_error($this->language->lang('ACP_DLS_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		// Set output vars for display in the template
		$this->assign_template_data($points);

		// Set template vars
		$this->template->assign_vars([
			'ZLAB_VERSION'	 => $this->config['dls_core_version'],
			'ZLAB_NAME'		 => $this->config['dls_core_name'],
			'DLS_NEWS_ID'	 => $this->get_ids('dls_news_fid'),
			'S_PAGINATION'	 => $this->config['dls_show_pagination'],
			'S_SHOW_NEWS'	 => $this->config['dls_show_news'],
			'MIN_TITLE_LENGTH'	 => $this->config['dls_title_length'],
			'MIN_CONTENT_LENGTH' => $this->config['dls_content_length'],
			'DLS_CORE_LIMIT'	 => $this->config['dls_limit'],
			'DLS_USER_LIMIT'	 => $this->config['dls_user_limit'],
			'U_ACTION'			 => $this->u_action,
		]);
	}

	/**
	* Set config options
	*
	* @return void
	*/
	protected function set_options()
	{
		$this->config->set('dls_news_fid', $this->request->variable('dls_news_fid', (int) 0));
		$this->config->set('dls_show_pagination', $this->request->variable('dls_show_pagination', (bool) 0));
		$this->config->set('dls_show_news', $this->request->variable('dls_show_news', (bool) 0));
		$this->config->set('dls_title_length', $this->request->variable('dls_title_length', (int) 0));
		$this->config->set('dls_content_length', $this->request->variable('dls_content_length', (int) 0));
		$this->config->set('dls_limit', $this->request->variable('dls_limit', (int) 0));
		$this->config->set('dls_user_limit', $this->request->variable('dls_user_limit', (int) 0));
	}

	/**
	* Assign template data
	*
	* @param  array $data Points data
	* @return void
	*/
	protected function assign_template_data(array $data)
	{
		foreach ($data as $val)
		{
			$this->template->assign_block_vars('points', [
				'name'	=> 'pts_' . $val,
				'value' => $val,
			]);
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
