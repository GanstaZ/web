<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\migrations\v23;

class m_dls_zodiac extends \phpbb\db\migration\migration
{
	/**
	* This migration depends on dls's m_dls_main migration
	* already being installed.
	*/
	static public function depends_on()
	{
		return ['\dls\web\migrations\v23\m_dls_main'];
	}

	public function update_data()
	{
		return [
			// Add zodiac data
			['custom', [[$this, 'zodiac_data']]],
			['custom', [[$this, 'zodiac_dates']]],
			['custom', [[$this, 'zodiac_heavenly_stems']]],
		];
	}

	public function zodiac_data()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'zodiac'))
		{
			$sql_ary = [
				// Tropical/Sidereal
				['sign' => 'ARIES',		  'plant'=> 'HONEYSUCKLE',	 'gems' => 'DIAMOND',	 'ruler' => 'MARS',	   'ext' => '', 'enr' => 1,],
				['sign' => 'TAURUS',	  'plant'=> 'POPPY',		 'gems' => 'EMERALD',	 'ruler' => 'VENUS',   'ext' => '', 'enr' => 2,],
				['sign' => 'GEMINI',	  'plant'=> 'LAVENDER',		 'gems' => 'AGATE',		 'ruler' => 'MERCURY', 'ext' => '', 'enr' => 3,],
				['sign' => 'CANCER',	  'plant'=> 'ACANTHUS',		 'gems' => 'PEARLS',	 'ruler' => 'MOON',	   'ext' => '', 'enr' => 4,],
				['sign' => 'LEO',		  'plant'=> 'SUNFLOWER',	 'gems' => 'RUBY',		 'ruler' => 'SUN',	   'ext' => '', 'enr' => 1,],
				['sign' => 'VIRGO',		  'plant'=> 'MORNING_GLORY', 'gems' => 'SAPPHIRE',	 'ruler' => 'MERCURY', 'ext' => '', 'enr' => 2,],
				['sign' => 'LIBRA',		  'plant'=> 'ROSE',			 'gems' => 'OPAL',		 'ruler' => 'VENUS',   'ext' => '', 'enr' => 3,],
				['sign' => 'SCORPIO',	  'plant'=> 'CHRYSANTHENUM', 'gems' => 'ONYX',		 'ruler' => 'MARS',	   'ext' => '', 'enr' => 4,],
				['sign' => 'SAGITTARIUS', 'plant'=> 'NARCISSUS',	 'gems' => 'TURQUOISE',	 'ruler' => 'JUPITER', 'ext' => '', 'enr' => 1,],
				['sign' => 'CAPRICORN',	  'plant'=> 'CARNATION',	 'gems' => 'GARNET',	 'ruler' => 'SATURN',  'ext' => '', 'enr' => 2,],
				['sign' => 'AQUARIUS',	  'plant'=> 'ORCHID',		 'gems' => 'AMETHYST',	 'ruler' => 'SATURN',  'ext' => '', 'enr' => 3,],
				['sign' => 'PISCES',	  'plant'=> 'WATER_LILY',	 'gems' => 'AQUAMARINE', 'ruler' => 'JUPITER', 'ext' => '', 'enr' => 4,],
				// Native
				['sign' => 'OTTER',		 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'WOLF',		 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'HAWK',		 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'BEAVER',	 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'DEER',		 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'WOODPECKER', 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'SALMON',	 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'BEAR',		 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'RAVEN',		 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'SNAKE',		 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'OWL',		 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				['sign' => 'GOOSE',		 'plant'=> '', 'gems' => '', 'ruler' => '', 'ext' => '', 'enr' => 0,],
				// Celtic
				['sign' => 'DEER',		'plant'=> 'BIRCH',	  'gems' => 'QUARTZ',	  'ruler' => 'SATURN',	'ext' => 'GATEWAY',		 'enr' => 2,],
				['sign' => 'CAT',		'plant'=> 'ROWAN',	  'gems' => 'DIAMOND',	  'ruler' => 'URANUS',	'ext' => 'INNER',		 'enr' => 3,],
				['sign' => 'SNAKE',		'plant'=> 'ASH',	  'gems' => 'CORAL',	  'ruler' => 'NEPTUNE', 'ext' => 'CHANGING',	 'enr' => 4,],
				['sign' => 'FOX',		'plant'=> 'ALDER',	  'gems' => 'RUBY',		  'ruler' => 'MARS',	'ext' => 'ADVANCING',	 'enr' => 1,],
				['sign' => 'BULL',		'plant'=> 'WILLOW',	  'gems' => 'MOONSTONE',  'ruler' => 'VENUS',	'ext' => 'DREAMING',	 'enr' => 2,],
				['sign' => 'SEAHORSE',	'plant'=> 'HAWTHORN', 'gems' => 'AMETHYST',	  'ruler' => 'MERCURY', 'ext' => 'GIFTING',		 'enr' => 3,],
				['sign' => 'WREN',		'plant'=> 'OAK',	  'gems' => 'AMBER',	  'ruler' => 'MOON',	'ext' => 'STANDING',	 'enr' => 4,],
				['sign' => 'HORSE',		'plant'=> 'HOLLY',	  'gems' => 'RUBY',		  'ruler' => 'SUN',		'ext' => 'ROYAL',		 'enr' => 1,],
				['sign' => 'SALMON',	'plant'=> 'HAZEL',	  'gems' => 'SAPPHIRE',	  'ruler' => 'MERCURY', 'ext' => 'AUTHORITY',	 'enr' => 2,],
				['sign' => 'SWAN',		'plant'=> 'VINE',	  'gems' => 'EMERALD',	  'ruler' => 'VENUS',	'ext' => 'BALANCING',	 'enr' => 3,],
				['sign' => 'BUTTERFLY', 'plant'=> 'IVY',	  'gems' => 'OPAL',		  'ruler' => 'MOON',	'ext' => 'EXPLORING',	 'enr' => 3,],
				['sign' => 'WOLF',		'plant'=> 'REED',	  'gems' => 'JASPER',	  'ruler' => 'PLUTO',	'ext' => 'HARMONIC',	 'enr' => 4,],
				['sign' => 'HAWK',		'plant'=> 'ELDER',	  'gems' => 'BLOODSTONE', 'ruler' => 'JUPITER', 'ext' => 'REGENERATING', 'enr' => 1,],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'zodiac', $sql_ary);
		}
	}

	public function zodiac_dates()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'zodiac_data'))
		{
			$sql_ary = [
				// Tropical
				[
					'zid' => 1, 'type' => 1, 'start' => '03-21', 'end' => '04-19',
				],
				[
					'zid' => 2, 'type' => 1, 'start' => '04-20', 'end' => '05-20',
				],
				[
					'zid' => 3, 'type' => 1, 'start' => '05-21', 'end' => '06-20',
				],
				[
					'zid' => 4, 'type' => 1, 'start' => '06-21', 'end' => '07-22',
				],
				[
					'zid' => 5, 'type' => 1, 'start' => '07-23', 'end' => '08-22',
				],
				[
					'zid' => 6, 'type' => 1, 'start' => '08-23', 'end' => '09-22',
				],
				[
					'zid' => 7, 'type' => 1, 'start' => '09-23', 'end' => '10-22',
				],
				[
					'zid' => 8, 'type' => 1, 'start' => '10-23', 'end' => '11-21',
				],
				[
					'zid' => 9, 'type' => 1, 'start' => '11-22', 'end' => '12-21',
				],
				[
					'zid' => 10, 'type' => 1, 'start' => '12-22', 'end' => '01-19',
				],
				[
					'zid' => 11, 'type' => 1, 'start' => '01-20', 'end' => '02-18',
				],
				[
					'zid' => 12, 'type' => 1, 'start' => '02-19', 'end' => '03-20',
				],
				// Sidereal
				[
					'zid' => 1, 'type' => 2, 'start' => '04-15', 'end' => '05-15',
				],
				[
					'zid' => 2, 'type' => 2, 'start' => '05-16', 'end' => '06-15',
				],
				[
					'zid' => 3, 'type' => 2, 'start' => '06-16', 'end' => '07-15',
				],
				[
					'zid' => 4, 'type' => 2, 'start' => '07-16', 'end' => '08-15',
				],
				[
					'zid' => 5, 'type' => 2, 'start' => '08-16', 'end' => '09-15',
				],
				[
					'zid' => 6, 'type' => 2, 'start' => '09-16', 'end' => '10-15',
				],
				[
					'zid' => 7, 'type' => 2, 'start' => '10-16', 'end' => '11-15',
				],
				[
					'zid' => 8, 'type' => 2, 'start' => '11-16', 'end' => '12-15',
				],
				[
					'zid' => 9, 'type' => 2, 'start' => '12-16', 'end' => '01-15',
				],
				[
					'zid' => 10, 'type' => 2, 'start' => '01-15', 'end' => '02-14',
				],
				[
					'zid' => 11, 'type' => 2, 'start' => '02-15', 'end' => '03-14',
				],
				[
					'zid' => 12, 'type' => 2, 'start' => '03-15', 'end' => '04-14',
				],
				// Native
				[
					'zid' => 13, 'type' => 3, 'start' => '01-20', 'end' => '02-18',
				],
				[
					'zid' => 14, 'type' => 3, 'start' => '02-19', 'end' => '03-20',
				],
				[
					'zid' => 15, 'type' => 3, 'start' => '03-21', 'end' => '04-19',
				],
				[
					'zid' => 16, 'type' => 3, 'start' => '04-20', 'end' => '05-20',
				],
				[
					'zid' => 17, 'type' => 3, 'start' => '05-21', 'end' => '06-20',
				],
				[
					'zid' => 18, 'type' => 3, 'start' => '06-21', 'end' => '07-21',
				],
				[
					'zid' => 19, 'type' => 3, 'start' => '07-22', 'end' => '08-21',
				],
				[
					'zid' => 20, 'type' => 3, 'start' => '08-22', 'end' => '09-21',
				],
				[
					'zid' => 21, 'type' => 3, 'start' => '09-22', 'end' => '10-22',
				],
				[
					'zid' => 22, 'type' => 3, 'start' => '10-23', 'end' => '11-22',
				],
				[
					'zid' => 23, 'type' => 3, 'start' => '11-23', 'end' => '12-21',
				],
				[
					'zid' => 24, 'type' => 3, 'start' => '12-22', 'end' => '01-19',
				],
				// Celtic
				[
					'zid' => 25, 'type' => 4, 'start' => '12-24', 'end' => '01-20',
				],
				[
					'zid' => 26, 'type' => 4, 'start' => '01-21', 'end' => '02-17',
				],
				[
					'zid' => 27, 'type' => 4, 'start' => '02-18', 'end' => '03-17',
				],
				[
					'zid' => 28, 'type' => 4, 'start' => '03-18', 'end' => '04-14',
				],
				[
					'zid' => 29, 'type' => 4, 'start' => '04-15', 'end' => '05-12',
				],
				[
					'zid' => 30, 'type' => 4, 'start' => '05-13', 'end' => '06-09',
				],
				[
					'zid' => 31, 'type' => 4, 'start' => '06-10', 'end' => '07-07',
				],
				[
					'zid' => 32, 'type' => 4, 'start' => '07-08', 'end' => '08-04',
				],
				[
					'zid' => 33, 'type' => 4, 'start' => '08-05', 'end' => '09-01',
				],
				[
					'zid' => 34, 'type' => 4, 'start' => '09-02', 'end' => '09-29',
				],
				[
					'zid' => 35, 'type' => 4, 'start' => '09-30', 'end' => '10-27',
				],
				[
					'zid' => 36, 'type' => 4, 'start' => '10-28', 'end' => '11-24',
				],
				[
					'zid' => 37, 'type' => 4, 'start' => '11-25', 'end' => '12-23',
				],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'zodiac_data', $sql_ary);
		}
	}

	public function zodiac_heavenly_stems()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'zodiac_heavenly_stems'))
		{
			$sql_ary = [
				// Ten heavenly stems & their cycle numbers (number 0 is equivalent to 60)
				['stem' => 'YANG_WOOD',	 'a_id' => 1,  'b_id' => 11, 'c_id' => 21, 'd_id' => 31, 'e_id' => 41, 'f_id'=> 51,],
				['stem' => 'YIN_WOOD',	 'a_id' => 2,  'b_id' => 12, 'c_id' => 22, 'd_id' => 32, 'e_id' => 42, 'f_id'=> 52,],
				['stem' => 'YANG_FIRE',	 'a_id' => 3,  'b_id' => 13, 'c_id' => 23, 'd_id' => 33, 'e_id' => 43, 'f_id'=> 53,],
				['stem' => 'YIN_FIRE',	 'a_id' => 4,  'b_id' => 14, 'c_id' => 24, 'd_id' => 34, 'e_id' => 44, 'f_id'=> 54,],
				['stem' => 'YANG_EARTH', 'a_id' => 5,  'b_id' => 15, 'c_id' => 25, 'd_id' => 35, 'e_id' => 45, 'f_id'=> 55,],
				['stem' => 'YIN_EARTH',	 'a_id' => 6,  'b_id' => 16, 'c_id' => 26, 'd_id' => 36, 'e_id' => 46, 'f_id'=> 56,],
				['stem' => 'YANG_METAL', 'a_id' => 7,  'b_id' => 17, 'c_id' => 27, 'd_id' => 37, 'e_id' => 47, 'f_id'=> 57,],
				['stem' => 'YIN_METAL',	 'a_id' => 8,  'b_id' => 18, 'c_id' => 28, 'd_id' => 38, 'e_id' => 48, 'f_id'=> 58,],
				['stem' => 'YANG_WATER', 'a_id' => 9,  'b_id' => 19, 'c_id' => 29, 'd_id' => 39, 'e_id' => 49, 'f_id'=> 59,],
				['stem' => 'YIN_WATER',	 'a_id' => 10, 'b_id' => 20, 'c_id' => 30, 'd_id' => 40, 'e_id' => 50, 'f_id'=> 0,],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'zodiac_heavenly_stems', $sql_ary);
		}
	}
}
