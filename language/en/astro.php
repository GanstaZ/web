<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'SUN'  => 'Sun',
	'MOON' => 'Moon',
	'MERCURY' => 'Mercury',
	'VENUS'	  => 'Venus',
	'P_EARTH' => 'Earth',
	'MARS'	  => 'Mars',
	'JUPITER' => 'Jupiter',
	'SATURN'  => 'Saturn',
	'URANUS'  => 'Uranus',
	'NEPTUNE' => 'Neptune',
	'PLUTO'	  => 'Pluto',
]);
