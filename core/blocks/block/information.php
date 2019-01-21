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
class information extends base
{
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
		$this->helper->assign('vars', [
			'phpbb_version' => (string) $this->config['version'],
			'core_stable'	=> (string) $this->config['dls_core_version'],
		]);
	}
}
