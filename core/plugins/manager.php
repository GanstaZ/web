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
	* @param service_collection $plugins_types
	*/
	public function __construct(service_collection $plugins_types)
	{
		$this->register_plugins($plugins_types);
	}

	/**
	* Register all available plugins
	*
	* @param Service collection of plugins
	*/
	protected function register_plugins($plugins_types): void
	{
		if (!empty($plugins_types))
		{
			self::$plugins = [];
			foreach ($plugins_types as $plugin)
			{
				self::$plugins[$plugin->get_type()][$plugin->get_name()] = $plugin;
			}
		}
	}

	/**
	* Get plugin type by name
	*	 For example: zodiac $this->get($name)['service_name']->load($date->format('...'))
	*
	* @param string $name Name of the plugin
	* @return array
	*/
	public function get($name): array
	{
		return self::$plugins[$name] ?? [];
	}

	/**
	* Get all available plugins
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
