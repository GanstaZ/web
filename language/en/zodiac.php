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
	'ZODIAC'   => 'Zodiac',
	'TROPICAL' => 'Tropical',
	'SIDEREAL' => 'Sidereal',
	'NATIVE'   => 'Native',
	'CELTIC'   => 'Celtic',
	'CHINESE'  => 'Chinese',
	'MYANMAR'  => 'Myanmar',
	'SIGN'	   => 'Sign',
	'ELEMENT'  => 'Element',
	'PLANT'	   => 'Plant',
	'STONE'	   => 'Stone',
	'RULER'	   => 'Ruler',
	'SYMBOL'   => 'Symbol',
	'WEEKDAY'  => 'Weekday',
	'SEASON'   => 'Season',

	// Elements
	'FIRE'	=> 'Fire',
	'EARTH' => 'Earth', // Should we remove it because of astro earth lang var?
	'AIR'	=> 'Air',
	'WATER' => 'Water',
	'WOOD'	=> 'Wood',
	'METAL' => 'Metal',
	'TOTEM' => 'Totem',

	'STEM' => [
		1 => 'Yang',
		2 => 'Yin',
	],

	// Seasons
	'AUTUMN' => 'Autumn',
	'SPRING' => 'Spring',
	'WINTER' => 'Winter',
	'SUMMER' => 'Summer',
	'LMOTS'	 => 'Last month of the season',

	// Tropical/Sidereal signs
	'ARIES'		  => 'Aries',
	'TAURUS'	  => 'Taurus',
	'GEMINI'	  => 'Gemini',
	'CANCER'	  => 'Cancer',
	'LEO'		  => 'Leo',
	'VIRGO'		  => 'Virgo',
	'LIBRA'		  => 'Libra',
	'SCORPIO'	  => 'Scorpio',
	'SAGITTARIUS' => 'Sagittarius',
	'CAPRICORN'	  => 'Capricorn',
	'AQUARIUS'	  => 'Aquarius',
	'PISCES'	  => 'Pisces',
	// Chinese signs
	'PIG'		  => 'Pig',
	'RAT'		  => 'Rat',
	'OX'		  => 'Ox',
	'TIGER'		  => 'Tiger',
	'RABBIT'	  => 'Rabbit',
	'DRAGON'	  => 'Dragon',
	'SNAKE'		  => 'Snake',
	'HORSE'		  => 'Horse',
	'GOAT'		  => 'Goat',
	'MONKEY'	  => 'Monkey',
	'ROOSTER'	  => 'Rooster',
	'DOG'		  => 'Dog',
	// Celtic/Native American signs
	'OTTER'		  => 'Otter',
	'WOLF'		  => 'Wolf',
	'HAWK'		  => 'Hawk',
	'BEAVER'	  => 'Beaver',
	'DEER'		  => 'Deer',
	'SALMON'	  => 'Salmon',
	'BEAR'		  => 'Bear',
	'RAVEN'		  => 'Raven',
	'OWL'		  => 'Owl',
	'GOOSE'		  => 'Goose',
	'CAT'		  => 'Cat',
	'FOX'		  => 'Fox',
	'BULL'		  => 'Bull',
	'WREN'		  => 'Wren',
	'SWAN'		  => 'Swan',
	'SEAHORSE'	  => 'Seahorse',
	'WOODPECKER'  => 'Woodpecker',
	'BUTTERFLY'	  => 'Butterfly',
	// Myanmar signs
	'GARUDA'	  => 'Garuda',
	'LION'		  => 'Lion',
	'ELEPHANT'	  => 'Elephant',
	'ELEPHANT_WT' => 'Elephant (With tusks)',
	'GUINEA_PIG'  => 'Guinea Pig',

	// Four symbols of the Chinese constellations
	'WHITE_TIGER'	  => 'White Tiger',
	'AZURE_DRAGON'	  => 'Azure Dragon',
	'BLACK_TORTOISE'  => 'Black Tortoise',
	'WERMILLION_BIRD' => 'Wermillion Bird',
	// Yellow Emperor of the center of the universe
	'YELLOW_DRAGON'	  => 'Yellow Dragon',

	// Plants (Trees/Flowers)
	'BIRCH'			=> 'Birch',
	'ROWAN'			=> 'Rowan',
	'ASH'			=> 'Ash',
	'ALDER'			=> 'Alder',
	'WILLOW'		=> 'Willow',
	'OAK'			=> 'Oak',
	'HOLLY'			=> 'Holly',
	'HAZEL'			=> 'Hazel',
	'VINE'			=> 'Vine',
	'IVY'			=> 'Ivy',
	'REED'			=> 'Reed',
	'ELDER'			=> 'Elder',
	'POPPY'			=> 'Poppy',
	'ROSE'			=> 'Rose',
	'ORCHID'		=> 'Orchid',
	'HAWTHORN'		=> 'Hawthorn',
	'LAVENDER'		=> 'Lavander',
	'ACANTHUS'		=> 'Acanthus',
	'SUNFLOWER'		=> 'Sunflower',
	'NARCISSUS'		=> 'Narcissus',
	'CARNATION'		=> 'Carnation',
	'WATER_LILY'	=> 'Water Lily',
	'HONEYSUCKLE'	=> 'Honeysuckle',
	'MORNING_GLORY' => 'Morning Glory',
	'CHRYSANTHENUM' => 'Chrysanthenum',

	// Gemstones
	'AGATE'		 => 'Agate',
	'PEARLS'	 => 'Pearls',
	'RUBY'		 => 'Ruby',
	'OPAL'		 => 'Opal',
	'ONYX'		 => 'Onyx',
	'GARNET'	 => 'Garner',
	'QUARTZ'	 => 'Quartz',
	'CORAL'		 => 'Coral',
	'AMBER'		 => 'Amber',
	'JASPER'	 => 'Jasper',
	'DIAMOND'	 => 'Diamond',
	'EMERALD'	 => 'Emerald',
	'AMETHYST'	 => 'Amethyst',
	'SAPPHIRE'	 => 'Sapphire',
	'TURQUOISE'	 => 'Turquoise',
	'MOONSTONE'	 => 'Moonstone',
	'AQUAMARINE' => 'Aquamarine',
	'BLOODSTONE' => 'Bloodstone',

	// Moon types
	'INNER'		   => 'Inner',
	'ROYAL'		   => 'Royal',
	'GATEWAY'	   => 'Gateway',
	'CHANGING'	   => 'Changing',
	'ADVANCING'	   => 'Advancing',
	'DREAMING'	   => 'Dreaming',
	'GIFTING'	   => 'Gifting',
	'STANDING'	   => 'Standing',
	'AUTHORITY'	   => 'Authority',
	'BALANCING'	   => 'Balancing',
	'EXPLORING'	   => 'Exploring',
	'HARMONIC'	   => 'Harmonic',
	'REGENERATING' => 'Regenerating',
]);
