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

use phpbb\config\config;
use phpbb\config\db_text;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use dls\web\core\helper;

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

	/** @var \dls\web\core\helper */
	protected $helper;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param \phpbb\config\config $config Config object
	* @param \phpbb\config\db_text $db_text Config text object
	* @param \phpbb\db\driver\driver_interface $db Db object
	* @param \phpbb\language\language $language Language object
	* @param \phpbb\request\request $request Request object
	* @param \phpbb\template\template $template Template object
	* @param \dls\web\core\helper $helper Data helper object
	*/
	public function __construct(config $config, db_text $db_text, driver_interface $db, language $language, request $request, template $template, helper $helper)
	{
		$this->config = $config;
		$this->db_text = $db_text;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->helper = $helper;
	}

	/**
	* Get options as forum_ids
	*
	* @param int $fid Current forum_id
	* @return string
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

		return $this->helper->get_options($forum_ids, (int) $fid);
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
			'DLS_NEWS_ID'	 => $this->get_ids($this->config['dls_news_fid']),
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
	* @param array $data Points data
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
