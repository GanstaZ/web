<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\event;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use dls\web\core\helper as dls_helper;
use dls\web\core\plugins\manager as plugins;
use dls\web\core\blocks\manager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* DLS Web Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var config */
	protected $config;

	/** @var helper */
	protected $helper;

	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var dls_helper */
	protected $dls_helper;

	/** @var plugins */
	protected $plugin;

	/** @var manager */
	protected $manager;

	/**
	* Constructor
	*
	* @param config		$config		Config object
	* @param helper		$helper		Controller helper object
	* @param language	$language	Language object
	* @param request	$request	Request object
	* @param template	$template	Template object
	* @param dls_helper $dls_helper Dls helper object
	* @param plugins	$plugin		Plugin object
	* @param manager	$manager	Blocks manager object
	*/
	public function __construct(
		config $config,
		helper $helper,
		language $language,
		request $request,
		template $template,
		dls_helper $dls_helper,
		plugins $plugin,
		manager $manager = null
	)
	{
		$this->config	  = $config;
		$this->helper	  = $helper;
		$this->language	  = $language;
		$this->request	  = $request;
		$this->template	  = $template;
		$this->dls_helper = $dls_helper;
		$this->plugin	  = $plugin;
		$this->manager	  = $manager;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	*/
	public static function getSubscribedEvents(): array
	{
		return [
			'core.user_setup'		=> 'add_language',
			'core.user_setup_after' => 'add_manager_data',
			'core.page_header'		=> 'add_dls_web_data',
			'core.acp_manage_forums_request_data'  => 'web_manage_forums_request_data',
			'core.acp_manage_forums_display_form'  => 'web_manage_forums_display_form',
			'core.memberlist_prepare_profile_data' => 'prepare_profile_data',
			'core.memberlist_view_profile'		   => 'view_profile_stats',
		];
	}

	/**
	* Event core.user_setup
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function add_language($event): void
	{
		// Load a single language file from dls/web/language/en/common.php
		$this->language->add_lang('common', 'dls/web');
	}

	/**
	* Event core.user_setup_after
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function add_manager_data($event): void
	{
		if ($this->config['dls_blocks'] && $get_page_data = $this->dls_helper->get_page_data())
		{
			// Set page var for template, so we know where we are
			$this->template->assign_var('S_DLS_PAGE', true);

			// Load available blocks
			$this->manager->load($get_page_data);

			// Assign template vars
			foreach ($get_page_data as $s_page)
			{
				$this->template->assign_vars([
					$s_page => $this->manager->has($s_page),
				]);
			}
		}
	}

	/**
	* Event core.page_header
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function add_dls_web_data(): void
	{
		$this->template->assign_vars([
			'U_NEWS' => $this->helper->route('dls_web_news_base'),
		]);
	}

	/**
	* Event core.acp_manage_forums_request_data
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function web_manage_forums_request_data($event): void
	{
		$forum_data = $event['forum_data'];
		$forum_data['news_fid_enable'] = $this->request->variable('news_fid_enable', 0);
		$event['forum_data'] = $forum_data;
	}

	/**
	* Event core.acp_manage_forums_display_form
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function web_manage_forums_display_form($event): void
	{
		$template_data = $event['template_data'];
		$template_data['S_NEWS_FID'] = $event['forum_data']['news_fid_enable'];
		$event['template_data'] = $template_data;
	}

	/**
	* Event core.memberlist_prepare_profile_data
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function prepare_profile_data($event): void
	{
		$user_xp = $this->plugin->get('achievement')['level']->get_member_exp($event['data']['user_posts']);

		$event['template_data'] = array_merge($event['template_data'], [
			'S_LEVEL'		  => ($user_xp['level'] === 1) ? true : false, // Will be fixed later (refactoring)
			'U_LEVEL'		  => $user_xp['level'],
			'U_LEVEL_PERCENT' => $user_xp['percent'],
			'U_POINTS'		  => $user_xp['end'],
		]);
	}

	/**
	* Event core.memberlist_view_profile
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function view_profile_stats($event): void
	{
		$u_bday = $event['member']['user_birthday'];

		if ($this->config['allow_birthdays'] && $u_bday && $this->config['dls_zodiac'])
		{
			$this->language->add_lang(['zodiac', 'astro'], 'dls/web');

			// Format date
			$u_bday = str_replace(' ', '', $u_bday);
			$date = \DateTime::createFromFormat('d-m-Y', $u_bday);

			foreach ($this->plugin->get('zodiac') as $service)
			{
				foreach ($service->load($date->format($service->get_format())) as $row)
				{
					$this->template->assign_block_vars('zodiac_data', [
						'stem'	  => $row['stem'],
						'sign'	  => $row['sign'],
						'symbol'  => $row['symbol'],
						'plant'	  => $row['plant'],
						'gem'	  => $row['gem'],
						'ruler'	  => $row['ruler'],
						'extra'	  => $row['extra'],
						'dir'	  => $row['dir'],
						'element' => $row['element'],
						'name'	  => $row['name'],
					]);
				}
			}
		}

		$member = $event['member']['user_regdate'];
		$memberdays = max(1, round((time() - $member) / 86400));

		$this->template->assign_vars([
			'S_MEMBER_DAYS' => ($memberdays == 1) ? true : false,
			'MEMBER_DAYS'	=> $memberdays,
		]);
	}
}
