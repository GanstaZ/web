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

	/** @var array Contains all available blocks */
	protected $collection;

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
	* @param driver_interface	$db			 Database object
	* @param service_collection $collection	 Service collection
	* @param event				$event		 Event object
	* @param string				$blocks_data The name of the blocks data table
	*/
	public function __construct(driver_interface $db, service_collection $collection, event $event, $blocks_data)
	{
		$this->db = $db;
		$this->collection = $collection;
		$this->blocks_data = $blocks_data;
		$this->event = $event;
	}

	/**
	* Get block data
	*
	* @param string $service Service name
	* @return ?object
	*/
	public function get(string $service)
	{
		return self::$blocks[$service] ?? null;
	}

	/**
	* Load blocks
	*
	* @param mixed $data
	* @param string $type [default: cat, block]
	* @return ?void
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
	* Get requested blocks
	*
	* @param mixed $data
	* @param string $type
	* @return array
	*/
	protected function get_blocks($data, $type): array
	{
		$where = (null !== $data) ? $this->where_clause($data, $type) : 'active = 1';

		$sql = 'SELECT block_name, ext_name, active, cat_name
				FROM ' . $this->blocks_data . '
				WHERE ' . $where . '
				ORDER BY position';
		$result = $this->db->sql_query($sql, 86400);

		self::$blocks = $blocks = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$block = $this->collection[$this->get_service($row['block_name'], $row['ext_name'])];

			if ($this->is_special($row))
			{
				self::$blocks[$row['block_name']] = $block;
			}
			else if ($block->is_load_active())
			{
				$blocks[$row['block_name']] = $block;
			}

			if (!$this->is_special($row))
			{
				$block_data = [
					'block_name' => (string) $row['block_name'],
					'ext_name'	 => (string) $row['ext_name'],
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
	* @return bool Depending on whether or not the block is valid
	*/
	public function is_special(array $row): bool
	{
		return $row['cat_name'] === 'special' ?? false;
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
		return utf8_strpos($data['block_name'], $this->get_vendor($data['ext_name'])) !== false ?? false;
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
