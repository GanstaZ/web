<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks\type;

use phpbb\auth\auth;
use phpbb\user;
use phpbb\language\language;

/**
* DLS Web Who's Online block
*/
class whos_online extends base
{
	/** @var auth */
	protected $auth;

	/** @var user */
	protected $user;

	/** @var language */
	protected $language;

	/**
	* Constructor
	*
	* @param auth	  $auth		Auth object
	* @param user	  $user		User object
	* @param language $language Language object
	*/
	public function __construct($config, $db, $controller, $template, $dispatcher, $root_path, $php_ext, auth $auth, user $user, language $language)
	{
		parent::__construct($config, $db, $controller, $template, $dispatcher, $root_path, $php_ext);

		$this->auth = $auth;
		$this->user = $user;
		$this->language = $language;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_block_data(): array
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

		$posts_per_day	= sprintf('%.2f', $total_posts / $boarddays);
		$topics_per_day = sprintf('%.2f', $total_topics / $boarddays);
		$users_per_day	= sprintf('%.2f', $total_users / $boarddays);

		// Generate birthday list if required...
		$show_birthdays = ($this->config['load_birthdays'] && $this->config['allow_birthdays'] && $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'));

		if ($show_birthdays)
		{
			$this->birthdays();
		}

		$this->legend();

		$this->language->lang('LOGIN_VIEWFORUM');
		$this->template->assign_vars([
			'dls_posts'	 => $total_posts,
			'dls_topics' => $total_topics,
			'dls_users'	 => $total_users,
			'new_user'	 => get_username_string('full', (int) $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour']),

			'ppd' => $posts_per_day,
			'tpd' => $topics_per_day,
			'upd' => $users_per_day,
			's_birthday_list' => $show_birthdays,
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
				'member' => $birthday_username,
				'age'	 => $birthday_age,
			];
		}

		$this->template->assign_block_vars_array('birthdays', $birthdays);
	}

	/**
	* Legend
	*
	* @return void
	*/
	protected function legend(): void
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
			$this->template->assign_block_vars('legend', [
				'color' => (string) $row['group_colour'],
				'name'	=> (string) $row['group_name'],
				'link'	=> (string) append_sid("{$this->get('root_path')}memberlist.{$this->get('php_ext')}", "mode=group&amp;g={$row['group_id']}"),
				'not_authed' => (bool) $this->not_authed($row),
			]);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* Is visitor a bot or does he/she have permissions
	*
	* @param array $row Groups data
	* @return bool
	*/
	protected function not_authed($row): bool
	{
		return $row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile'));
	}
}
