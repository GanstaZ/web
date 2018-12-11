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
use dls\web\core\helper;

/**
* DLS Web blocks manager
*/
class block_manager
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var blocks table */
	protected $blocks_main;

	/** @var blocks data table */
	protected $blocks_data;

	/** @var \dls\web\core\helper */
	protected $helper;

	/** @var \template_data */
	protected $template_data;

	/** @var array Contains enabled block services */
	protected $blocks;

	protected $tester;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db Db object
	* @param \phpbb\di\service_collection $blocks_data Service container
	* @param string $blocks_main The name of the blocks category table
	* @param string $blocks_data The name of the blocks data table
	* @param \dls\web\core\helper $helper Data helper object
	* @param \template_data $template_data Data helper object
	*/
	public function __construct(driver_interface $db, \phpbb\di\service_collection $blocks_collection, $blocks_main, $blocks_data, helper $helper, template_data $template_data)
	{
		$this->db = $db;
		$this->blocks_main = $blocks_main;
		$this->blocks_data = $blocks_data;
		$this->helper = $helper;
		//$this->template = $template;
		$this->template_data = $template_data;

		$this->register_blocks($blocks_collection);
	}

	public function tester()
	{
		return $this->tester;//['side_blocks'];
	}

	/**
	* Register all enabled blocks
	*
	* @param Service collection of blocks
	* @return null
	*/
	protected function register_blocks($blocks_collection)
	{
		//$sql = 'SELECT *
				//FROM ' . $this->blocks_data . '
				//WHERE active = 1
					//AND position <> 0
				//ORDER BY position';

		$this->blocks = [];

		$sql = 'SELECT b.*, bd.*
				FROM ' . $this->blocks_main . ' b, ' . $this->blocks_data . ' bd
				WHERE b.category_id = bd.category_id
					AND bd.active = 1
					AND bd.position <> 0
				ORDER BY bd.position ASC';
		$result = $this->db->sql_query($sql, 86400);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$data = [
				'block_name' => (string) $row['block_name'],
				'vendor' => (string) $row['vendor'],
				'service' => '',
			];

			$data['block_name'] = $this->helper->is_dls($data);
			$this->blocks[$row['category_name']][$row['block_name']] = $data;
			$block = $blocks_collection[$this->helper->get_service_name($row['block_name'], $row['vendor'])];

			// Validate service name
			if ($block && $this->helper->is_valid_name($block->get_data()))
			{
				$this->blocks[$row['category_name']][$row['block_name']]['service'] = $block;
				$this->tester[$row['category_name']][$row['block_name']] = $data;
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
		$this->template_data->set_data($cat_name, array_column($this->get_block($cat_name), 'vendor', 'block_name'));
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
				$this->template_data->set_data($cat_name, [$is_valid['block_name'] => $is_valid['vendor']]);
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
