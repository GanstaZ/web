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
* DLS Web Chinese zodiac
*/
class chinese extends base
{
	/** @var driver_interface */
	protected $db;

	/** @var helper */
	protected $helper;

	/** @var zodiac heavenly stems table */
	protected $zodiac_stems;

	/**
	* Constructor
	*
	* @param driver_interface $db			 Database object
	* @param helper			  $helper Zodiac Helper object
	* @param string			  $zodiac_stems	 Zodiac heavenly stems table
	*/
	public function __construct(driver_interface $db, helper $helper, $zodiac_stems)
	{
		$this->db = $db;
		$this->helper = $helper;
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
	public function load(string $year): array
	{
		// Twelve earthly branches
		$animals = ['PIG', 'RAT', 'OX', 'TIGER', 'RABBIT', 'DRAGON', 'SNAKE', 'HORSE', 'GOAT', 'MONKEY', 'ROOSTER', 'DOG'];

		// Convert Gregorian year into sexagenary cycle number
		$year = $this->get_sexagenary_cycle_number($year);
		$get = $year % 12;

		if (isset($animals[$get]) || array_key_exists($get, $animals))
		{
			$row = $this->get_stem($year);
			$row['animal'] = $animals[$get];
			$row = array_merge($row, $this->helper->zodiac_data(5)[(int) $row['zid']]);

			return [$this->get_data($row)];
		}
	}

	/**
	* Get stem
	*
	* @param int $year Year is equivalent to one of the sexagenary cycle number
	* @return string $stem
	*/
	public function get_stem(int $year): ?array
	{
		// Ten heavenly stems & their cycle numbers (number 0 is equivalent to 60)
		$sql = 'SELECT snr, enr, zid
				FROM ' . $this->zodiac_stems . '
				WHERE aid = ' . (int) $year . '
					OR bid = ' . (int) $year . '
					OR cid = ' . (int) $year . '
					OR did = ' . (int) $year . '
					OR eid = ' . (int) $year . '
					OR fid = ' . (int) $year;
		$result = $this->db->sql_query($sql, 3600);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row ?? null;
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
	* @return float
	*/
	public function get_sexagenary_cycle_number(string $year): float
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
	* @param int $year Year to calculate cycle
	* @return float
	*/
	protected function cycle_formula(int $year): float
	{
		return $year - (60 * (floor($year / 60)));
	}
}
