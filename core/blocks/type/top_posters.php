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

/**
* DLS Web Top Posters block
*/
class top_posters extends base
{
	/**
	* {@inheritdoc}
	*/
	public function get_block_data(): array
	{
		return [
			'cat_name' => 'right',
			'ext_name' => 'dls_web',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
		$sql = 'SELECT user_id, user_type, user_posts, username, user_colour
				FROM ' . USERS_TABLE . '
				WHERE user_id <> ' . (int) ANONYMOUS . '
					AND user_type <> ' . (int) USER_IGNORE . '
					AND user_posts > 0
				ORDER BY user_posts DESC';
		$result = $this->db->sql_query_limit($sql, (int) $this->config['dls_user_limit'], 0, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('top_posters', [
				'top' => get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
				'posts' => (int) $row['user_posts'],
			]);
		}
		$this->db->sql_freeresult($result);
	}
}
