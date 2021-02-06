<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
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
	protected $type = ['page', 'cat', 'block'];

	/** @var array Contains validated data for installation */
	protected $install;

	/** @var string active */
	protected $active;

	/** @var array Contains validated block services */
	protected static $blocks = false;

	/**
	* Constructor
	*
	* @param driver_interface	$db			 Database object
	* @param service_collection $collection	 Service collection
	* @param event				$event		 Event object
	* @param string				$blocks_data The name of the blocks data table
	*/
	public function __construct(driver_interface $db, service_collection $collection, event $event, $blocks_data)
	{
		$this->db = $db;
		$this->blocks_data = $blocks_data;
		$this->event = $event;

		$this->register($collection);
	}

	/**
	* Register all available blocks
	*
	* @param Service collection of blocks
	*/
	protected function register($collection): void
	{
		// Get all blocks
		$sql = 'SELECT block_name, ext_name
				FROM ' . $this->blocks_data . '
				ORDER BY block_id';
		$result = $this->db->sql_query($sql, 86400);

		self::$blocks = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$block = $collection[$this->get_service($row['block_name'], $row['ext_name'])];

			if ($block)
			{
				self::$blocks[$row['block_name']] = $block;
			}
		}
		$this->db->sql_freeresult($result);

		$this->check_for_new_blocks($collection);
	}

	/**
	* Check for new block/s
	*
	* @param Service collection of blocks
	* @return void
	*/
	public function check_for_new_blocks($collection)
	{
		$this->install = [];
		foreach ($collection as $service)
		{
			$data = $service->get_data();

			// Validate data and set it for installation
			if ($this->is_valid($data) && !array_key_exists($data['block_name'], self::$blocks))
			{
				$this->install[$data['block_name']] = $data;
			}
		}
	}

	/**
	* Is valid data
	*
	* @param array $row
	* @return bool
	*/
	protected function is_valid($row)
	{
		return is_array($row) && !empty($row['block_name']) && !empty($row['ext_name']) && $this->is_valid_name($row) && !empty($row['cat_name']);
	}

	/**
	* Set active service
	*
	* @param string $service
	* @return manager object
	*/
	public function active(string $service): object
	{
		$this->active = $special;

		return $this;
	}

	/**
	* Get block service
	*
	* @param string $service Service name
	* @return ?object
	*/
	public function get(string $service): object
	{
		return self::$blocks[$service];
	}

	/**
	* Get new blocks for installation
	*
	* @return array
	*/
	public function get_new_blocks(): array
	{
		return $this->install ?? [];
	}

	/**
	* Load blocks
	*
	* @param mixed	$name [string, array, default is null]
	* @param string $type [page, block, default is cat]
	* @return void
	*/
	public function load($name = null, string $type = 'cat'): void
	{
		if (!in_array($type, $this->type))
		{
			return;
		}

		$this->get_blocks($name, $type);
	}

	/**
	* Get requested blocks
	*
	* @param mixed	$name
	* @param string $type
	* @return void
	*/
	protected function get_blocks($name, $type): void
	{
		$where = (null !== $name) ? $this->where_clause($name, $type) : 'active = 1';

		$sql = 'SELECT block_name, ext_name, cat_name
				FROM ' . $this->blocks_data . '
				WHERE ' . $where . '
				ORDER BY position';
		$result = $this->db->sql_query($sql, 86400);

		$blocks_data = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$block = $this->get($row['block_name']);

			if (!$block)
			{
				continue;
			}

			if ($block->is_load_active() && $this->active !== $row['block_name'])
			{
				$blocks_data[$row['block_name']] = $block;
			}

			if (!$this->is_special($row))
			{
				$data = [
					'block_name' => (string) $row['block_name'],
					'ext_name'	 => (string) $row['ext_name'],
				];

				$data['block_name'] = $this->is_dls($data);
				$this->event->set_data($row['cat_name'], [$data['block_name'] => $data['ext_name']]);
			}
		}
		$this->db->sql_freeresult($result);

		$this->loading($blocks_data);
	}

	/**
	* Where clause
	*
	* @param array|string $name
	* @param string		  $type
	* @return string
	*/
	protected function where_clause($name, $type): string
	{
		if (is_array($name))
		{
			return $this->db->sql_in_set("{$type}_name", $name) . ' AND active = 1';
		}
		else if (is_string($name))
		{
			return "{$type}_name = '" . $this->db->sql_escape($name) . "' AND active = 1";
		}
	}

	/**
	* Loading
	*
	* @param array $blocks Array of requested blocks
	* @return void
	*/
	protected function loading(array $blocks): void
	{
		foreach ($blocks as $block)
		{
			$block->load();
		}
	}

	/**
	* Get service name
	*
	* @param string $service  Name of the service
	* @param string $ext_name Name of the extension
	* @param string $search	  Default is underscore
	* @return ?string
	*/
	public function get_service(string $service, string $ext_name, string $search = '_'): ?string
	{
		$start = utf8_strpos($service, $search);
		if (is_bool($start))
		{
			return null;
		}

		return str_replace($search, '.', "{$ext_name}.block." . utf8_substr($service, $start + utf8_strlen($search)));
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
	* Check if our cat name is special
	*
	* @param array $row block data
	* @return bool Depending on whether or not the category is special
	*/
	public function is_special(array $row): bool
	{
		return $row['cat_name'] === 'special';
	}

	/**
	* Get vendor name
	*
	* @param string $ext_name Name of the extension
	* @return string
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
		return utf8_strpos($data['block_name'], $this->get_vendor($data['ext_name'])) !== false;
	}

	/**
	* If extension name is dls, remove prefix
	*
	* @param array $data Data array
	* @return string
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
