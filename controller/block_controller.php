<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\controller;

use phpbb\db\driver\driver_interface;
use dls\web\core\blocks\manager;
use dls\web\core\blocks\event;

/**
* DLS Web block controller
*/
class block_controller
{
	/** @var driver_interface */
	protected $db;

	/** @var manager */
	protected $manager;

	/** @var event */
	protected $event;

	/** @var array type */
	protected $type = ['cat', 'block'];

	/**
	* Constructor
	*
	* @param driver_interface $db	   Database object
	* @param manager		  $manager Manager object
	* @param event			  $event   Event object
	*/
	public function __construct(driver_interface $db, manager $manager, event $event)
	{
		$this->db = $db;
		$this->event = $event;
		$this->manager = $manager;
	}

	/**
	* Load blocks
	*
	* @param mixed $data
	* @param string $type [default: cat, block]
	* @return null
	*/
	public function load($data = null, string $type = 'cat')
	{
		if (!in_array($type, $this->type))
		{
			return;
		}

		if ($blocks = $this->get_blocks($data, $type))
		{
			$this->loading($blocks);
		}
	}

	/**
	* Get blocks
	*
	* @param mixed $data
	* @param string $type
	* @return array
	*/
	protected function get_blocks($data, $type)
	{
		$where = (null !== $data) ? $this->where_clause($data, $type) : 'active = 1';

		$sql = 'SELECT *
				FROM ' . $this->manager->blocks_data() . '
				WHERE ' . $where . '
				ORDER BY position';
		$result = $this->db->sql_query($sql, 86400);

		$blocks = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($block = $this->manager->get($row['block_name']))
			{
				$blocks[$row['block_name']] = $block;

				$block_data = [
					'block_name' => (string) $row['block_name'],
					'ext_name' => (string) $row['ext_name'],
				];

				$block_data['block_name'] = $this->manager->is_dls($block_data);
				$this->event->set_data($row['cat_name'], [$block_data['block_name'] => $block_data['ext_name']]);
			}
		}
		$this->db->sql_freeresult($result);

		return $blocks;
	}

	/**
	* Where clause
	*
	* @param array|string $data
	* @param string $type
	* @return string
	*/
	protected function where_clause($data, $type)
	{
		if (is_array($data))
		{
			return $this->db->sql_in_set("{$type}_name", $data) . ' AND active = 1';
		}
		else if (is_string($data))
		{
			return "{$type}_name = '" . $this->db->sql_escape($data) . "' AND active = 1";
		}
	}

	/**
	* Loading
	*
	* @param array $blocks Array of requested blocks
	* @return void
	*/
	protected function loading($blocks)
	{
		foreach ($blocks as $block)
		{
			$block->load();
		}
	}
}
