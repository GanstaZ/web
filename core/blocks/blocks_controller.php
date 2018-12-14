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

		$this->register_blocks();
	}

	/**
	* Register all enabled blocks
	*
	* @param Service collection of blocks
	* @return null
	*/
	protected function register_blocks()
	{
		$this->blocks = [];

		$sql = 'SELECT *
				FROM ' . $this->manager->blocks_data() . '
				WHERE active = 1
					AND position <> 0
				ORDER BY position';
		$result = $this->db->sql_query($sql, 86400);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$data = [
				'block_name' => (string) $row['block_name'],
				'ext_name' => (string) $row['ext_name'],
				'service' => '',
			];

			$data['block_name'] = $this->manager->is_dls($data);
			$this->blocks[$row['cat_name']][$row['block_name']] = $data;

			// Validate service name
			if ($block = $this->manager->get($row['block_name']))
			{
				$this->blocks[$row['cat_name']][$row['block_name']]['service'] = $block;
			}
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* Load blocks
	*
	* @param mixed $cat_name
	* @param string $type [default: category, blocks]
	* @return null
	*/
	public function load($cat_name = null, string $type = 'category')
	{
		if ($blocks = $this->get_blocks($cat_name, $type))
		{
			$this->loading($blocks);
		}
	}

	/**
	* Get block data
	*
	* @param string $cat_name
	* @return array
	*/
	protected function get_block($cat_name)
	{
		if ($this->blocks[$cat_name])
		{
			return $this->blocks[$cat_name];
		}
	}

	/**
	* Set category template data
	*
	* @param string $cat_name
	* @return void
	*/
	protected function set_data($cat_name)
	{
		$this->manager->set($cat_name, array_column($this->get_block($cat_name), 'ext_name', 'block_name'));
	}

	/**
	* Get blocks
	*
	* @param mixed $cat_name
	* @param string $type [default: category, blocks]
	* @return array
	*/
	protected function get_blocks($cat_name, $type)
	{
		if (null !== $cat_name && $type === 'category')
		{
			if (is_array($cat_name))
			{
				return $this->get_requested_categories($cat_name);
			}
			$this->set_data($cat_name);

			return $this->get_block($cat_name);
		}

		return $this->get_all_blocks($cat_name, $type);
	}

	/**
	* Get requested categories
	*
	* @param array $categories Array of category names
	* @return array
	*/
	protected function get_requested_categories(array $categories)
	{
		$requested = [];
		foreach ($categories as $cat_name)
		{
			if ($this->get_block($cat_name))
			{
				$this->set_data($cat_name);
				$requested = array_merge($requested, $this->get_block($cat_name));
			}
		}

		return $requested;
	}

	/**
	* Get all blocks
	*
	* @param mixed $cat_name
	* @param string $type [default: category, blocks]
	* @return array
	*/
	protected function get_all_blocks($cat_name, $type)
	{
		$requested = [];
		foreach (array_keys($this->blocks) as $name)
		{
			if (is_null($cat_name))
			{
				$this->set_data($name);
				$requested = array_merge($requested, $this->get_block($name));
			}
			else if (is_array($cat_name) && $type === 'blocks')
			{
				$requested = array_merge($requested, $this->get_requested_blocks($name, $cat_name));
			}
		}

		return $requested;
	}

	/**
	* Get requested blocks
	*
	* @param string $cat_name
	* @param array $blocks_ary Array of block names
	* @return array
	*/
	protected function get_requested_blocks($cat_name, array $blocks_ary)
	{
		$array = [];
		foreach ($blocks_ary as $block)
		{
			$is_valid = $this->get_block($cat_name)[$block];
			if ($is_valid)
			{
				$this->manager->set($cat_name, [$is_valid['block_name'] => $is_valid['ext_name']]);
				$array[$block] = $this->get_block($cat_name)[$block];
			}
		}

		return $array;
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
			$block['service']->load();
		}
	}
}
