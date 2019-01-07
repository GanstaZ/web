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
use phpbb\di\service_collection;

/**
* DLS Web blocks manager
*/
class manager
{
	/** @var driver_interface */
	protected $db;

	/** @var event */
	protected $event;

	/** @var blocks data table */
	protected $blocks_data;

	/** @var array type */
	protected $type = ['cat', 'block'];

	/** @var array Contains validated block services */
	protected static $blocks = false;

	/**
	* Constructor
	*
	* @param driver_interface	$db				  Database object
	* @param service_collection $block_collection Service collection
	* @param event				$event			  Event object
	* @param string $blocks_data The name of the blocks data table
	*/
	public function __construct(driver_interface $db, service_collection $block_collection, event $event, $blocks_data)
	{
		$this->db = $db;
		$this->blocks_data = $blocks_data;
		$this->event = $event;

		$this->register_blocks($block_collection);
	}

	/**
	* Register all validated blocks
	*
	* @param Service collection of blocks
	* @return void
	*/
	protected function register_blocks($block_collection): void
	{
		$sql = 'SELECT block_name, active
				FROM ' . $this->blocks_data . '
				ORDER BY block_id';
		$result = $this->db->sql_query($sql, 86400);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$rowset[$row['block_name']] = $row['active'];
		}
		$this->db->sql_freeresult($result);

		self::$blocks = [];
		foreach ($block_collection as $block)
		{
			$data = $block->get_data();

			if ($this->is_valid_name($data))
			{
				self::$blocks[$data['block_name']] = (!$rowset[$data['block_name']]) ? $data : $block;
			}
		}
	}

	/**
	* Blocks data table
	*
	* @return string table name
	*/
	public function blocks_data(): string
	{
		return $this->blocks_data;
	}

	/**
	* Get block/s data
	*
	* @param null|string $service Service name
	* @return object|array
	*/
	public function get($service = null)
	{
		if (null !== $service && self::$blocks[$service])
		{
			return self::$blocks[$service];
		}

		return self::$blocks;
	}

	/**
	* Load blocks
	*
	* @param mixed $data
	* @param string $type [default: cat, block]
	* @return null
	*/
	public function load($data = null, string $type = 'cat'): void
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
	protected function get_blocks($data, $type) : array
	{
		$where = (null !== $data) ? $this->where_clause($data, $type) : 'active = 1';

		$sql = 'SELECT *
				FROM ' . $this->blocks_data . '
				WHERE ' . $where . '
				ORDER BY position';
		$result = $this->db->sql_query($sql, 86400);

		$blocks = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($block = $this->get($row['block_name']))
			{
				$blocks[$row['block_name']] = $block;

				$block_data = [
					'block_name' => (string) $row['block_name'],
					'ext_name' => (string) $row['ext_name'],
				];

				$block_data['block_name'] = $this->is_dls($block_data);
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
	protected function where_clause($data, $type): string
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
	protected function loading($blocks): void
	{
		foreach ($blocks as $block)
		{
			$block->load();
		}
	}

	/**
	* Get vendor name
	*
	* @param string $ext_name Name of the extension
	* @return string vendor name
	*/
	public function get_vendor(string $ext_name): string
	{
		return strstr($ext_name, '_', true);
	}

	/**
	* Check if our block name is valid
	*
	* @param array $data Stores data that we need to validate
	* @return bool Depending on whether or not the block is valid
	*/
	public function is_valid_name(array $data): bool
	{
		$ext_name = $this->get_vendor($data['ext_name']);
		$validate = utf8_strpos($data['block_name'], $ext_name);

		return ($validate !== false) ? true : false;
	}

	/**
	* If extension name is dls, remove prefix
	*
	* @param array $data Data array
	* @return string $data['block_name']
	*/
	public function is_dls(array $data): string
	{
		if ($this->get_vendor($data['ext_name']) === 'dls')
		{
			$data['block_name'] = str_replace('dls_', '', $data['block_name']);
		}

		return $data['block_name'];
	}
}
