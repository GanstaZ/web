<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dls.org/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core;

/**
* DLS Web helper class
*/
class helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\group\helper */
	protected $group_helper;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db			 Db object
	* @param \phpbb\group\helper			   $group_helper Group helper object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\group\helper $group_helper)
	{
		$this->db = $db;
		$this->group_helper = $group_helper;
	}

	/**
	* Get group name
	*
	* @param string $group_name name of the group
	*
	* @return string group_name
	*/
	public function get_name($group_name)
	{
		return $this->group_helper->get_name($group_name);
	}

	/**
	* Truncate title
	*
	* @param string $title	Truncate title
	* @param string $length Max length of the string
	*
	* @return mixed
	*/
	public function truncate($title, $length)
	{
		return truncate_string(censor_text($title), $length, 255, false, '...');
	}

	/**
	* Get group name
	*
	* @param int $group_id id of a group
	*
	* @return string group_name
	*/
	public function get_team($group_id)
	{
		$sql = 'SELECT group_name, group_type
				FROM ' . GROUPS_TABLE . '
				WHERE group_id = ' . (int) $group_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			return false;
		}

		return $this->get_name($row['group_name']);
	}
}
