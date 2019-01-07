<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugins;

use phpbb\di\service_collection;

/**
* DLS Web plugins manager
*/
class manager
{
	/** @var array Contains all available plugins */
	protected static $plugins = false;

	/**
	* Constructor
	*
	* @param service_collection $plugins
	*/
	public function __construct(service_collection $plugins)
	{
		$this->register_plugins($plugins);
	}

	/**
	* Register all available plugins
	*
	* @param Service collection of plugins
	*/
	protected function register_plugins($plugins): void
	{
		if (!empty($plugins))
		{
			self::$plugins = [];
			foreach ($plugins as $plugin)
			{
				self::$plugins[$plugin->get_name()] = $plugin;
			}
		}
	}

	/**
	* Get plugin
	*
	* @param string $name Name of the plugin we want to load
	* @return object
	*/
	public function get($name)
	{
		return self::$plugins[$name] ?? null;
	}

	/**
	* Get available plugins
	*
	* @return array
	*/
	public function get_plugins(): array
	{
		return array_keys(self::$plugins) ?? [];
	}

	/**
	* Remove plugin
	*
	* @param string $name Name of the plugin we want to remove
	* @return void
	*/
	public function remove($name): void
	{
		if (isset(self::$plugins[$name]) || array_key_exists($name, self::$plugins))
		{
			unset(self::$plugins[$name]);
		}
	}
}
