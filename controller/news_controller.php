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
use dls\web\core\news;
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

	/** @var news */
	protected $news;

	/**
	* Constructor
	*
	* @param helper	  $helper	Controller helper object
	* @param language $language Language object
	* @param news	  $news		News object
	* @param manager  $manager	Manager object
	*/
	public function __construct(helper $helper, language $language, news $news, manager $manager)
	{
		$this->helper = $helper;
		$this->language = $language;
		$this->news = $news;

		$manager->load();
	}

	/**
	* News controller to display news for routes:
	*
	*	 /news/{id}
	*	 /news/{id}/page/{page}
	*
	* @param int $id
	* @param int $page
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($id, $page)
	{
		$this->news->set_page($page);
		$this->news->base($id);

		$title = $this->language->lang('VIEW_NEWS', $id);

		return $this->helper->render('news.html', $title, 200, true);
	}

	/**
	* Article controller for route /article/{aid}
	*
	* @param int $aid
	* @throws \phpbb\exception\http_exception
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle2($aid)
	{
		$this->news->get_article($aid);

		$title = $this->language->lang('VIEW_ARTICLE', $aid);

		return $this->helper->render('article.html', $title, 200, true);
	}
}
