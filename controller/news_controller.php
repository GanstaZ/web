<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\controller;

use phpbb\controller\helper;
use phpbb\language\language;
use dls\web\core\blocks\manager;

/**
* DLS Web news controller
*/
class news_controller
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
		$this->helper = $helper;
		$this->language = $language;
		$this->manager = $manager;

		$this->manager->load();
	}

	/**
	* News controller for routes:
	*
	*	 /news/{id}
	*	 /news/{id}/page/{page}
	*
	* @param int $id
	* @param int $page
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle(int $id, int $page)
	{
		// Check if news is disabled
		if (!$this->manager->get('dls_news'))
		{
			throw new \phpbb\exception\http_exception(404, 'DISABLED');
		}

		$this->manager->get('dls_news')
			->set_page($page)
			->trim_news(true)
			->base($id);

		return $this->helper->render('news.html', $this->language->lang('VIEW_NEWS', $id), 200, true);
	}

	/**
	* Article controller for route /article/{aid}
	*
	* @param int $aid
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function article(int $aid)
	{
		// Check if news is disabled
		if (!$this->manager->get('dls_news'))
		{
			throw new \phpbb\exception\http_exception(404, 'DISABLED');
		}

		$this->manager->get('dls_news')
			->get_article($aid);

		return $this->helper->render('article.html', $this->language->lang('VIEW_ARTICLE', $aid), 200, true);
	}
}
