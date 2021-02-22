<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
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
	// Planets
	'SUN'	  => 'Sun',
	'MOON'	  => 'Moon',
	'MERCURY' => 'Mercury',
	'VENUS'	  => 'Venus',
	'EARTH'	  => 'Earth',
	'MARS'	  => 'Mars',
	'JUPITER' => 'Jupiter',
	'SATURN'  => 'Saturn',
	'URANUS'  => 'Uranus',
	'NEPTUNE' => 'Neptune',
	'PLUTO'	  => 'Pluto',
	'RAHU'	  => 'Rahu',

	// Cardinal directions
	'DIRECTION' => 'Cardinal direction',
	'CENTER'	=> 'Center',
	'NORTH'		=> 'North',
	'EAST'		=> 'East',
	'SOUTH'		=> 'South',
	'WEST'		=> 'West',
	'NORTHEAST' => 'Northeast',
	'SOUTHEAST' => 'Southeast',
	'SOUTHWEST' => 'Southwest',
	'NORTHWEST' => 'Northwest',
]);
