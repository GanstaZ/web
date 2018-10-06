<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\block;

/**
* DLS Web The Team block
*/
class the_team extends base
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \dls\web\core\helper */
	protected $core;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db		 Db object
	* @param \phpbb\language\language		   $language Language object
	* @param \phpbb\template\template		   $template Template object
	* @param \dls\web\core\helper			   $core	 Helper object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\template\template $template, \dls\web\core\helper $core)
	{
		$this->db = $db;
		$this->language = $language;
		$this->template = $template;
		$this->core = $core;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_data()
	{
		return [
			'block_name' => 'dls_the_team',
			'cat_name' => 'side_blocks',
			'vendor' => 'dls_web',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load()
	{
		$group_id = (int) $this->config['dls_the_team_fid'];

		$sql = 'SELECT ug.*, u.username, u.user_id, u.user_colour, u.username_clean
				FROM ' . USER_GROUP_TABLE . ' ug, ' . USERS_TABLE . ' u
				WHERE ug.user_id = u.user_id
					AND ug.user_pending = 0
					AND ug.group_id = ' . $group_id;
		$result = $this->db->sql_query($sql, 3600);

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
