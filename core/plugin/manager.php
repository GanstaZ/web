<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugin;

/**
* DLS Web plugin manager
*/
class manager
{
	/**
	* Array that contains all available plugins
	* @var array
	*/
	protected $plugins;

	/**
	* Constructor
	*
	* @param \phpbb\di\service_collection $plugins
	*/
	public function __construct(\phpbb\di\service_collection $plugins)
	{
		$this->register_plugins($plugins);
	}

	/**
	* Get plugin
	*
	* @param string $name Name of the service we want to use.
	*
	* @return object
	*/
	public function get($name)
	{
		return $this->plugins[$name];
	}

	/**
	* Get available plugins
	*
	* @return array
	*/
	public function get_plugins()
	{
		return $this->plugins;
	}

	/**
	* Remove plugin
	*
	* @param string $name Name of the plugin service we want to remove
	*
	* @return void
	*/
	public function remove($name)
	{
		if (isset($this->plugins[$name]) || array_key_exists($name, $this->plugins))
		{
			unset($this->plugins[$name]);
		}
	}

	/**
	* Register all available plugins
	*
	* @param array $plugins Array of available plugins
	*/
	protected function register_plugins($plugins)
	{
		if (!empty($plugins))
		{
			foreach ($plugins as $plugin)
			{
				$this->plugins[$plugin->get_name()] = $plugin;
			}
		}
	}
}
