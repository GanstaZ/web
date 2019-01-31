<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks\type;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use dls\web\core\helper;
use phpbb\event\dispatcher;

/**
* DLS Web zodiac base class
*/
abstract class base implements block_interface
{
	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var helper */
	protected $helper;

	/** @var dispatcher */
	protected $dispatcher;

	/** @var bool loading */
	protected $loading;

	/**
	* Constructor
	*
	* @param config			  $config	  Config object
	* @param driver_interface $db		  Database object
	* @param helper			  $helper	  Helper object
	* @param dispatcher		  $dispatcher Dispatcher object
	*/
	public function __construct(config $config, driver_interface $db, helper $helper, dispatcher $dispatcher)
	{
		$this->config = $config;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->helper = $helper;
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
	}

	/**
	* {@inheritdoc}
	*/
	public function is_load_active(): bool
	{
		return $this->loading;
	}

	/**
	* {@inheritdoc}
	*/
	public function loading(bool $set): void
	{
		$this->loading = $set;
	}
}