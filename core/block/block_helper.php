<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\block;

/**
* DLS Web blocks helper class
*/
class block_helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var blocks table */
	protected $blocks;

	/** @var blocks data table */
	protected $b_data;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var cache_name */
	protected $cache_name;

	/** @var array Contains validated blocks data */
	protected $blocks_data;

	/** @var array Contains filtered blocks data */
	protected $filter_blocks;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db Db object
	* @param string $blocks The name of the blocks category table
	* @param string $b_data The name of the blocks data table
	* @param \phpbb\cache\service $cache A cache instance or null
	* @param string $cache_name The name of the cache variable, defaults to _blocks
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $blocks, $b_data, \phpbb\cache\service $cache = null, $cache_name = '_blocks')
	{
		$this->cache = $cache;
		$this->cache_name = $cache_name;
		$this->db = $db;
		$this->blocks = $blocks;
		$this->b_data = $b_data;

		$this->blocks_data = ($this->cache) ? $this->cache->get($this->cache_name) : false;

		if ($this->blocks_data === false)
		{
			$this->load_blocks();
		}
	}

	/**
	* Loads all active blocks from the database
	*
	* @return null
	*/
	protected function load_blocks()
	{
		$sql = 'SELECT b.*, bd.*
				FROM ' . $this->blocks . ' b, ' . $this->b_data . ' bd
				WHERE b.category_id = bd.category_id
					AND bd.active = 1
					AND bd.position <> 0
				ORDER BY bd.position ASC';
		$result = $this->db->sql_query($sql);

		$this->blocks_data = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->blocks_data[$row['category_name']][$row['block_name']] = [
				'cat_name'	 => (string) $row['category_name'],
				'block_name' => (string) $row['block_name'],
				'active'	 => (bool) $row['active'],
				'vendor'	 => (string) $row['vendor'],
			];
		}
		$this->db->sql_freeresult($result);

		if ($this->cache)
		{
			$this->cache->put($this->cache_name, $this->blocks_data);
		}
	}

	/**
	* Set block/s
	*
	* @param string $name Name of the category
	* @param string|array $data Block data
	* @return void
	*/
	public function set_to_filter($name, $data)
	{
		if (is_string($data))
		{
			$this->filter_blocks[$name][$data] = '';
		}
		else if (is_array($data))
		{
			$this->filter_blocks[$name] = $data;
		}
	}

	/**
	* Get table name
	*
	* @param string $table_name Name of the table we want to use.
	* @return string table name
	*/
	public function get_table($table_name)
	{
		return $this->{$table_name};
	}

	/**
	* Get data from a given category
	*
	* @param string $category Category name
	* @return array $this->blocks_data
	*/
	public function get($category)
	{
		if ($this->blocks_data[$category])
		{
			return $this->blocks_data[$category];
		}
	}

	/**
	* Get template loader vars for enabled blocks from a given category
	*
	* @param string $block_category Category name
	* @return array $blocks_data
	*/
	public function get_vars($block_category)
	{
		$blocks = $this->get($block_category);
		if ($to_filter = $this->filter_blocks[$block_category])
		{
			$blocks = array_intersect_key($blocks, $to_filter);
		}

		$blocks_data = [];
		foreach ($blocks as $data)
		{
			$data['block_name'] = $this->is_dls($data);
			$blocks_data[$data['block_name']] = $data['vendor'];
		}

		return $blocks_data;
	}

	/**
	* Get vendor name
	*
	* @param string $ext_name Name of the extension
	* @return string
	*/
	public function get_vendor($ext_name)
	{
		return strstr($ext_name, '_', true);
	}

	/**
	* Check if our block name is valid
	*
	* @param array $data Stores data that we need to validate
	* @return bool Depending on whether or not the block is valid
	*/
	public function is_valid_name($data)
	{
		$vendor = $this->get_vendor($data['vendor']);
		$validate = utf8_strpos($data['block_name'], $vendor);

		return ($validate !== false) ? true : false;
	}

	/**
	* If extension name is dls, remove prefix.
	*
	* @param array $data Data array
	* @return string $data['block_name']
	*/
	public function is_dls(array $data)
	{
		if ($this->get_vendor($data['vendor']) === 'dls')
		{
			$data['block_name'] = str_replace('dls_', '', $data['block_name']);
		}

		return $data['block_name'];
	}

	/**
	* Check if our block data is valid
	*
	* @param array $data Stores data that we need to validate
	* @return bool Depending on whether or not the block is valid
	*/
	public function is_valid_data(array $data)
	{
		return $this->is_enabled($data['cat_name'], $data['block_name']) && $this->is_valid_name($data);
	}

	/**
	* Check if our block is set & enabled
	*
	* @param string $cat_name Name of the block category
	* @param string $name Name of the block
	* @return bool Depending on whether or not the block is enabled
	*/
	public function is_enabled($cat_name, $name)
	{
		return isset($this->get($cat_name)[$name]) && $this->get($cat_name)[$name];
	}
}
