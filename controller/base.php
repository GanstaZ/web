<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\controller;

use phpbb\controller\helper;
use phpbb\language\language;
use dls\web\core\blocks\manager;

/**
* DLS Web base controller
*/
abstract class base
{
	/** @var controller helper */
	protected $helper;

	/** @var language */
	protected $language;

	/** @var manager */
	protected $manager;

	/**
	* Constructor
	*
	* @param helper	  $helper	Controller helper object
	* @param language $language Language object
	* @param manager  $manager	Manager object
	*/
	public function __construct(helper $helper, language $language, manager $manager)
	{
		$this->helper	= $helper;
		$this->language = $language;
		$this->manager	= $manager;
	}

	/**
	* Will check, if our service is disabled
	*
	* @param string $name
	* @throws \phpbb\exception\http_exception
	* @return exeption, if any
	*/
	protected function disabled(string $name): bool
	{
		return !$this->manager->get($name) || !(array) $this->manager->get($name);
	}
}
