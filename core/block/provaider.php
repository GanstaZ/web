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
* DLS Web blocks data provaider class
*/
class provaider
{
	/** @var \phpbb\config\config */
	protected $config;

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

	/**
	* Constructor
	*
	* @param \phpbb\config\config			   $config Config object
	* @param \phpbb\db\driver\driver_interface $db	   Db object
	* @param \phpbb\cache\service			   $cache  A cache instance or null
	* @param string							   $blocks Blocks category table
	* @param string							   $b_data Blocks data table
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $blocks, $b_data, \phpbb\cache\service $cache = null, $cache_name = '_blocks')
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->blocks = $blocks;
		$this->b_data = $b_data;

		$this->blocks_data = ($this->cache) ? $this->cache->get($cache_name) : false;

		if ($this->blocks_data === false)
		{
			$this->load_blocks();
		}
	}

	/**
	* Loads all block information from the database
	*
	* @return null
	*/
	protected function load_blocks()
	{
		$sql = 'SELECT b.*, bd.*
					FROM ' . $this->blocks . ' b, ' . $this->b_data . ' bd
					WHERE b.category_id = bd.category_id
				ORDER BY bd.position ASC';
		$result = $this->db->sql_query($sql, 3600);

		$this->blocks_data = $acp_data_ary = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			// Validate & assign data
			if ($this->is_valid_name($row))
			{
				$acp_data_ary['acp'][] = ['name' => $row['block_name']];

				$this->blocks_data['blocks'][$row['category_name']][] = [
					'cat_id'   => (int) $row['category_id'],
					'cat_name' => $row['category_name'],
					'name'	   => $row['block_name'],
					'position' => (int) $row['position'],
					'vendor'   => $row['vendor'],
				];
			}
		}
		$this->db->sql_freeresult($result);

		$this->blocks_data = array_merge($this->blocks_data, $acp_data_ary);

		if ($this->cache)
		{
			$this->cache->put($cache_name, $this->blocks_data);
		}
	}

	/**
	* Get data from a given category
	*
	* @param string $category Category name
	*
	* @return array $this->blocks_data
	*/
	public function get($category, $name = null)
	{
		if ($this->blocks_data[$category])
		{
			if ($this->blocks_data[$category][$name])
			{
				return $this->blocks_data[$category][$name];
			}

			return $this->blocks_data[$category];
		}
	}

	/**
	* Get template loader vars for enabled blocks from a given category
	*
	* @param string $block_category Category name
	*
	* @return array $blocks_data
	*/
	public function get_vars($block_category)
	{
		$blocks_data = [];
		foreach ($this->get('blocks', $block_category) as $data)
		{
			// Validate & assign validated service data
			if ($this->is_valid_data($data))
			{
				$data['name'] = $this->is_dls($data);

				$blocks_data[$data['name']] = $data['vendor'];
			}
		}

		return $blocks_data;
	}

	/**
	* Check if our block is set & enabled in config
	*
	* @param string $config_name Name of the block.
	*
	* @return bool
	*/
	protected function is_enabled($config_name)
	{
		return isset($this->config[$config_name]) && $this->config[$config_name];
	}

	/**
	* Get vendor name
	*
	* @param string $ext_name Name of the extension.
	*
	* @return string
	*/
	public function get_vendor_name($ext_name)
	{
		return strstr($ext_name, '_', true);
	}

	/**
	* Check if our block name is valid
	*
	* @param string $data Stores data that we need to validate.
	*
	* @return bool Depending on whether or not the block is valid
	*/
	protected function is_valid_name($data)
	{
		$vendor = $this->get_vendor_name($data['vendor']);
		$validate = utf8_strpos($data['block_name'], $vendor);

		return ($validate !== false) ? true : false;
	}

	/**
	* Check if our acp block data is valid & enabled
	*
	* @param array $data Stores data that we need to validate.
	*
	* @return bool Depending on whether or not the block is valid
	*/
	protected function is_valid_data($data)
	{
		return $this->is_enabled($data['name']);
	}

	/**
	* Check if our block service is valid & enabled
	*
	* @param string $data Stores data that we need to validate.
	*
	* @return bool Depending on whether or not the block service is valid
	*/
	public function is_valid_service($data)
	{
		return $this->is_enabled($data['block_name']) && $this->is_valid_name($data);
	}

	/**
	* If extension name is dls, remove prefix.
	*
	* @param array $data Data array
	*
	* @return $data['name']
	*/
	public function is_dls($data)
	{
		if ($this->get_vendor_name($data['vendor']) === 'dls')
		{
			$data['name'] = str_replace('dls_', '', $data['name']);
		}

		return $data['name'];
	}

	/**
	* Count data
	*
	* @param  array		 $data Data
	* @param  string	 $column Column
	* @param  string|int $field	 Field
	* @return int
	*/
	public function count($data, $column, $field)
	{
		return count(array_keys(array_column($data, $column), $field));
	}

	/**
	* Get position options
	*
	* @param  int	 $max Highest number to use in a loop
	* @param  int	 $current_position Current position of a block
	* @return string $options
	*/
	public function get_options($max, $current_position)
	{
		$options = '';
		foreach ($max as $pos)
		{
			$s_selected = ($pos == $current_position) ? ' selected="selected"' : '';
			$options .= '<option value="' . $pos . '"' . $s_selected . '>' . $pos . '</option>';
		}

		return $options;
	}
}
