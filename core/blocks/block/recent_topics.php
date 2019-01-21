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
class recent_topics extends base
{
	/**
	* {@inheritdoc}
	*/
	public function load(): void
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
				'link'	=> append_sid("{$this->helper->get('root_path')}viewtopic.{$this->helper->get('php_ext')}", 't=' . $row['topic_id']),
				'title' => $this->helper->truncate($row['topic_title'], $this->config['dls_title_length']),
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
