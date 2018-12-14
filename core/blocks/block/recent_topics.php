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

/**
* DLS Web Recent Topics block
*/
class recent_topics implements block_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \dls\web\core\helper */
	protected $core;

	/** @var phpBB root path */
	protected $root_path;

	/**
	* Constructor
	*
	* @param \phpbb\config\config     $config Config object
	* @param \phpbb\db\driver\driver_interface $db		 Db object
	* @param \phpbb\template\template		   $template Template object
	* @param \dls\web\core\helper			   $core	 Helper object
	* @param string $root_path Path to the phpbb includes directory.
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \dls\web\core\helper $core, $root_path)
	{
		$this->config = $config;
		$this->db = $db;
		$this->template = $template;
		$this->core = $core;
		$this->root_path = $root_path;
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
			$this->template->assign_block_vars('recent_topics', [
				'link'	=> append_sid("{$this->root_path}viewtopic.php", 't=' . $row['topic_id']),
				'title' => $this->core->truncate($row['topic_title'], $this->config['dls_title_length']),
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
