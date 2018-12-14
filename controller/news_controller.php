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

/**
* DLS Web news controller
*/
class news_controller
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \dls\web\core\news */
	protected $news;

	protected $category_ids = [2, 3, 4];

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper		   $helper Controoler helper object
	* @param \dls\web\core\news				   $news   News object
	* @param \dls\web\core\blocks\block_manager $Block Block object
	*/
	public function __construct(\phpbb\controller\helper $helper, \dls\web\core\news $news, \dls\web\core\blocks\block_manager $block)
	{
		$this->helper = $helper;
		$this->news	  = $news;

		$block->load();
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
		// Check news id
		if (!$id || !in_array($id, $this->category_ids))
		{
			throw new \phpbb\exception\http_exception(403, 'NO_FORUM', [$id]);
		}

		$this->news->set_page($page);
		$this->news->base($id);

		$title = sprintf($this->news->get('language')->lang('VIEW_NEWS'), $id);

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

		$title = sprintf($this->news->get('language')->lang('VIEW_ARTICLE'), $aid);

		return $this->helper->render('article.html', $title, 200, true);
	}
}
