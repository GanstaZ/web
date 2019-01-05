<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks\block;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\user;
use dls\web\core\helper;
use phpbb\event\dispatcher;

/**
* DLS Web Who's Online block
*/
class whos_online implements block_interface
{
	/** @var auth */
	protected $auth;

	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var language */
	protected $language;

	/** @var user */
	protected $user;

	/** @var helper */
	protected $helper;

	/** @var dispatcher */
	protected $dispatcher;

	/** @var phpBB root path */
	protected $root_path;

	/** @var file extension */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param auth             $auth       Auth object
	* @param config           $config     Config object
	* @param driver_interface $db         Database object
	* @param language         $language   Language object
	* @param user             $user       User object
	* @param helper           $helper     Data helper object
	* @param dispatcher       $dispatcher Dispatcher object
	*/
	public function __construct(auth $auth, config $config, driver_interface $db, language $language, user $user, helper $helper, dispatcher $dispatcher)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->user = $user;
		$this->helper = $helper;
		$this->dispatcher = $dispatcher;
		$this->root_path = $this->helper->get('root_path');
		$this->php_ext = $this->helper->get('php_ext');
	}

	/**
	* {@inheritdoc}
	*/
	public function get_data(): array
	{
		return [
			'block_name' => 'dls_whos_online',
			'cat_name' => 'bottom',
			'ext_name' => 'dls_web',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
		$total_posts = (int) $this->config['num_posts'];
		$total_topics = (int) $this->config['num_topics'];
		$total_users = (int) $this->config['num_users'];

		$boarddays = (time() - $this->config['board_startdate']) / 86400;

		$posts_per_day = $total_posts / $boarddays;
		$topics_per_day = $total_topics / $boarddays;
		$users_per_day = $total_users / $boarddays;

		// Generate birthday list if required...
		$show_birthdays = ($this->config['load_birthdays'] && $this->config['allow_birthdays'] && $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'));

		if ($show_birthdays)
		{
			$this->birthdays();
		}

		$this->helper->assign('vars', [
			'TOTAL_POSTS'  => $this->language->lang('TOTAL_POSTS_COUNT', $total_posts),
			'TOTAL_TOPICS' => $this->language->lang('TOTAL_TOPICS', $total_topics),
			'TOTAL_USERS'  => $this->language->lang('TOTAL_USERS', $total_users),
			'NEWEST_USER'  => $this->language->lang('NEWEST_USER', get_username_string('full', (int) $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour'])),

			'LEGEND' => $this->legend(),

			'POSTS_PER_DAY'  => $this->language->lang('T_POST_DAY', (float) $posts_per_day),
			'TOPICS_PER_DAY' => $this->language->lang('T_TOPICS_DAY', (float) $topics_per_day),
			'USERS_PER_DAY'  => $this->language->lang('T_USERS_DAY', (float) $users_per_day),
			'S_DISPLAY_BIRTHDAY_LIST' => $show_birthdays,
		]);

		/**
		* Event dls.web.main_blocks_after
		*
		* You can use this event to load function files and initiate objects
		*
		* @event dls.web.main_blocks_after
		* @since 2.3.5
		*/
		$this->dispatcher->dispatch('dls.web.main_blocks_after');
	}

	/**
	* Birthdays
	*
	* @return void
	*/
	protected function birthdays(): void
	{
		$birthdays = [];

		$time = $this->user->create_datetime();
		$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

		// Display birthdays of 29th february on 28th february in non-leap-years
		$leap_year_birthdays = '';
		if ($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L'))
		{
			$leap_year_birthdays = " OR u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
		}

		$sql = $this->db->sql_build_query('SELECT', [
			'SELECT' => 'u.user_id, u.username, u.user_colour, u.user_birthday',
			'FROM' => [
				USERS_TABLE => 'u',
			],
			'LEFT_JOIN' => [
				[
					'FROM' => [BANLIST_TABLE => 'b'],
					'ON' => 'u.user_id = b.ban_userid',
				],
			],
			'WHERE' => "(b.ban_id IS NULL OR b.ban_exclude = 1)
				AND (u.user_birthday LIKE '" . $this->db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%' $leap_year_birthdays)
				AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')',
		]);

		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		foreach ($rows as $row)
		{
			$birthday_username = get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']);
			$birthday_year = (int) substr($row['user_birthday'], -4);
			$birthday_age = ($birthday_year) ? max(0, $now['year'] - $birthday_year) : '';

			$birthdays[] = [
				'USERNAME' => $birthday_username,
				'AGE' => $birthday_age,
			];
		}

		$this->helper->assign('block_vars_array', 'birthdays', $birthdays);
	}

	/**
	* Legend
	*
	* @return string
	*/
	protected function legend(): string
	{
		$order_legend = ($this->config['legend_sort_groupname']) ? 'group_name' : 'group_legend';

		// Grab group details for legend display
		$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type, g.group_legend
			FROM ' . GROUPS_TABLE . ' g
			LEFT JOIN ' . USER_GROUP_TABLE . ' ug
				ON (
					g.group_id = ug.group_id
					AND ug.user_id = ' . (int) $this->user->data['user_id'] . '
					AND ug.user_pending = 0
				)
			WHERE g.group_legend > 0
				AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . (int) $this->user->data['user_id'] . ')
			ORDER BY g.' . $order_legend;

		if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
				FROM ' . GROUPS_TABLE . '
				WHERE group_legend > 0
				ORDER BY ' . $order_legend;
		}
		$result = $this->db->sql_query($sql);

		$legend = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
			$group_name = $this->helper->get_name($row['group_name']);
			$group_link = append_sid("{$this->helper->get('root_path')}memberlist.{$this->helper->get('php_ext')}", "mode=group&amp;g={$row['group_id']}");

			if ($this->not_authed($row))
			{
				$legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
			}

			$legend[] = '<a' . $colour_text . ' href="' . $group_link . '">' . $group_name . '</a>';
		}
		$this->db->sql_freeresult($result);

		return implode($this->language->lang('COMMA_SEPARATOR'), $legend);
	}

	/**
	* Is visitor a bot or does he/she have permissions
	*
	* @param  array $row Groups data
	* @return bool
	*/
	protected function not_authed($row): bool
	{
		return $row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile'));
	}
}
