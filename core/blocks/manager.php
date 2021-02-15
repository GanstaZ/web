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

	/** @var array Contains all available blocks */
	protected $collection;

	/** @var event */
	protected $event;

	/** @var blocks data table */
	protected $blocks_data;

	/** @var array type */
	protected $type = ['page', 'cat', 'block'];

	/** @var array error */
	protected $error = [];

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
	* Check for new block/s
	*
	* @param array	$block_ary
	* @param object $container
	* @return array
	*/
	public function check_for_new_blocks(array $block_ary, object $container): array
	{
		$return = [];
		foreach ($this->collection as $service => $service_data)
		{
			$data = $service_data->get_block_data();
			$data['block_name'] = $this->get_block_name($service, $data['ext_name'], $container);

			// Validate data and set it for installation
			if ($this->is_valid($data) && !in_array($data['block_name'], array_column($block_ary, 'block_name')))
			{
				$return[$data['block_name']] = $data;
			}
		}

		return $return ?? [];
	}

	/**
	* Get error log for invalid block names
	*
	* @return array
	*/
	public function get_error_log(): array
	{
		return $this->error ?? [];
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

		if ($blocks = $this->get_blocks($name, $type))
		{
			$this->loading($blocks);
		}
	}

	/**
	* Get requested blocks
	*
	* @param mixed	$name
	* @param string $type
	* @return array
	*/
	protected function get_blocks($name, $type): array
	{
		$where = (null !== $name) ? $this->where_clause($name, $type) : 'active = 1';

		$sql = 'SELECT block_name, ext_name, cat_name
				FROM ' . $this->blocks_data . '
				WHERE ' . $where . '
				ORDER BY position';
		$result = $this->db->sql_query($sql, 86400);

		self::$blocks = $blocks_data = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$block = $this->collection[$this->get_service($row['block_name'], $row['ext_name'])];

			// If is set as special, then we can call it with get method
			if ($block->is_load_special())
			{
				self::$blocks[$row['block_name']] = $block;
			}

			// If is set as active, then load method will handle it
			if ($block->is_load_active())
			{
				$blocks_data[$row['block_name']] = $block;
			}

			// This is for twig blocks tag
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

		return $blocks_data;
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
	protected function loading($blocks): void
	{
		foreach ($blocks as $block)
		{
			$block->load();
		}
	}

	/**
	* Get block name from service
	*	 For example acme.demo.block.some.name => acme_some_name
	*
	* @param string $service  Name of the service
	* @param string $ext_name Name of the extension
	* @param object $container
	* @return string
	*/
	public function get_block_name(string $service, string $ext_name, object $container): string
	{
		$string	  = str_replace('.', '_', $service);
		$ext_name = $this->is_valid_ext_name($string, $ext_name) ? $ext_name : 'acme_demo';
		$clean	  = str_replace(utf8_substr($ext_name, utf8_strpos($ext_name, '_') + 1) . '_block_', '', $string);

		if (!$container->has($this->get_service($clean, $ext_name)))
		{
			$this->error[$clean] = 'Service name is not valid';

			return '';
		}

		return $this->is_valid_name(['block_name' => $clean, 'ext_name' => $ext_name]) ? $clean : '';
	}

	/**
	* Get service name
	*
	* @param string $service  Name of the service
	* @param string $ext_name Name of the extension
	* @return string
	*/
	public function get_service(string $service, string $ext_name): string
	{
		return str_replace('_', '.', "{$ext_name}.block." . utf8_substr($service, utf8_strpos($service, '_') + 1));
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
	protected function is_special(array $row): bool
	{
		return $row['cat_name'] === 'special';
	}

	/**
	* Is valid data
	*
	* @param array $row
	* @return bool
	*/
	protected function is_valid($row)
	{
		return is_array($row) && !empty($row['block_name']) && !empty($row['ext_name']) && !empty($row['cat_name']);
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
	* Check if our ext_name is valid
	*
	* @param string $string	  Name of the service
	* @param string $ext_name Name of the extension
	* @return bool
	*/
	protected function is_valid_ext_name(string $string, string $ext_name): bool
	{
		return strcmp($string, $ext_name . '_' . utf8_substr($string, utf8_strlen($ext_name) + 1)) === 0;
	}

	/**
	* Check if our block name is valid
	*
	* @param array $data Stores data that we need to validate
	* @return bool Depending on whether or not the block is valid
	*/
	protected function is_valid_name(array $data): bool
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
