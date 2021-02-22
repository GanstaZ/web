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

use phpbb\db\driver\driver_interface;

/**
* DLS Web Chinese zodiac
*/
class chinese extends base
{
	/** @var driver_interface */
	protected $db;

	/** @var zodiac symbols table */
	protected $zodiac_symbols;

	/** @var zodiac heavenly stems table */
	protected $zodiac_stems;

	/**
	* Constructor
	*
	* @param driver_interface $db			  Database object
	* @param string			  $zodiac_symbols Zodiac symbols table
	* @param string			  $zodiac_stems	  Zodiac heavenly stems table
	*/
	public function __construct(driver_interface $db, $zodiac_symbols, $zodiac_stems)
	{
		$this->db = $db;
		$this->zodiac_stems = $zodiac_stems;
		$this->zodiac_symbols = $zodiac_symbols;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_name(): string
	{
		return 'chinese';
	}

	/**
	* {@inheritdoc}
	*/
	public function get_type(): string
	{
		return 'zodiac';
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
			$row['sign'] = $animals[$get];

			return [$this->get_data($row)];
		}
	}

	/**
	* Get stem
	*
	* @param int $year Year is equivalent to one of the sexagenary cycle number
	* @return array $row
	*/
	public function get_stem(int $year): ?array
	{
		// Ten heavenly stems & their cycle numbers (number 0 is equivalent to 60)
		$sql = 'SELECT s.snr, s.enr, s.sid, zs.symbol, zs.type, zs.ruler, zs.ext, zs.dir
				FROM ' . $this->zodiac_stems . ' s, ' . $this->zodiac_symbols . ' zs
				WHERE s.sid = zs.symbol_id AND s.aid = ' . (int) $year . '
					OR s.bid = ' . (int) $year . '
					OR s.cid = ' . (int) $year . '
					OR s.did = ' . (int) $year . '
					OR s.eid = ' . (int) $year . '
					OR s.fid = ' . (int) $year;
		$result = $this->db->sql_query($sql, 86400);
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
