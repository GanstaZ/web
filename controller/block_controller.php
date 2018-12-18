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
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \dls\web\core\blocks\manager */
	protected $manager;

	/** @var \dls\web\core\blocks\event */
	protected $event;

	/** @var array Contains enabled block services */
	protected $blocks;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db Db object
	* @param \dls\web\core\blocks\manager $manager Data manager object
	* @param \dls\web\core\blocks\event $event Data object
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
	* @param string $type [default: category, block]
	* @return null
	*/
	public function load($data = null, string $type = 'category')
	{
		if ($blocks = $this->get_blocks($data, $type))
		{
			$this->loading($blocks);
		}
	}

	/**
	* Get blocks
	*
	* @param mixed $data
	* @param string $type [default: category, block]
	* @return array
	*/
	protected function get_blocks($data, $type)
	{
		$where = 'active = 1';

		if (null !== $data)
		{
			$where = $this->get_where_clause($data, $type);
		}

		// Register requested data
		$this->register_blocks($where);

		return array_filter($this->blocks);
	}

	/**
	* Register all enabled blocks
	*
	* @param string $where sql where clause
	* @return null
	*/
	protected function register_blocks($where)
	{
		$sql = 'SELECT *
				FROM ' . $this->manager->blocks_data() . '
				WHERE ' . $where . '
					AND position <> 0
				ORDER BY position';
		$result = $this->db->sql_query($sql, 86400);

		$this->blocks = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$data = [
				'block_name' => (string) $row['block_name'],
				'ext_name' => (string) $row['ext_name'],
			];

			$data['block_name'] = $this->manager->is_dls($data);

			if ($block = $this->manager->get($row['block_name']))
			{
				$this->blocks[$row['block_name']] = $block;
				$this->event->set_data($row['cat_name'], [$data['block_name'] => $data['ext_name']]);
			}
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* Get where clause
	*
	* @param mixed $data
	* @param string $type [default: category, block]
	* @return string where
	*/
	protected function get_where_clause($data, $type)
	{
		$where = '';
		if (is_array($data))
		{
			$where = $this->db->sql_in_set('cat_name', $data);

			if ($type === 'block')
			{
				$where = $this->db->sql_in_set('block_name', $data);
			}
		}
		else if (is_string($data))
		{
			$where = "block_name = '" . $this->where_clause_affix($data);

			if ($type === 'category')
			{
				$where = "cat_name = '" . $this->where_clause_affix($data);
			}
		}

		return $where;
	}

	/**
	* Get escaped data for where clause
	*
	* @param string $data
	* @return string
	*/
	protected function where_clause_affix($data)
	{
		return $this->db->sql_escape($data) . "' AND active = 1";
	}

	/**
	* Loading
	*
	* @param array $blocks Array of enabled blocks
	* @return null
	*/
	protected function loading($blocks)
	{
		foreach ($blocks as $block)
		{
			$block->load();
		}
	}
}
