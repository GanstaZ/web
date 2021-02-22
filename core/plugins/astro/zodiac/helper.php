<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugins\astro\zodiac;

use phpbb\cache\service as cache;
use phpbb\db\driver\driver_interface;

/**
* DLS Web zodiac
*/
class helper
{
	/** @var cache */
	protected $cache;

	/** @var driver_interface */
	protected $db;

	/** @var zodiac table */
	protected $zodiac;

	/** @var zodiac dates table */
	protected $zodiac_dates;

	/** @var int type */
	protected $type = 6;

	/**
	* Constructor
	*
	* @param cache			  $cache		Cache object
	* @param driver_interface $db			Database object
	* @param string			  $zodiac		Zodiac table
	* @param string			  $zodiac_dates Data table
	*/
	public function __construct(cache $cache, driver_interface $db, $zodiac, $zodiac_dates)
	{
		$this->cache = $cache;
		$this->db = $db;
		$this->zodiac = $zodiac;
		$this->zodiac_dates = $zodiac_dates;
	}

	/**
	* Get zodiac data
	*
	* @param null|int $type zodiac type
	* @return array
	*/
	function zodiac_data($type = null): array
	{
		if (($zodiac = $this->cache->get('_zodiac')) === false)
		{
			$sql = 'SELECT z.*, zd.*
					FROM ' . $this->zodiac . ' z, ' . $this->zodiac_dates . ' zd
					WHERE z.sign_id = zd.zid';
			$result = $this->db->sql_query($sql);

			$zodiac = [];
			while ($row = $this->db->sql_fetchrow($result))
			{
				$select = (int) $row['sign_id'];

				if ((int) $row['type'] === $this->type)
				{
					$select = (string) $row['ext'];
				}

				$zodiac[(int) $row['type']][$select] = array_filter($row);
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_zodiac', $zodiac);
		}

		return $zodiac[(int) $type] ?? $zodiac;
	}

	/**
	* Filter data
	*
	* @param array $allowed
	* @return array filtered data
	*/
	public function filter(array $allowed): array
	{
		return array_intersect_key($this->zodiac_data(), array_flip($allowed));
	}
}
