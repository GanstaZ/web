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

/**
* DLS Web blocks manager
*/
class manager
{
	/** @var blocks data table */
	protected $blocks_data;

	/** @var \dls\web\core\blocks\event */
	protected $event;

	/** @var array Contains validated block services */
	protected static $blocks = false;

	/** @var array Contains info about current status */
	protected $info;

	/**
	* Constructor
	*
	* @param string $blocks_data The name of the blocks data table
	* @param \dls\web\core\blocks\event $event Data object
	*/
	public function __construct(\phpbb\di\service_collection $blocks_collection, $blocks_data, event $event)
	{
		$this->blocks_data = $blocks_data;
		$this->event = $event;

		$this->register_validated_blocks($blocks_collection);
	}

	/**
	* Set event data
	*
	* @param string $cat_name
	* @param array $blocks
	* @return void
	*/
	public function set($cat_name, $blocks)
	{
		$this->event->set_data($cat_name, $blocks);
	}

	/**
	* Get status info
	*
	* @param string $status
	* @return array
	*/
	public function info($status)
	{
		return ($this->info[$status]) ? $this->info[$status] : [];
	}

	/**
	* Register all validated blocks
	*
	* @param Service collection of blocks
	* @return null
	*/
	protected function register_validated_blocks($blocks_collection)
	{
		self::$blocks = [];
		foreach ($blocks_collection as $block)
		{
			$data = $block->get_data();

			if ($this->is_valid_name($data))
			{
				self::$blocks[$data['block_name']] = $block;
			}
		}
	}

	/**
	* Blocks data
	*
	* @return string blocks data table
	*/
	public function blocks_data()
	{
		return $this->blocks_data;
	}

	/**
	* Get block data
	*
	* @param string $service Service name
	* @return object
	*/
	public function get($service)
	{
		if (self::$blocks[$service])
		{
			return self::$blocks[$service];
		}
	}

	/**
	* Check for new blocks
	*
	* @param array $rowset
	* @return array
	*/
	public function status_available($rowset)
	{
		$new_blocks = [];
		foreach (self::$blocks as $new_block)
		{
			$new = $new_block->get_data();

			if (!$rowset[$new['cat_name']][$new['block_name']])
			{
				$this->info['update'][] = $new['block_name'];
				$new_blocks[] = [
					'block_name' => $new['block_name'],
					'ext_name'	 => $new['ext_name'],
					'position'	 => 0,
					'active'	 => 0,
					'cat_name'   => $new['cat_name'],
				];
			}
		}

		return $new_blocks;
	}

	/**
	* Check for unavailable blocks
	*
	* @param ContainerInterface $container A container
	* @param array $row The name of the block we want to remove
	* @return void
	*/
	public function status_unavailable($container, $row)
	{
		$service = $this->get_service_name($row['block_name'], $row['ext_name']);
		if (!$container->has($service))
		{
			$this->info['remove'][] = $row['block_name'];
		}
	}

	/**
	* Check for update/remove status
	*
	* @param array $info
	* @return string
	*/
	public function check_status()
	{
		if (!$this->info)
		{
			return;
		}
		else if ($this->info['update'])
		{
			$status = 'update';
		}
		else if ($this->info['remove'])
		{
			$status = 'remove';
		}

		return $status;
	}

	/**
	* Get vendor name
	*
	* @param string $ext_name Name of the extension
	* @return string
	*/
	public function get_vendor($ext_name)
	{
		//return utf8_substr($ext_name, 0, utf8_strpos($ext_name, '_'));
		return strstr($ext_name, '_', true);
	}

	/**
	* Get block name from service
	*    For example dls.web.block.some.name => dls_some_name
	*
	* @param string $service Name of the service
	* @param string $ext_name Name of the extension
	* @param string $remove Remove part of the string
	* @param mixed $replace Default is underscore
	* @return string block name
	*/
	public function get_block_name($service, $ext_name, $remove = '.block.', $replace = '_')
	{
		$start = utf8_strpos($ext_name, $replace);
		if (!is_bool($start))
		{
			$string = utf8_substr($ext_name, $start + utf8_strlen($replace));

			return str_replace("{$string}{$remove}", $replace, $service);
		}
	}

	/**
	* Get service name
	*
	* @param string $service Name of the service
	* @param string $ext_name Name of the extension
	* @param string $insert Insert a string part
	* @param mixed $search Default is underscore
	* @param mixed $replace Default is dot
	* @return string service name
	*/
	public function get_service_name($service, $ext_name, $insert = '.block.', $search = '_', $replace = '.')
	{
		$start = utf8_strpos($service, $search);
		if (!is_bool($start))
		{
			$string = utf8_substr($service, $start + utf8_strlen($search));

			return str_replace($search, $replace, "{$ext_name}{$insert}{$string}");
		}
	}

	/**
	* Check if our block name is valid
	*
	* @param array $data Stores data that we need to validate
	* @return bool Depending on whether or not the block is valid
	*/
	public function is_valid_name(array $data)
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
	public function is_dls(array $data)
	{
		if ($this->get_vendor($data['ext_name']) === 'dls')
		{
			$data['block_name'] = str_replace('dls_', '', $data['block_name']);
		}

		return $data['block_name'];
	}
}
