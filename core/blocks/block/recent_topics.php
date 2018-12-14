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

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use dls\web\core\helper;

/**
* DLS Web Recent Topics block
*/
class recent_topics implements block_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \dls\web\core\helper */
	protected $helper;

	/** @var phpBB root path */
	protected $root_path;

	/** @var file extension */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\config\config $config Config object
	* @param \phpbb\db\driver\driver_interface $db Db object
	* @param \dls\web\core\helper $helper Data helper object
	*/
	public function __construct(config $config, driver_interface $db, helper $helper)
	{
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->root_path = $this->helper->get('root_path');
		$this->php_ext = $this->helper->get('php_ext');
	}

	/**
	* {@inheritdoc}
	*/
	public function get_data()
	{
		return [
			'block_name' => 'dls_recent_topics',
			'cat_name' => 'side',
			'ext_name' => 'dls_web',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load()
	{
		$sql = 'SELECT topic_id, topic_visibility, topic_title, topic_time, topic_status
				FROM ' . TOPICS_TABLE . '
				WHERE topic_status <> ' . ITEM_MOVED . '
					AND topic_visibility = 1
				ORDER BY topic_id DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['dls_user_limit'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->helper->assign('block_vars', 'recent_topics', [
				'link'	=> append_sid("{$this->root_path}viewtopic.{$this->php_ext}", 't=' . $row['topic_id']),
				'title' => $this->helper->truncate($row['topic_title'], $this->config['dls_title_length']),
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
