<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks;

use phpbb\db\driver\driver_interface;

/**
* DLS Web blocks controller
*/
class blocks_controller
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \dls\web\core\blocks\manager */
	protected $manager;

	/** @var array Contains enabled block services */
	protected $blocks;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db Db object
	* @param \dls\web\core\blocks\manager $manager Data manager object
	*/
	public function __construct(driver_interface $db, manager $manager)
	{
		$this->db = $db;
		$this->manager = $manager;
	}

	/**
	* Load blocks
	*
	* @param mixed $data
	* @param string $type [default: category, blocks]
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
	* @param string $type [default: category, blocks]
	* @return array
	*/
	protected function get_blocks($data, $type)
	{
		$where = 'active = 1';

		if (null !== $data)
		{
			$where = "cat_name = '" . $this->where_clause_affix($data);

			if (is_array($data))
			{
				$where = $this->get_where_clause($data, $type);
			}
			else if (is_string($data) && $type === 'blocks')
			{
				$where = "block_name = '" . $this->where_clause_affix($data);
			}
		}

		// Register requested data
		$this->register_blocks($where);

		return array_filter($this->blocks);
	}

	/**
	* Get where clause
	*
	* @param mixed $data
	* @param string $type [default: category, blocks]
	* @return string where
	*/
	protected function get_where_clause($data, $type)
	{
		if ($type === 'category')
		{
			return $this->db->sql_in_set('cat_name', $data);
		}
		else if ($type === 'blocks')
		{
			return $this->db->sql_in_set('block_name', $data);
		}
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
				$this->manager->set($row['cat_name'], [$data['block_name'] => $data['ext_name']]);
			}
		}
		$this->db->sql_freeresult($result);
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
