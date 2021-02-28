<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core;

use phpbb\cache\service as cache;
use phpbb\db\driver\driver_interface;
use phpbb\user;

/**
* DLS Web helper class
*/
class helper
{
	/** @var cache */
	protected $cache;

	/** @var driver_interface */
	protected $db;

	/** @var user */
	protected $user;

	/** @var page table */
	protected $page_data;

	/**
	* Constructor
	*
	* @param cache			  $cache	 Cache object
	* @param driver_interface $db		 Database object
	* @param user			  $user		 User object
	* @param string			  $page_data The name of the page data table
	*/
	public function __construct(cache $cache, driver_interface $db, user $user, $page_data)
	{
		$this->cache	 = $cache;
		$this->db		 = $db;
		$this->user		 = $user;
		$this->page_data = $page_data;
	}

	/**
	* Get page data for blocks loader
	*
	* @return array
	*/
	public function get_page_data(): array
	{
		$on_page = explode('/', str_replace('.php', '', $this->user->page['page_name']));
		$page_name = $on_page[0];

		if ($page_name === 'app')
		{
			$get_last  = end($on_page);
			$page_name = count($on_page) > 2 && is_numeric($get_last) ? $on_page[1] : $get_last;
		}

		return $this->get($page_name);
	}

	/**
	* Get page data
	*
	* @param string $name Page name
	* @return array
	*/
	public function get(string $name = null): array
	{
		if (($pages = $this->cache->get('_dls_pages')) === false)
		{
			$sql = 'SELECT name, dls_special, dls_right, dls_left, dls_middle, dls_top, dls_bottom
					FROM ' . $this->page_data . '
					WHERE active = 1
					ORDER BY id';
			$result = $this->db->sql_query($sql);

			$pages = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				// Filter out inactive sections & unset page name, as we don't need it in data array
				$data = array_keys(array_filter($row));
				unset($data[0]);

				$pages[$row['name']] = $data;
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_dls_pages', $pages);
		}

		return $pages[$name] ?? [];
	}
}
