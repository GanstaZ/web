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

/**
* DLS Web zodiac
*/
class zodiac extends base
{
	/** @var helper */
	protected $helper;

	/**
	* Constructor
	*
	* @param helper $helper Zodiac Helper object
	*/
	public function __construct(helper $helper)
	{
		$this->helper = $helper;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_name(): string
	{
		return 'zodiac';
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
	public function load(string $dob): array
	{
		$array = [];
		foreach ($this->helper->filter([1, 2, 3, 4]) as $rows)
		{
			foreach ($rows as $row)
			{
				if ($this->compare($dob, $row))
				{
					$array[] = $this->get_data($row);
				}
			}
		}

		return $array;
	}

	/**
	* Compare zodiac dates
	*
	* @param string $dob date of birth (day and month)
	* @param array	$row Array of dates
	* @return bool
	*/
	protected function compare(string $dob, array $row): bool
	{
		[$month, $day] = $this->map($dob);
		$date = $this->map($row['date']);

		return ($month === $date[0] && $day >= $date[1]) || ($month === $date[2] && $day <= $date[3]);
	}

	/**
	* Map data
	*
	* @param string $date start/end date of the zodiac
	* @return array
	*/
	protected function map(string $date): array
	{
		return array_map('intval', explode('-', $date));
	}
}
