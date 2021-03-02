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
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\user;

/**
* DLS Web helper class
*/
class helper
{
	/** @var cache */
	protected $cache;

	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var user */
	protected $user;

	/** @var page table */
	protected $page_data;

	/** @var array allow */
	protected $allow = [];

	/**
	* Constructor
	*
	* @param cache			  $cache	 Cache object
	* @param config			  $config	 Config object
	* @param driver_interface $db		 Database object
	* @param user			  $user		 User object
	* @param string			  $page_data The name of the page data table
	*/
	public function __construct(cache $cache, config $config, driver_interface $db, user $user, $page_data)
	{
		$this->cache	 = $cache;
		$this->config	 = $config;
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

		// Do we have special pages, that can load all pages (controllers/routes)
		$this->check_allowed_condition();

		if ($page_name === 'app')
		{
			$get_last  = end($on_page);
			$page_name = count($on_page) > 2 && is_numeric($get_last) ? $on_page[1] : $get_last;
			$page_name = isset($on_page[1]) && $this->allow($on_page[1]) ? $on_page[1] : $page_name;
		}

		return !$this->is_cp($page_name) ? $this->get($page_name) : [];
	}

	/**
	* Check, if we are in cps or not
	*
	* @param string $page_name Current page name
	* @return bool
	*/
	protected function is_cp(string $page_name): bool
	{
		return $this->user->page['page_dir'] === 'adm' || $page_name === 'mcp' || $page_name === 'ucp';
	}

	/**
	* Check, if page is in allowed array or not
	*
	* @param string $name Current page name
	* @return bool
	*/
	protected function allow(string $name): bool
	{
		return isset($this->allow[$name]) || array_key_exists($name, $this->allow);
	}

	/**
	* Check, if page is special/allowed
	*
	* @return void
	*/
	protected function check_allowed_condition(): void
	{
		$sql = 'SELECT name, allow
				FROM ' . $this->page_data . '
				WHERE active = 1
				ORDER BY id';
		$result = $this->db->sql_query($sql, 300);

		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['allow'])
			{
				$this->allow[$row['name']] = $row['allow'];
			}
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* Get page data
	*
	* @param string $name Page name
	* @return array
	*/
	public function get(string $name): array
	{
		$enabled = [];
		foreach (['dls_special', 'dls_right', 'dls_left', 'dls_middle', 'dls_top', 'dls_bottom'] as $section)
		{
			if ($this->config[$section])
			{
				$enabled[] = $section;
			}
		}

		$enabled = implode($this->user->lang['COMMA_SEPARATOR'], $enabled);

		if (($pages = $this->cache->get('_dls_pages')) === false)
		{
			$sql = 'SELECT name, ' . $enabled . '
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
