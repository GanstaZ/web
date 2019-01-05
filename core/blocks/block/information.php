<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks\block;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\event\dispatcher;

/**
* DLS Web Information block
*/
class information implements block_interface
{
	/** @var config */
	protected $config;

	/** @var template */
	protected $template;

	/** @var dispatcher */
	protected $dispatcher;

	/**
	* Constructor
	*
	* @param config		$config		Config object
	* @param template	$template	Template object
	* @param dispatcher $dispatcher Dispatcher object
	*/
	public function __construct(config $config, template $template, dispatcher $dispatcher)
	{
		$this->config = $config;
		$this->dispatcher = $dispatcher;
		$this->template = $template;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_data(): array
	{
		return [
			'block_name' => 'dls_information',
			'cat_name' => 'side',
			'ext_name' => 'dls_web',
		];
	}

	/**
	* {@inheritdoc}
	*/
	public function load(): void
	{
		/**
		* Event dls.web.information_before
		*
		* @event dls.web.information_before
		* @since 2.3.6-RC1
		*/
		$this->dispatcher->dispatch('dls.web.information_before');

		// Set template vars
		$this->template->assign_vars([
			'phpbb_version' => (string) $this->config['version'],
			'core_stable'	=> (string) $this->config['dls_core_version'],
		]);
	}
}
