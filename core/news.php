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
	protected $helper;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \dls\web\core\core */
	protected $core;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var phpBB root path */
	protected $root_path;

	protected $page;
	protected $is_trimmed;
	protected $news_order = 'p.post_id DESC';

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth				   $auth	   Auth object
	* @param \phpbb\config\config			   $config	   Config object
	* @param \phpbb\db\driver\driver_interface $db		   Db object
	* @param \phpbb\controller\helper		   $helper	   Controller helper object
	* @param \phpbb\language\language		   $language   Language object
	* @param \phpbb\template\template		   $template   Template object
	* @param \dls\web\core\helper			   $core	   Core helper object
	* @param \phpbb\user					   $user	   User object
	* @param \phpbb\pagination				   $pagination Pagination object
	* @param string $root_path Path to the phpbb includes directory.
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\language\language $language, \phpbb\template\template $template, \dls\web\core\helper $core, \phpbb\user $user, \phpbb\pagination $pagination, $root_path)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->language = $language;
		$this->template = $template;
		$this->core = $core;
		$this->user = $user;
		$this->pagination = $pagination;
		$this->root_path = $root_path;

		if (!function_exists('phpbb_get_user_rank'))
		{
			include($this->root_path . 'includes/functions_display.php');
		}
	}

	/**
	* Get
	*
	* @param string $name Name of the service we want to use.
	*
	* @return object
	*/
	public function get($name)
	{
		return $this->{$name};
	}

	/**
	* Set start
	*
	* @param int $start
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
		// Check permissions
		if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				throw new \phpbb\exception\http_exception(403, 'SORRY_AUTH_READ', [$forum_id]);
			}

			login_box('', $this->language->lang('LOGIN_VIEWFORUM'));
		}

		$this->template->assign_vars([
			'cat_name' => $this->category_name($forum_id),
			'cat_link' => $this->helper->route('dls_web_news_base', ['id' => $forum_id]),
		]);

		// Do the sql thang
		$sql_ary = $this->get_sql_data($forum_id);

		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query_limit($sql, (int) $this->config['dls_limit'], (int) $this->page, 60);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('news', $this->get_template_data($row, true));
		}

		$this->db->sql_freeresult($result);

		if ($this->config['dls_show_pagination'])
		{
			// get total posts.
			$sql_ary['SELECT'] = 'COUNT(p.post_id) AS num_posts';
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query($sql);

			// get the total topics, this is a single row, single field.
			$total = (int) $this->db->sql_fetchfield('num_posts');
			// free the result
			$this->db->sql_freeresult($result);

			$base = [
				'routes' => [
					'dls_web_news_base',
					'dls_web_news_page',
				],
				'params' => ['id' => $forum_id],
			];

			$this->pagination->generate_template_pagination($base, 'pagination', 'page', $total, $this->config['dls_limit'], $this->page);

			$this->template->assign_var('total_news', $this->language->lang('TOTAL_POSTS_COUNT', $total));
		}

		return;
	}

	/**
	* Get sql data
	*
	* @return array
	*/
	public function get_sql_data($forum_id)
	{
		return [
			'SELECT' => 'p.post_id, p.post_text, p.bbcode_bitfield, p.bbcode_uid, t.topic_id, t.forum_id,
			t.topic_visibility, t.topic_title, t.topic_poster, t.topic_time, t.topic_views, t.topic_status,
			t.topic_posts_approved, t.topic_first_post_id',

			'FROM' => [
				POSTS_TABLE => 'p',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [TOPICS_TABLE => 't'],
					'ON' => 'p.post_id = t.topic_first_post_id'
				],
			],

			'WHERE' => 't.forum_id = ' . (int) $forum_id . '
				AND t.topic_status <> ' . ITEM_MOVED . '
				AND t.topic_visibility = 1',
			'ORDER_BY' => $this->news_order,
		];
	}

	/**
	* Get template data
	*
	* @return array
	*/
	public function get_template_data($row, $trim = null)
	{
		$poster = $this->poster($row['topic_poster']);
		$rank_title = phpbb_get_user_rank($poster, $poster['user_posts']);
		$bbcode_options = OPTION_FLAG_BBCODE + OPTION_FLAG_SMILIES + OPTION_FLAG_LINKS;
		$text = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $bbcode_options, true);

		return [
			'id'	  => $row['post_id'],
			'link'	  => $this->helper->route('dls_web_article', ['aid' => $row['topic_id']]),
			'title'	  => $this->core->truncate($row['topic_title'], $this->config['dls_title_length']),
			'date'	  => $this->user->format_date($row['topic_time']),
			'author'  => get_username_string('full', $poster['user_id'], $poster['username'], $poster['user_colour']),
			'avatar'  => phpbb_get_user_avatar($poster),
			'rank'	  => $rank_title['title'],
			'views'	  => $row['topic_views'],
			'replies' => $row['topic_posts_approved'] - 1,
			'text'	  => ($trim) ? $this->trim_message($text) : $text,
			'topic_link' => append_sid("{$this->root_path}viewtopic.php", 'f=' . $row['forum_id'] . '&amp;t=' . $row['topic_id']),
			'is_trimmed' => $this->is_trimmed(),
		];
	}

	/**
	* Trim message.
	*
	* @param string $text trim message, if needed.
	*
	* @return mixed
	*/
	public function trim_message($text)
	{
		$this->is_trimmed = false;

		if (utf8_strlen($text) > $this->config['dls_content_length'])
		{
			$this->is_trimmed = true;
			$text = $this->get_offset($text);
		}

		return $text;
	}

	/**
	* Get offset
	*
	* @param string $text trim message, if needed.
	*
	* @return string
	*/
	public function get_offset($text)
	{
		$offset = ($this->config['dls_content_length'] - 3) - utf8_strlen($text);

		return utf8_substr($text, 0, utf8_strrpos($text, ' ', $offset)) . $this->language->lang('ELLIPSIS');
	}

	/**
	* Get category name
	*
	* @param int $cat_id use to get forum name.
	*
	* @return mixed or null
	*/
	public function category_name($category_id = null)
	{
		$sql = 'SELECT forum_name
				FROM ' . FORUMS_TABLE . '
				WHERE forum_id = ' . (int) $category_id;
		$result = $this->db->sql_query($sql, 3600);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (!$row) ? false : $row['forum_name'];
	}

	/**
	* Get category name
	*
	* @param int $cat_id use to get forum name.
	*
	* @return mixed or null
	*/
	public function poster($poster_id = null)
	{
		$sql = 'SELECT user_id, username, user_posts, user_rank, user_colour, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . (int) $poster_id;
		$result = $this->db->sql_query($sql, 3600);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (!$row) ? false : $row;
	}

	/**
	* Get article
	*
	* @param int $topic_id the id of the article.
	*
	* @return mixed or null
	*/
	public function get_article($topic_id = null)
	{
		// Do the sql thang
		$sql = $this->db->sql_build_query('SELECT', [
			'SELECT' => 'p.post_id, p.post_text, p.bbcode_bitfield, p.bbcode_uid, t.topic_id, t.forum_id,
			t.topic_visibility, t.topic_title, t.topic_poster, t.topic_time, t.topic_views, t.topic_status,
			t.topic_posts_approved, t.topic_first_post_id',

			'FROM' => [
				POSTS_TABLE => 'p',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [TOPICS_TABLE => 't'],
					'ON' => 'p.post_id = t.topic_first_post_id'
				],
			],

			'WHERE' => 't.topic_id = ' . (int) $topic_id . '
				AND t.topic_status <> ' . ITEM_MOVED . '
				AND t.topic_visibility = 1',
		]);

		$result = $this->db->sql_query($sql, 3600);
		$row = $this->db->sql_fetchrow($result);

		if (!$row)
		{
			throw new \phpbb\exception\http_exception(403, 'NO_TOPICS', [$row]);
		}

		$this->template->assign_vars($this->get_template_data($row));

		$this->db->sql_freeresult($result);

		return;
	}
}
