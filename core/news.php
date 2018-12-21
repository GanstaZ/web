<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\controller\helper as controller_helper;
use phpbb\language\language;
use phpbb\textformatter\s9e\renderer;
use dls\web\core\helper;
use phpbb\user;
use phpbb\pagination;

/**
* DLS Web news class
*/
class news
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $controller_helper;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/** @var \dls\web\core\helper */
	protected $helper;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var phpBB root path */
	protected $root_path;

	/** @var int Page offset for pagination */
	protected $page;

	/** @var bool is trim set */
	protected $is_trimmed;

	/** @var string news order */
	protected $news_order = 'p.post_id DESC';

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth Auth object
	* @param \phpbb\config\config $config Config object
	* @param \phpbb\db\driver\driver_interface $db Db object
	* @param \phpbb\controller\helper $controller_helper Controller helper object
	* @param \phpbb\language\language $language Language object
	* @param \phpbb\textformatter\s9e\renderer $renderer Renderer object
	* @param \dls\web\core\helper $helper Helper object
	* @param \phpbb\user $user User object
	* @param \phpbb\pagination $pagination Pagination object
	*/
	public function __construct(auth $auth, config $config, driver_interface $db, controller_helper $controller_helper, language $language, renderer $renderer, helper $helper, user $user, pagination $pagination)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->controller_helper = $controller_helper;
		$this->language = $language;
		$this->renderer = $renderer;
		$this->helper = $helper;
		$this->user = $user;
		$this->pagination = $pagination;
		$this->root_path = $this->helper->get('root_path');

		if (!function_exists('phpbb_get_user_rank'))
		{
			include($this->root_path . 'includes/functions_display.php');
		}
	}

	/**
	* Set page start
	*
	* @param int $page
	*/
	public function set_page($page)
	{
		$this->page = ($page - 1) * (int) $this->config['dls_limit'];

		return $this;
	}

	/**
	* Is the message trimmed?
	*
	* @return bool
	*/
	public function is_trimmed()
	{
		return (bool) $this->is_trimmed;
	}

	/**
	* News base
	*
	* @param int $forum_id use it to fetch news data
	*
	* @return mixed or null
	*/
	public function base($forum_id)
	{
		$category = $this->get_categories();

		// Check news id
		if (!$category[$forum_id])
		{
			throw new \phpbb\exception\http_exception(404, 'NO_FORUM', [$forum_id]);
		}

		// Check permissions
		if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				throw new \phpbb\exception\http_exception(403, 'SORRY_AUTH_READ', [$forum_id]);
			}

			login_box('', $this->language->lang('LOGIN_VIEWFORUM'));
		}

		$this->helper->assign('vars', [
			'cat_name' => $category[$forum_id],
			'cat_link' => $this->controller_helper->route('dls_web_news_base', ['id' => $forum_id]),
		]);

		// Do the sql thang
		$sql_ary = $this->get_sql_data($forum_id);

		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query_limit($sql, (int) $this->config['dls_limit'], (int) $this->page, 60);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->helper->assign('block_vars', 'news', $this->get_template_data($row));
		}
		$this->db->sql_freeresult($result);

		if ($this->config['dls_show_pagination'])
		{
			// Get total posts
			$sql_ary['SELECT'] = 'COUNT(p.post_id) AS num_posts';
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$total = (int) $this->db->sql_fetchfield('num_posts');
			$this->db->sql_freeresult($result);

			$base = [
				'routes' => [
					'dls_web_news_base',
					'dls_web_news_page',
				],
				'params' => ['id' => $forum_id],
			];

			$this->pagination->generate_template_pagination($base, 'pagination', 'page', $total, $this->config['dls_limit'], $this->page);

			$this->helper->assign('var', 'total_news', $this->language->lang('TOTAL_POSTS_COUNT', $total));
		}

		return;
	}

	/**
	* Get sql data
	*
	* @param int $s_id id to get news or article data
	* @param string $where query where clause [forum or topic]
	* @return array
	*/
	public function get_sql_data($s_id, $where = 'forum')
	{
		$sql_ary = [
			'SELECT'	=> 't.topic_id, t.forum_id, t.topic_title, t.topic_time, t.topic_views, t.topic_status, t.topic_posts_approved,
			p.post_id, p.poster_id, p.post_text, u.user_id, u.username, u.user_posts, u.user_rank, u.user_colour, u.user_avatar,
			u.user_avatar_type, u.user_avatar_width, u.user_avatar_height',

			'FROM'		=> [
				TOPICS_TABLE => 't',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [POSTS_TABLE => 'p'],
					'ON'   => 'p.post_id = t.topic_first_post_id'
				],
				[
					'FROM' => [USERS_TABLE => 'u'],
					'ON'   => 'u.user_id = p.poster_id'
				],
			],

			'WHERE'		=> 't.' . $where . '_id = ' . (int) $s_id . '
				AND t.topic_status <> ' . ITEM_MOVED . '
				AND t.topic_visibility = 1',
		];

		if ($where === 'forum')
		{
			$sql_ary['ORDER_BY'] = $this->news_order;
		}

		return $sql_ary;
	}

	/**
	* Get template data
	*
	* @param array $row data array
	* @param string $action default is trim
	* @return array
	*/
	public function get_template_data($row, $action = 'trim')
	{
		$poster = [
			'user_rank'		=> $row['user_rank'],
			'avatar'		=> $row['user_avatar'],
			'avatar_type'	=> $row['user_avatar_type'],
			'avatar_width'	=> $row['user_avatar_width'],
			'avatar_height'	=> $row['user_avatar_height'],
		];

		$rank_title = phpbb_get_user_rank($poster, $row['user_posts']);
		$text = $this->renderer->render($row['post_text']);

		return [
			'id'	  => $row['post_id'],
			'link'	  => $this->controller_helper->route('dls_web_article', ['aid' => $row['topic_id']]),
			'title'	  => $this->helper->truncate($row['topic_title'], $this->config['dls_title_length']),
			'date'	  => $this->user->format_date($row['topic_time']),
			'author'  => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
			'avatar'  => phpbb_get_user_avatar($poster),
			'rank'	  => $rank_title['title'],
			'views'	  => $row['topic_views'],
			'replies' => $row['topic_posts_approved'] - 1,
			'text'	  => ($action === 'trim') ? $this->trim_message($text) : $text,
			'topic_link' => append_sid("{$this->root_path}viewtopic.php", 'f=' . $row['forum_id'] . '&amp;t=' . $row['topic_id']),
			'is_trimmed' => $this->is_trimmed(),
		];
	}

	/**
	* Trim message
	*
	* @param string $text trim message if needed
	* @return string
	*/
	public function trim_message($text)
	{
		$this->is_trimmed = false;

		if (utf8_strlen($text) > $this->config['dls_content_length'])
		{
			$this->is_trimmed = true;
			$offset = ($this->config['dls_content_length'] - 3) - utf8_strlen($text);
			$text = utf8_substr($text, 0, utf8_strrpos($text, ' ', $offset)) . $this->language->lang('ELLIPSIS');
		}

		return $text;
	}

	/**
	* Get news categories
	*
	* @return array
	*/
	public function get_categories()
	{
		$sql = 'SELECT forum_id, forum_name
				FROM ' . FORUMS_TABLE . '
				WHERE forum_type = ' . FORUM_POST . '
					AND news_fid_enable = 1';
		$result = $this->db->sql_query($sql, 86400);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$forum_ary[(int) $row['forum_id']] = (string) $row['forum_name'];
		}
		$this->db->sql_freeresult($result);

		return ($forum_ary) ? $forum_ary : [];
	}

	/**
	* Get article
	*
	* @param int $topic_id the id of the article
	* @return mixed
	*/
	public function get_article($topic_id)
	{
		// Do the sql thang
		$sql_ary = $this->get_sql_data($topic_id, 'topic');
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql, 86400);
		$row = $this->db->sql_fetchrow($result);

		if (!$row)
		{
			throw new \phpbb\exception\http_exception(403, 'NO_TOPICS', [$row]);
		}

		$this->helper->assign('vars', $this->get_template_data($row, 'no'));

		$this->db->sql_freeresult($result);

		return;
	}
}
