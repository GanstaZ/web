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
use phpbb\language\language;
use phpbb\textformatter\s9e\renderer;
use phpbb\user;
use phpbb\pagination;

/**
* DLS Web News block
*/
class news extends base
{
	/** @var auth */
	protected $auth;

	/** @var language */
	protected $language;

	/** @var s9e renderer */
	protected $renderer;

	/** @var user */
	protected $user;

	/** @var pagination */
	protected $pagination;

	/** @var int Page offset for pagination */
	protected $page;

	/** @var bool enable trim */
	protected $trim_news = false;

	/** @var bool is trimmed */
	protected $is_trimmed;

	/** @var string news order */
	protected $news_order = 'p.post_id DESC';

	/**
	* Constructor
	*
	* @param auth		$auth		Auth object
	* @param language	$language	Language object
	* @param renderer	$renderer	s9e renderer object
	* @param user		$user		User object
	* @param pagination $pagination Pagination object
	*/
	public function __construct($config, $db, $controller, $template, $dispatcher, $root_path, $php_ext, auth $auth, language $language, renderer $renderer, user $user, pagination $pagination)
	{
		parent::__construct($config, $db, $controller, $template, $dispatcher, $root_path, $php_ext);

		$this->auth = $auth;
		$this->language = $language;
		$this->renderer = $renderer;
		$this->user = $user;
		$this->pagination = $pagination;

		if (!function_exists('phpbb_get_user_rank'))
		{
			include($this->get('root_path') . 'includes/functions_display.php');
		}
	}

	/**
	* {@inheritdoc}
	*/
	public function get_block_data(): array
	{
		return [
			'cat_name' => 'special',
			'ext_name' => 'dls_web',
		];
	}

	/**
	* Set page start
	*
	* @param int $page
	* @return \dls\web\core\blocks\block\news News object
	*/
	public function set_page(int $page)
	{
		$this->page = ($page - 1) * (int) $this->config['dls_limit'];

		return $this;
	}

	/**
	* Trim news [Set to true if you want news to be trimmed]
	*
	* @param bool $bool
	* @return \dls\web\core\blocks\block\news News object
	*/
	public function trim_news(bool $bool)
	{
		$this->trim_news = $bool;

		return $this;
	}

	/**
	* News categories
	*
	* @param int $fid
	* @return string
	*/
	public function categories(int $fid): string
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

		return $forum_ary[$fid];
	}

	/**
	* Assign breadcrumb
	*
	* @param string $name	Name of the breadcrumb
	* @param string $route	Name of the route
	* @param array	$params Additional params
	* @return \dls\web\core\blocks\block\news News object
	*/
	public function assign_breadcrumb(string $name, string $route, array $params)
	{
		$this->template->assign_block_vars('navlinks', [
			'FORUM_NAME'   => $name,
			'U_VIEW_FORUM' => $this->controller->route($route, $params),
		]);

		return $this;
	}

	/**
	* News base
	*
	* @param int $forum_id Forum id to fetch news data
	* @return void
	*/
	public function base(int $forum_id): void
	{
		// Check news id
		if (!$this->categories($forum_id))
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

		// Assign breadcrumb
		$this->assign_breadcrumb($this->categories($forum_id), 'dls_web_news_base', ['id' => $forum_id]);

		// Do the sql thang
		$sql_ary = $this->get_sql_data($forum_id);
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query_limit($sql, (int) $this->config['dls_limit'], $this->page, 60);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('news', $this->get_template_data($row));
		}
		$this->db->sql_freeresult($result);

		if ($this->config['dls_pagination'])
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

			$this->pagination->generate_template_pagination($base, 'pagination', 'page', $total, (int) $this->config['dls_limit'], $this->page);

			$this->template->assign_var('total_news', $total);
		}
	}

	/**
	* Get sql data
	*
	* @param int $s_id id to get news or article data
	* @param string $where query where clause [forum or topic]
	* @return array
	*/
	public function get_sql_data($s_id, string $where = 'forum'): array
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
	* @return array
	*/
	public function get_template_data(array $row): array
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
			'link'	  => $this->controller->route('dls_web_article', ['aid' => $row['topic_id']]),
			'title'	  => $this->truncate($row['topic_title'], $this->config['dls_title_length']),
			'date'	  => $this->user->format_date($row['topic_time']),
			'author'  => get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
			'avatar'  => phpbb_get_user_avatar($poster),
			'rank'	  => $rank_title['title'],
			'views'	  => $row['topic_views'],
			'replies' => $row['topic_posts_approved'] - 1,
			'text'	  => $this->trim_news ? $this->trim_message($text) : $text,
			'topic_link' => append_sid("{$this->get('root_path')}viewtopic.{$this->get('php_ext')}", "f={$row['forum_id']}&amp;t={$row['topic_id']}"),
			'is_trimmed' => $this->is_trimmed,
		];
	}

	/**
	* Trim message
	*
	* @param string $text
	* @return string
	*/
	public function trim_message(string $text): string
	{
		$this->is_trimmed = false;

		if (utf8_strlen($text) > (int) $this->config['dls_content_length'])
		{
			$this->is_trimmed = true;

			$offset = ((int) $this->config['dls_content_length'] - 3) - utf8_strlen($text);
			$text	= utf8_substr($text, 0, utf8_strrpos($text, ' ', $offset));
		}

		return $text;
	}

	/**
	* Get article
	*
	* @param int $topic_id the id of the article
	* @return void
	*/
	public function get_article($topic_id): void
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

		// Assign breadcrumb
		$this->assign_breadcrumb($this->get_template_data($row)['title'], 'dls_web_article', ['aid' => $topic_id]);

		$this->template->assign_block_vars('article', $this->get_template_data($row));

		$this->db->sql_freeresult($result);
	}
}
