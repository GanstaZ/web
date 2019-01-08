<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugins\astro\zodiac;

use phpbb\db\driver\driver_interface;

/**
* DLS Web chinese zodiac
*/
class chinese extends base
{
	/** @var driver_interface */
	protected $db;

	/** @var zodiac heavenly stems table */
	protected $zodiac_stems;

	/**
	* Constructor
	*
	* @param driver_interface $db			Database object
	* @param string			  $zodiac_stems Zodiac heavenly stems table
	*/
	public function __construct(driver_interface $db, $zodiac_stems)
	{
		$this->db = $db;
		$this->zodiac_stems = $zodiac_stems;
	}

	/**
	* {@inheritdoc}
	*/
	public static function astro_data(): array
	{
		return [
			'type' => 'zodiac',
			'name' => 'chinese',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load($year)
	{
		// Twelve earthly branches
		$animals = ['PIG', 'RAT', 'OX', 'TIGER', 'RABBIT', 'DRAGON', 'SNAKE', 'HORSE', 'GOAT', 'MONKEY', 'ROOSTER', 'DOG'];

		// Convert Gregorian year into sexagenary cycle number
		$year = $this->get_sexagenary_cycle_number($year);
		$get = $year % 12;

		if (isset($animals[$get]) || array_key_exists($get, $animals))
		{
			return [$this->get_data([
				'sign' => $animals[$get],
				'enr'  => $year,
				'type' => 5,
			])];
		}
	}

	/**
	* Get stem
	*
	* @param int $year Year is equivalent to one of the sexagenary cycle number
	*
	* @return string $stem
	*/
	protected function get_stem($year)
	{
		// Ten heavenly stems & their cycle numbers (number 0 is equivalent to 60)
		$sql = 'SELECT stem, a_id, b_id, c_id, d_id, e_id, f_id
				FROM ' . $this->zodiac_stems . '
				WHERE a_id = ' . (int) $year . '
					OR b_id = ' . (int) $year . '
					OR c_id = ' . (int) $year . '
					OR d_id = ' . (int) $year . '
					OR e_id = ' . (int) $year . '
					OR f_id = ' . (int) $year;
		$result = $this->db->sql_query($sql, 3600);
		//$row = $this->db->sql_fetchrow($result);
		$row = $this->db->sql_fetchfield('stem');
		$this->db->sql_freeresult($result);

		return (!$row) ? false : $row;
	}

	/**
	* To find out the Gregorian year's equivalent in the sexagenary cycle use the appropriate method below.
	*
	* For any year number greater than 4 AD, the equivalent sexagenary year can be found by
	*	  subtracting 3 from the Gregorian year, dividing by 60 and taking the remainder.
	* For any year before 1 AD, the equivalent sexagenary year can be found by adding 2 to the Gregorian year number (in BC),
	*	  dividing it by 60, and subtracting the remainder from 60. See example below.
	* 1 AD, 2 AD and 3 AD correspond respectively to the 58th, 59th and 60th years of the sexagenary cycle.
	*
	* @param string $year Year to calculate cycle
	*
	* @return float
	*/
	public function get_sexagenary_cycle_number($year)
	{
		if ($year < 0)
		{
			return 60 - ($this->cycle_formula(abs($year) + 2));
		}

		return $this->cycle_formula($year - 3);
	}

	/**
	* Sexagenary cycle formula
	*
	* @param string $year Year to calculate cycle
	*
	* @return float
	*/
	protected function cycle_formula($year)
	{
		return $year - (60 * (floor($year / 60)));
	}

	/**
	* {@inheritdoc}
	*/
	public function get_data($row)
	{
		return [
			'sign'	=> $row['sign'],
			'plant' => '',
			'gems'	=> '',
			'ruler' => '',
			'extra' => $this->get_stem((int) $row['enr']),
			'name'	=> $this->types[(int) $row['type']],
		];
	}
}
