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

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;

/**
* DLS Web admin controller
*/
class admin_controller
{
	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param config			  $config	Config object
	* @param driver_interface $db		Database object
	* @param language		  $language Language object
	* @param request		  $request	Request object
	* @param template		  $template Template object
	*/
	public function __construct(config $config, driver_interface $db, language $language, request $request, template $template)
	{
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
	}

	/**
	* Get options as forum_ids (Will be replaced)
	*
	* @return array
	*/
	protected function get_ids(): array
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

		return $forum_ids ?? [];
	}

	/**
	* Display web settings
	*
	* @return void
	*/
	public function display_web(): void
	{
		// Add form key for form validation checks
		add_form_key('dls/web');

		$this->language->add_lang('acp_web', 'dls/web');

		// Is the form submitted
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('dls/web'))
			{
				trigger_error('FORM_INVALID');
			}

			// If the form has been submitted, set all data and save it
			$this->set_options();

			// Show user confirmation of success and provide link back to the previous screen
			trigger_error($this->language->lang('ACP_DLS_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		// Set template vars
		$this->template->assign_vars([
			'DLS_VERSION'		 => $this->config['dls_core_version'],
			'DLS_NEWS_ID'		 => $this->get_ids(),
			'S_NEWS_CURRENT'	 => $this->config['dls_news_fid'],
			'S_PAGINATION'		 => $this->config['dls_pagination'],
			'DLS_LIMIT'			 => $this->config['dls_limit'],
			'DLS_USER_LIMIT'	 => $this->config['dls_user_limit'],
			'MIN_TITLE_LENGTH'	 => $this->config['dls_title_length'],
			'MIN_CONTENT_LENGTH' => $this->config['dls_content_length'],
			'S_BLOCKS'			 => $this->config['dls_blocks'],
			'S_SPECIAL'			 => $this->config['dls_special'],
			'S_RIGHT'			 => $this->config['dls_right'],
			'S_LEFT'			 => $this->config['dls_left'],
			'S_MIDDLE'			 => $this->config['dls_middle'],
			'S_TOP'				 => $this->config['dls_top'],
			'S_BOTTOM'			 => $this->config['dls_bottom'],
			'U_ACTION'			 => $this->u_action,
		]);
	}

	/**
	* Set config options
	*
	* @return void
	*/
	protected function set_options(): void
	{
		$this->config->set('dls_news_fid', $this->request->variable('dls_news_fid', (int) 0));
		$this->config->set('dls_pagination', $this->request->variable('dls_pagination', (bool) 0));
		$this->config->set('dls_title_length', $this->request->variable('dls_title_length', (int) 0));
		$this->config->set('dls_content_length', $this->request->variable('dls_content_length', (int) 0));
		$this->config->set('dls_limit', $this->request->variable('dls_limit', (int) 0));
		$this->config->set('dls_user_limit', $this->request->variable('dls_user_limit', (int) 0));
		$this->config->set('dls_blocks', $this->request->variable('dls_blocks', (bool) 0));
		$this->config->set('dls_special', $this->request->variable('dls_special', (bool) 0));
		$this->config->set('dls_right', $this->request->variable('dls_right', (bool) 0));
		$this->config->set('dls_left', $this->request->variable('dls_left', (bool) 0));
		$this->config->set('dls_middle', $this->request->variable('dls_middle', (bool) 0));
		$this->config->set('dls_top', $this->request->variable('dls_top', (bool) 0));
		$this->config->set('dls_bottom', $this->request->variable('dls_bottom', (bool) 0));
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return void
	*/
	public function set_page_url($u_action): void
	{
		$this->u_action = $u_action;
	}
}
