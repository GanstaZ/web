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

	/** @var array sections */
	protected $sections = ['special', 'right', 'bottom', 'left', 'top', 'middle'];

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
			$block = $this->collection[$this->get_service_name($row['block_name'], $row['ext_name'])];

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
	* Get error log for invalid block names
	*
	* @return array
	*/
	public function get_error_log(): array
	{
		return $this->error ?? [];
	}

	/**
	* Check for new block/s
	*
	* @param array	$data_ary
	* @param object $container
	* @return array
	*/
	public function check_for_new_blocks(array $data_ary, object $container): array
	{
		$return = [];
		foreach ($this->collection as $service => $service_data)
		{
			$data = $this->check($service, $service_data->get_block_data(), $container);

			// Validate data and set it for installation
			if ($data && !in_array($data['block_name'], array_column($data_ary, 'block_name')))
			{
				$return[$data['block_name']] = $data;
			}
		}

		return $return ?? [];
	}

	/**
	* Check conditioning
	*
	* @param string $service   Name of the service
	* @param array	$row	   Data array
	* @param object $container
	* @return array
	*/
	public function check(string $service, array $row, object $container): array
	{
		$this->_section($service, $row['cat_name']);
		$this->_ext_name($service, $row['ext_name'], $container);

		$row['block_name'] = str_replace(
			utf8_substr($row['ext_name'], utf8_strpos($row['ext_name'], '_') + 1) . '_block_',
			'',
			str_replace('.', '_', $service)
		);

		$this->_block_name($service, $row, $container);

		return $this->is_valid($service) ? $data = [
			'block_name' => $row['block_name'],
			'cat_name'	 => $row['cat_name'],
			'ext_name'	 => $row['ext_name'],
		] : [];
	}

	/**
	* Is valid data
	*
	* @param string $service
	* @return bool
	*/
	public function is_valid($service): bool
	{
		return empty($this->error[$service]) ?? false;
	}

	/**
	* Get service name
	*
	* @param string $service  Name of the service
	* @param string $ext_name Name of the extension
	* @return string
	*/
	public function get_service_name(string $service, string $ext_name): string
	{
		return str_replace('_', '.', "{$ext_name}.block." . utf8_substr($service, utf8_strpos($service, '_') + 1));
	}

	/**
	* Check if section is valid
	*
	* @param string $service Service name
	* @param string $section Section name
	* @return void
	*/
	protected function _section(string $service, string $section): void
	{
		if (!in_array($section, $this->sections))
		{
			$this->error[$service]['cat_name'] = $section;

			if (empty($section))
			{
				$this->error[$service]['cat_name'] = 'VAR_EMPTY';
			}
		}
	}

	/**
	* Check if ext_name is valid
	*
	* @param string $service   Name of the service
	* @param string $ext_name  Name of the extension
	* @param object $container
	* @return void
	*/
	protected function _ext_name($service, string $ext_name, $container): void
	{
		if (!$container->get('ext.manager')->is_enabled(str_replace('_', '/', $ext_name)))
		{
			$this->error[$service]['ext_name'] = $ext_name;
			$this->error[$service]['service'] = 'PRE_ERROR';

			if (empty($ext_name))
			{
				$this->error[$service]['ext_name'] = 'VAR_EMPTY';
				unset($this->error[$service]['service']);
			}
		}
	}

	/**
	* Check if block service name is valid
	*
	* @param string $service   Name of the service
	* @param array	$row	   Data array
	* @param object $container
	* @return void
	*/
	protected function _block_name($service, $row, $container): void
	{
		if (isset($this->error[$service]['cat_name']))
		{
			$this->error[$service]['error'] = 'NOT_AVAILABLE';
		}

		if (!$container->has($this->get_service_name($row['block_name'], $row['ext_name'])))
		{
			if (empty($this->error[$service]['service']))
			{
				$this->error[$service]['service'] = 'SER_ERROR';
			}

			$this->error[$service]['error'] = 'NOT_AVAILABLE';
		}
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
