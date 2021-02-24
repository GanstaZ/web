<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\migrations\v24;

class m4_dls_zodiac extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	static public function depends_on()
	{
		return ['\dls\web\migrations\v24\m1_dls_main'];
	}

	/**
	* Add the initial data in the database
	*
	* @return array Array of table data
	* @access public
	*/
	public function update_data()
	{
		return [
			// Add zodiac data
			['custom', [[$this, 'zodiac_data']]],
			['custom', [[$this, 'zodiac_dates']]],
			['custom', [[$this, 'zodiac_symbols']]],
			['custom', [[$this, 'zodiac_heavenly_stems']]],
		];
	}

	/**
	* Custom function to add data
	*/
	public function zodiac_data()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'dls_zodiac'))
		{
			$sql_ary = [
				// Tropical/Sidereal
				['sign' => 'ARIES',		  'plant'=> 'HONEYSUCKLE',	 'gem' => 'DIAMOND',	'ruler' => 'MARS',	  'ext' => '', 'enr' => 1, 'dir' => 0,],
				['sign' => 'TAURUS',	  'plant'=> 'POPPY',		 'gem' => 'EMERALD',	'ruler' => 'VENUS',	  'ext' => '', 'enr' => 2, 'dir' => 0,],
				['sign' => 'GEMINI',	  'plant'=> 'LAVENDER',		 'gem' => 'AGATE',		'ruler' => 'MERCURY', 'ext' => '', 'enr' => 3, 'dir' => 0,],
				['sign' => 'CANCER',	  'plant'=> 'ACANTHUS',		 'gem' => 'PEARLS',		'ruler' => 'MOON',	  'ext' => '', 'enr' => 4, 'dir' => 0,],
				['sign' => 'LEO',		  'plant'=> 'SUNFLOWER',	 'gem' => 'RUBY',		'ruler' => 'SUN',	  'ext' => '', 'enr' => 1, 'dir' => 0,],
				['sign' => 'VIRGO',		  'plant'=> 'MORNING_GLORY', 'gem' => 'SAPPHIRE',	'ruler' => 'MERCURY', 'ext' => '', 'enr' => 2, 'dir' => 0,],
				['sign' => 'LIBRA',		  'plant'=> 'ROSE',			 'gem' => 'OPAL',		'ruler' => 'VENUS',	  'ext' => '', 'enr' => 3, 'dir' => 0,],
				['sign' => 'SCORPIO',	  'plant'=> 'CHRYSANTHENUM', 'gem' => 'ONYX',		'ruler' => 'MARS',	  'ext' => '', 'enr' => 4, 'dir' => 0,],
				['sign' => 'SAGITTARIUS', 'plant'=> 'NARCISSUS',	 'gem' => 'TURQUOISE',	'ruler' => 'JUPITER', 'ext' => '', 'enr' => 1, 'dir' => 0,],
				['sign' => 'CAPRICORN',	  'plant'=> 'CARNATION',	 'gem' => 'GARNET',		'ruler' => 'SATURN',  'ext' => '', 'enr' => 2, 'dir' => 0,],
				['sign' => 'AQUARIUS',	  'plant'=> 'ORCHID',		 'gem' => 'AMETHYST',	'ruler' => 'SATURN',  'ext' => '', 'enr' => 3, 'dir' => 0,],
				['sign' => 'PISCES',	  'plant'=> 'WATER_LILY',	 'gem' => 'AQUAMARINE', 'ruler' => 'JUPITER', 'ext' => '', 'enr' => 4, 'dir' => 0,],
				// Native
				['sign' => 'OTTER',		 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'WOLF',		 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'HAWK',		 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'BEAVER',	 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'DEER',		 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'WOODPECKER', 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'SALMON',	 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'BEAR',		 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'RAVEN',		 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'SNAKE',		 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'OWL',		 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				['sign' => 'GOOSE',		 'plant'=> '', 'gem' => '', 'ruler' => '', 'ext' => '', 'enr' => 7, 'dir' => 0,],
				// Celtic
				['sign' => 'DEER',		'plant'=> 'BIRCH',	  'gem' => 'QUARTZ',	 'ruler' => 'SATURN',  'ext' => 'GATEWAY',		'enr' => 2, 'dir' => 0,],
				['sign' => 'CAT',		'plant'=> 'ROWAN',	  'gem' => 'DIAMOND',	 'ruler' => 'URANUS',  'ext' => 'INNER',		'enr' => 3, 'dir' => 0,],
				['sign' => 'SNAKE',		'plant'=> 'ASH',	  'gem' => 'CORAL',		 'ruler' => 'NEPTUNE', 'ext' => 'CHANGING',		'enr' => 4, 'dir' => 0,],
				['sign' => 'FOX',		'plant'=> 'ALDER',	  'gem' => 'RUBY',		 'ruler' => 'MARS',	   'ext' => 'ADVANCING',	'enr' => 1, 'dir' => 0,],
				['sign' => 'BULL',		'plant'=> 'WILLOW',	  'gem' => 'MOONSTONE',	 'ruler' => 'VENUS',   'ext' => 'DREAMING',		'enr' => 2, 'dir' => 0,],
				['sign' => 'SEAHORSE',	'plant'=> 'HAWTHORN', 'gem' => 'AMETHYST',	 'ruler' => 'MERCURY', 'ext' => 'GIFTING',		'enr' => 3, 'dir' => 0,],
				['sign' => 'WREN',		'plant'=> 'OAK',	  'gem' => 'AMBER',		 'ruler' => 'MOON',	   'ext' => 'STANDING',		'enr' => 4, 'dir' => 0,],
				['sign' => 'HORSE',		'plant'=> 'HOLLY',	  'gem' => 'RUBY',		 'ruler' => 'SUN',	   'ext' => 'ROYAL',		'enr' => 1, 'dir' => 0,],
				['sign' => 'SALMON',	'plant'=> 'HAZEL',	  'gem' => 'SAPPHIRE',	 'ruler' => 'MERCURY', 'ext' => 'AUTHORITY',	'enr' => 2, 'dir' => 0,],
				['sign' => 'SWAN',		'plant'=> 'VINE',	  'gem' => 'EMERALD',	 'ruler' => 'VENUS',   'ext' => 'BALANCING',	'enr' => 3, 'dir' => 0,],
				['sign' => 'BUTTERFLY', 'plant'=> 'IVY',	  'gem' => 'OPAL',		 'ruler' => 'MOON',	   'ext' => 'EXPLORING',	'enr' => 3, 'dir' => 0,],
				['sign' => 'WOLF',		'plant'=> 'REED',	  'gem' => 'JASPER',	 'ruler' => 'PLUTO',   'ext' => 'HARMONIC',		'enr' => 4, 'dir' => 0,],
				['sign' => 'HAWK',		'plant'=> 'ELDER',	  'gem' => 'BLOODSTONE', 'ruler' => 'JUPITER', 'ext' => 'REGENERATING', 'enr' => 1, 'dir' => 0,],
				// Myanmar
				['sign' => 'GARUDA',	  'plant'=> '', 'gem' => '', 'ruler' => 'SUN',	   'ext' => 'Sunday',	   'enr' => 0, 'dir' => 5,],
				['sign' => 'TIGER',		  'plant'=> '', 'gem' => '', 'ruler' => 'MOON',	   'ext' => 'Monday',	   'enr' => 0, 'dir' => 2,],
				['sign' => 'LION',		  'plant'=> '', 'gem' => '', 'ruler' => 'MARS',	   'ext' => 'Tuesday',	   'enr' => 0, 'dir' => 6,],
				['sign' => 'ELEPHANT_WT', 'plant'=> '', 'gem' => '', 'ruler' => 'MERCURY', 'ext' => 'Wednesday',   'enr' => 0, 'dir' => 3,],
				['sign' => 'ELEPHANT',	  'plant'=> '', 'gem' => '', 'ruler' => 'RAHU',	   'ext' => 'Wednesday-2', 'enr' => 0, 'dir' => 8,],
				['sign' => 'RAT',		  'plant'=> '', 'gem' => '', 'ruler' => 'JUPITER', 'ext' => 'Thursday',	   'enr' => 0, 'dir' => 4,],
				['sign' => 'GUINEA_PIG',  'plant'=> '', 'gem' => '', 'ruler' => 'VENUS',   'ext' => 'Friday',	   'enr' => 0, 'dir' => 1,],
				['sign' => 'DRAGON',	  'plant'=> '', 'gem' => '', 'ruler' => 'SATURN',  'ext' => 'Saturday',	   'enr' => 0, 'dir' => 7,],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'dls_zodiac', $sql_ary);
		}
	}

	/**
	* Custom function to add data
	*/
	public function zodiac_dates()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'dls_zodiac_dates'))
		{
			$sql_ary = [
				// Tropical
				['zid' => 1,  'type' => 1, 'date' => '03-21-04-19',],
				['zid' => 2,  'type' => 1, 'date' => '04-20-05-20',],
				['zid' => 3,  'type' => 1, 'date' => '05-21-06-20',],
				['zid' => 4,  'type' => 1, 'date' => '06-21-07-22',],
				['zid' => 5,  'type' => 1, 'date' => '07-23-08-22',],
				['zid' => 6,  'type' => 1, 'date' => '08-23-09-22',],
				['zid' => 7,  'type' => 1, 'date' => '09-23-10-22',],
				['zid' => 8,  'type' => 1, 'date' => '10-23-11-21',],
				['zid' => 9,  'type' => 1, 'date' => '11-22-12-21',],
				['zid' => 10, 'type' => 1, 'date' => '12-22-01-19',],
				['zid' => 11, 'type' => 1, 'date' => '01-20-02-18',],
				['zid' => 12, 'type' => 1, 'date' => '02-19-03-20',],
				// Sidereal
				['zid' => 1,  'type' => 2, 'date' => '04-15-05-15',],
				['zid' => 2,  'type' => 2, 'date' => '05-16-06-15',],
				['zid' => 3,  'type' => 2, 'date' => '06-16-07-15',],
				['zid' => 4,  'type' => 2, 'date' => '07-16-08-15',],
				['zid' => 5,  'type' => 2, 'date' => '08-16-09-15',],
				['zid' => 6,  'type' => 2, 'date' => '09-16-10-15',],
				['zid' => 7,  'type' => 2, 'date' => '10-16-11-15',],
				['zid' => 8,  'type' => 2, 'date' => '11-16-12-15',],
				['zid' => 9,  'type' => 2, 'date' => '12-16-01-15',],
				['zid' => 10, 'type' => 2, 'date' => '01-15-02-14',],
				['zid' => 11, 'type' => 2, 'date' => '02-15-03-14',],
				['zid' => 12, 'type' => 2, 'date' => '03-15-04-14',],
				// Native
				['zid' => 13, 'type' => 3, 'date' => '01-20-02-18',],
				['zid' => 14, 'type' => 3, 'date' => '02-19-03-20',],
				['zid' => 15, 'type' => 3, 'date' => '03-21-04-19',],
				['zid' => 16, 'type' => 3, 'date' => '04-20-05-20',],
				['zid' => 17, 'type' => 3, 'date' => '05-21-06-20',],
				['zid' => 18, 'type' => 3, 'date' => '06-21-07-21',],
				['zid' => 19, 'type' => 3, 'date' => '07-22-08-21',],
				['zid' => 20, 'type' => 3, 'date' => '08-22-09-21',],
				['zid' => 21, 'type' => 3, 'date' => '09-22-10-22',],
				['zid' => 22, 'type' => 3, 'date' => '10-23-11-22',],
				['zid' => 23, 'type' => 3, 'date' => '11-23-12-21',],
				['zid' => 24, 'type' => 3, 'date' => '12-22-01-19',],
				// Celtic
				['zid' => 25, 'type' => 4, 'date' => '12-24-01-20',],
				['zid' => 26, 'type' => 4, 'date' => '01-21-02-17',],
				['zid' => 27, 'type' => 4, 'date' => '02-18-03-17',],
				['zid' => 28, 'type' => 4, 'date' => '03-18-04-14',],
				['zid' => 29, 'type' => 4, 'date' => '04-15-05-12',],
				['zid' => 30, 'type' => 4, 'date' => '05-13-06-09',],
				['zid' => 31, 'type' => 4, 'date' => '06-10-07-07',],
				['zid' => 32, 'type' => 4, 'date' => '07-08-08-04',],
				['zid' => 33, 'type' => 4, 'date' => '08-05-09-01',],
				['zid' => 34, 'type' => 4, 'date' => '09-02-09-29',],
				['zid' => 35, 'type' => 4, 'date' => '09-30-10-27',],
				['zid' => 36, 'type' => 4, 'date' => '10-28-11-24',],
				['zid' => 37, 'type' => 4, 'date' => '11-25-12-23',],
				// Myanmar
				['zid' => 38, 'type' => 6, 'date' => '1',],
				['zid' => 39, 'type' => 6, 'date' => '1',],
				['zid' => 40, 'type' => 6, 'date' => '1',],
				['zid' => 41, 'type' => 6, 'date' => '1',],
				['zid' => 42, 'type' => 6, 'date' => '12',],
				['zid' => 43, 'type' => 6, 'date' => '1',],
				['zid' => 44, 'type' => 6, 'date' => '1',],
				['zid' => 45, 'type' => 6, 'date' => '1',],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'dls_zodiac_dates', $sql_ary);
		}
	}

	/**
	* Custom function to add data
	*/
	public function zodiac_symbols()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'dls_zodiac_symbols'))
		{
			$sql_ary = [
				// Chinese
				['symbol' => 'WHITE_TIGER',		'type' => 5, 'ruler' => 'VENUS',   'ext' => 'AUTUMN', 'dir' => 4,],
				['symbol' => 'AZURE_DRAGON',	'type' => 5, 'ruler' => 'JUPITER', 'ext' => 'SPRING', 'dir' => 2,],
				['symbol' => 'BLACK_TORTOISE',	'type' => 5, 'ruler' => 'MERCURY', 'ext' => 'WINTER', 'dir' => 1,],
				['symbol' => 'WERMILLION_BIRD', 'type' => 5, 'ruler' => 'MARS',	   'ext' => 'SUMMER', 'dir' => 3,],
				['symbol' => 'YELLOW_DRAGON',	'type' => 5, 'ruler' => 'SATURN',  'ext' => 'LMOTS',  'dir' => 9,],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'dls_zodiac_symbols', $sql_ary);
		}
	}

	/**
	* Custom function to add data
	*/
	public function zodiac_heavenly_stems()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'dls_zodiac_heavenly_stems'))
		{
			$sql_ary = [
				// Ten heavenly stems & their cycle numbers (number 0 is equivalent to 60)
				['snr' => 1, 'enr' => 5, 'sid' => 2, 'aid' => 1,  'bid' => 11, 'cid' => 21, 'did' => 31, 'eid' => 41, 'fid'=> 51,],
				['snr' => 2, 'enr' => 5, 'sid' => 2, 'aid' => 2,  'bid' => 12, 'cid' => 22, 'did' => 32, 'eid' => 42, 'fid'=> 52,],
				['snr' => 1, 'enr' => 1, 'sid' => 4, 'aid' => 3,  'bid' => 13, 'cid' => 23, 'did' => 33, 'eid' => 43, 'fid'=> 53,],
				['snr' => 2, 'enr' => 1, 'sid' => 4, 'aid' => 4,  'bid' => 14, 'cid' => 24, 'did' => 34, 'eid' => 44, 'fid'=> 54,],
				['snr' => 1, 'enr' => 2, 'sid' => 5, 'aid' => 5,  'bid' => 15, 'cid' => 25, 'did' => 35, 'eid' => 45, 'fid'=> 55,],
				['snr' => 2, 'enr' => 2, 'sid' => 5, 'aid' => 6,  'bid' => 16, 'cid' => 26, 'did' => 36, 'eid' => 46, 'fid'=> 56,],
				['snr' => 1, 'enr' => 6, 'sid' => 1, 'aid' => 7,  'bid' => 17, 'cid' => 27, 'did' => 37, 'eid' => 47, 'fid'=> 57,],
				['snr' => 2, 'enr' => 6, 'sid' => 1, 'aid' => 8,  'bid' => 18, 'cid' => 28, 'did' => 38, 'eid' => 48, 'fid'=> 58,],
				['snr' => 1, 'enr' => 4, 'sid' => 3, 'aid' => 9,  'bid' => 19, 'cid' => 29, 'did' => 39, 'eid' => 49, 'fid'=> 59,],
				['snr' => 2, 'enr' => 4, 'sid' => 3, 'aid' => 10, 'bid' => 20, 'cid' => 30, 'did' => 40, 'eid' => 50, 'fid'=> 0,],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'dls_zodiac_heavenly_stems', $sql_ary);
		}
	}
}
