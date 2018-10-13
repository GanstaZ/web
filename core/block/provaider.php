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
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var blocks table */
	protected $helper;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var cache_name */
	protected $cache_name;

	/** @var array Contains validated blocks data */
	protected $blocks_data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db	   Db object
	* @param \phpbb\cache\service			   $cache  A cache instance or null
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \dls\web\core\block\data_helper $helper, \phpbb\cache\service $cache = null, $cache_name = '_blocks')
	{
		$this->cache = $cache;
		$this->cache_name = $cache_name;
		$this->db = $db;
		$this->helper = $helper;

		$this->blocks_data = ($this->cache) ? $this->cache->get($this->cache_name) : false;

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
					FROM ' . $this->helper->get('blocks') . ' b, ' . $this->helper->get('b_data') . ' bd
					WHERE b.category_id = bd.category_id
						AND bd.active = 1
				ORDER BY bd.position ASC';
		$result = $this->db->sql_query($sql);

		$this->blocks_data = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			// Validate & assign data
			if ($this->helper->is_valid_name($row))
			{
				$this->blocks_data[$row['category_name']][] = [
					'cat_id'   => (int) $row['category_id'],
					'cat_name' => $row['category_name'],
					'name'	   => $row['block_name'],
					'position' => (int) $row['position'],
					'vendor'   => $row['vendor'],
				];
			}
		}
		$this->db->sql_freeresult($result);

		if ($this->cache)
		{
			$this->cache->put($this->cache_name, $this->blocks_data);
		}
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
		$blocks_data = [];
		foreach ($this->get($block_category) as $data)
		{
			$data['name'] = $this->is_dls($data);
			$blocks_data[$data['name']] = $data['vendor'];
		}

		return $blocks_data;
	}

	/**
	* If extension name is dls, remove prefix.
	*
	* @param array $data Data array
	* @return $data['name']
	*/
	public function is_dls($data)
	{
		if ($this->helper->get_vendor_name($data['vendor']) === 'dls')
		{
			$data['name'] = str_replace('dls_', '', $data['name']);
		}

		return $data['name'];
	}
}
