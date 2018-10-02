<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* DLS Web Event listener
*/
class blocks implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \dls\web\core\helper */
	protected $core;

	/** @var \phpbb\event\dispatcher */
	protected $dispatcher;

	/** @var phpBB root path */
	protected $root_path;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth				   $auth	   Auth object
	* @param \phpbb\config\config			   $config	   Config object
	* @param \phpbb\db\driver\driver_interface $db		   Db object
	* @param \phpbb\language\language		   $language   Language object
	* @param \phpbb\user					   $user	   User object
	* @param \phpbb\template\template		   $template   Template object
	* @param \dls\web\core\helper			   $core	   Helper object
	* @param \phpbb\event\dispatcher		   $dispatcher Dispatcher object
	* @param string $root_path Path to the phpbb includes directory.
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\user $user, \phpbb\template\template $template, \dls\web\core\helper $core, \phpbb\event\dispatcher $dispatcher, $root_path)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->user = $user;
		$this->template = $template;
		$this->core = $core;
		$this->dispatcher = $dispatcher;
		$this->root_path = $root_path;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	*/
	public static function getSubscribedEvents()
	{
		return [
			'core.page_header' => 'dls_side_blocks',
			'dls.web_main_block' => 'dls_main_blocks',
		];
	}

	/**
	* Block dls_side_blocks
	*/
	public function dls_side_blocks()
	{
		// DLS Web Information
		if ($this->config['dls_information'])
		{
			/**
			* Event dls.web.information_before
			*
			* @event dls.web.information_before
			* @since 2.3.6-RC1
			*/
			$this->dispatcher->dispatch('dls.web.information_before');

			// Set template vars
			$this->template->assign_vars([
				'phpbb_version' => (string) $this->config['version'],
				'core_stable' => (string) $this->config['dls_core_version'],
				'portal_name' => (string) $this->config['dls_core_name'],
			]);
		}

		$this->the_team();
		$this->top_posters();
		$this->recent_posts();
		$this->recent_topics();

		/**
		* Event dls.web.side_blocks_after
		*
		* You can use this event to load function files and initiate objects
		*
		* @event dls.web.side_blocks_after
		* @since 2.3.5
		*/
		$this->dispatcher->dispatch('dls.web.side_blocks_after');
	}

	/**
	* The team
	*
	* @return void
	*/
	protected function the_team()
	{
		// DLS Web The Team
		if ($this->config['dls_the_team'])
		{
			$group_id = (int) $this->config['dls_the_team_fid'];

			$sql = 'SELECT ug.*, u.username, u.user_id, u.user_colour, u.username_clean
					FROM ' . USER_GROUP_TABLE . ' ug, ' . USERS_TABLE . ' u
					WHERE ug.user_id = u.user_id
						AND ug.user_pending = 0
						AND ug.group_id = ' . $group_id;
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('the_team', [
					'member' => get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
				]);
			}
			$this->db->sql_freeresult($result);

			$this->template->assign_var('get_team_name', $this->core->get_team($group_id));
		}
	}

	/**
	* Top posters
	*
	* @return void
	*/
	protected function top_posters()
	{
		// DLS Web Top Posters
		if ($this->config['dls_top_posters'])
		{
			$sql = 'SELECT user_id, user_type, user_posts, username, user_colour
					FROM ' . USERS_TABLE . '
					WHERE user_id <> ' . (int) ANONYMOUS . '
						AND user_type <> ' . (int) USER_IGNORE . '
						AND user_posts > 0
					ORDER BY user_posts DESC';
			$result = $this->db->sql_query_limit($sql, (int) $this->config['dls_limit']);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('top_posters', [
					'top' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
					'posts' => (int) $row['user_posts'],
				]);
			}
			$this->db->sql_freeresult($result);
		}
	}

	/**
	* Recent posts
	*
	* @return void
	*/
	protected function recent_posts()
	{
		// DLS Web Recent Posts
		if ($this->config['dls_recent_posts'])
		{
			$sql = 'SELECT p.post_id, t.topic_id, t.topic_visibility, t.topic_title, t.topic_time, t.topic_status, t.topic_last_post_id
					FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
					WHERE t.topic_last_post_id = p.post_id
						AND t.topic_status <> ' . ITEM_MOVED . '
						AND t.topic_visibility = 1
					ORDER BY p.post_id DESC';
			$result = $this->db->sql_query_limit($sql, (int) $this->config['dls_user_limit']);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('recent_posts', [
					'link'	=> append_sid("{$this->root_path}viewtopic.php", 't=' . $row['topic_id'] . '#p' . $row['post_id']),
					'title' => $this->core->truncate($row['topic_title'], $this->config['dls_title_length']),
				]);
			}
			$this->db->sql_freeresult($result);
		}
	}

	/**
	* Recent topics
	*
	* @return void
	*/
	protected function recent_topics()
	{
		// DLS Web Recent Topics
		if ($this->config['dls_recent_topics'])
		{
			$sql = 'SELECT topic_id, topic_visibility, topic_title, topic_time, topic_status
					FROM ' . TOPICS_TABLE . '
					WHERE topic_status <> ' . ITEM_MOVED . '
						AND topic_visibility = 1
					ORDER BY topic_id DESC';
			$result = $this->db->sql_query_limit($sql, (int) $this->config['dls_user_limit']);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('recent_topics', [
					'link'	=> append_sid("{$this->root_path}viewtopic.php", 't=' . $row['topic_id']),
					'title' => $this->core->truncate($row['topic_title'], $this->config['dls_title_length']),
				]);
			}
			$this->db->sql_freeresult($result);
		}
	}

	/**
	* Block dls_main_blocks
	*/
	public function dls_main_blocks()
	{
		// DLS Web Online Info
		if ($this->config['load_online_time'])
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

			$this->template->assign_vars([
				'TOTAL_POSTS' => $this->language->lang('TOTAL_POSTS_COUNT', $total_posts),
				'TOTAL_TOPICS' => $this->language->lang('TOTAL_TOPICS', $total_topics),
				'TOTAL_USERS' => $this->language->lang('TOTAL_USERS', $total_users),
				'NEWEST_USER' => $this->language->lang('NEWEST_USER', get_username_string('full', $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour'])),

				'LEGEND' => $this->legend(),
				'BIRTHDAY_LIST' => (empty($birthday_list)) ? '' : implode($this->language->lang('COMMA_SEPARATOR'), $birthday_list),

				'POSTS_PER_DAY' => $this->language->lang('T_POST_DAY', (float) $posts_per_day),
				'TOPICS_PER_DAY' => $this->language->lang('T_TOPICS_DAY', (float) $topics_per_day),
				'USERS_PER_DAY' => $this->language->lang('T_USERS_DAY', (float) $users_per_day),
				'S_DISPLAY_BIRTHDAY_LIST' => $show_birthdays,
			]);
		}

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
	protected function birthdays()
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
			$birthday_username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
			$birthday_year = (int) substr($row['user_birthday'], -4);
			$birthday_age = ($birthday_year) ? max(0, $now['year'] - $birthday_year) : '';

			$birthdays[] = [
				'USERNAME' => $birthday_username,
				'AGE' => $birthday_age,
			];
		}

		$this->template->assign_block_vars_array('birthdays', $birthdays);
	}

	/**
	* Legend
	*
	* @return string
	*/
	protected function legend()
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
				ORDER BY g.' . $order_legend . ' ASC';

		if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
					FROM ' . GROUPS_TABLE . '
					WHERE group_legend > 0
					ORDER BY ' . $order_legend . ' ASC';
		}

		$result = $this->db->sql_query($sql);

		$legend = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
			$group_name = $this->core->get_name($row['group_name']);
			$group_link = append_sid("{$this->root_path}memberlist.php", 'mode=group&amp;g=' . $row['group_id']);

			if ($this->is_authed($row))
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
	protected function is_authed($row)
	{
		return $row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile'));
	}
}
