<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks\type;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\controller\helper as controller;
use phpbb\template\template;
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

	/** @var controller helper */
	protected $controller;

	/** @var template */
	protected $template;

	/** @var dispatcher */
	protected $dispatcher;

	/** @var bool special */
	protected $special;

	/** @var bool loading */
	protected $loading;

	/** @var array Contains phpBB vars */
	protected $phpbb_vars;

	/**
	* Constructor
	*
	* @param config			  $config	  Config object
	* @param driver_interface $db		  Database object
	* @param controller       $controller Controller helper object
	* @param template		  $template	  Template object
	* @param dispatcher		  $dispatcher Dispatcher object
	* @param string			  $root_path  Path to the phpbb includes directory
	* @param string			  $php_ext	  PHP file extension
	*/
	public function __construct(config $config, driver_interface $db, controller $controller, template $template, dispatcher $dispatcher, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->db = $db;
		$this->controller = $controller;
		$this->dispatcher = $dispatcher;
		$this->template = $template;
		$this->phpbb_vars = ['root_path' => $root_path, 'php_ext' => $php_ext];
	}

	/**
	* {@inheritdoc}
	*/
	public function set_special(bool $set): void
	{
		$this->special = $set;
	}

	/**
	* {@inheritdoc}
	*/
	public function is_load_special(): bool
	{
		return $this->special;
	}

	/**
	* {@inheritdoc}
	*/
	public function set_active(bool $set): void
	{
		$this->loading = $set;
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
	public function load(): void
	{
	}

	/**
	* Get $phpbb_root_path or php_ext
	*
	* @return string
	*/
	public function get(string $var): ?string
	{
		return $this->phpbb_vars[$var] ?? null;
	}

	/**
	* Truncate title
	*
	* @param string		 $title	 Truncate title
	* @param int		 $length Max length of the string
	* @param null|string $ellips Language ellips
	* @return string
	*/
	public function truncate(string $title, int $length, $ellips = null): string
	{
		return truncate_string(censor_text($title), $length, 255, false, $ellips ?? '...');
	}
}
