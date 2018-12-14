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

/**
* DLS Web Information block
*/
class information implements block_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\event\dispatcher */
	protected $dispatcher;

	/**
	* Constructor
	*
	* @param \phpbb\config\config     $config Config object
	* @param \phpbb\template\template $template	  Template object
	* @param \phpbb\event\dispatcher  $dispatcher Dispatcher object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\template\template $template, \phpbb\event\dispatcher $dispatcher)
	{
		$this->config = $config;
		$this->template = $template;
		$this->dispatcher = $dispatcher;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_data()
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
	public function load()
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
