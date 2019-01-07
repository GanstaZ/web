<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugins\astro\logy;

/**
* DLS Web zodiac
*/
class zodiac extends base
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var zodiac table */
	protected $zodiac;

	/** @var zodiac data table */
	protected $zodiac_data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db			Db object
	* @param string							   $zodiac		Zodiac table
	* @param string							   $zodiac_data Data table
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $zodiac, $zodiac_data)
	{
		$this->db = $db;
		$this->zodiac = $zodiac;
		$this->zodiac_data = $zodiac_data;
	}

	/**
	* {@inheritdoc}
	*/
	public function load($dob)
	{
		// Do the sql thang
		$sql = 'SELECT z.*, zd.*
					FROM ' . $this->zodiac . ' z, ' . $this->zodiac_data . ' zd
					WHERE z.zodiac_id = zd.zid';
		$result = $this->db->sql_query($sql, 3600);

		$array = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($this->compare($dob, $row))
			{
				$array[] = $this->get_data($row);
			}
		}
		$this->db->sql_freeresult($result);

		return $array;
	}

	/**
	* Compare zodiac dates
	*
	* @param string $dob date of birth (day and month)
	* @param array	$row Array of dates
	*
	* @return bool
	*/
	protected function compare($dob, $row)
	{
		return $dob >= $row['start'] && $dob <= $row['end'];
	}

	/**
	* {@inheritdoc}
	*/
	public function get_data($row)
	{
		return [
			'sign'	=> $row['sign'],
			'plant' => $row['plant'],
			'gems'	=> $row['gems'],
			'ruler' => $row['ruler'],
			'moon'  => $row['ext'],
			'extra' => $this->elements[(int) $row['enr']],
			'name'	=> $this->types[(int) $row['type']],
		];
	}
}
