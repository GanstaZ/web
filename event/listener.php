<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* DLS Web Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \dls\web\core\plugin\manager */
	protected $plugin;

	/**
	* Constructor
	*
	* @param \phpbb\config\config		  $config	Config object
	* @param \phpbb\controller\helper	  $helper	Controller helper object
	* @param \phpbb\language\language	  $language Language object
	* @param \phpbb\template\template	  $template Template object
	* @param \dls\web\core\plugin\manager $plugin	Plugin object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\language\language $language, \phpbb\template\template $template, \dls\web\core\plugin\manager $plugin)
	{
		$this->config	= $config;
		$this->helper	= $helper;
		$this->language = $language;
		$this->template = $template;
		$this->plugin	= $plugin;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	*/
	public static function getSubscribedEvents()
	{
		return [
			'core.user_setup'  => 'add_language',
			'core.page_header' => 'add_dls_web_data',
			'core.memberlist_prepare_profile_data' => 'prepare_profile_data',
			'core.memberlist_view_profile' => 'view_profile_stats',
		];
	}

	/**
	* Event core.user_setup
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function add_language($event)
	{
		// Load a single language file
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'dls/web',
			'lang_set' => 'common',
		];

		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	* Event core.page_header
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function add_dls_web_data()
	{
		$this->template->assign_vars([
			'U_NEWS' => $this->helper->route('dls_web_news_base'),
		]);
	}

	/**
	* Event core.memberlist_prepare_profile_data
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function prepare_profile_data($event)
	{
		$user_xp = $this->plugin->get('level')->get_member_exp($event['data']['user_posts']);

		$set_data = [
			'S_ZODIAC'		  => ($this->config['dls_zodiac']) ? 1 : 0,
			'S_LEVEL'		  => ($user_xp['level'] === 1) ? true : false,
			'U_LEVEL'		  => $user_xp['level'],
			'U_LEVEL_PERCENT' => $user_xp['percent'],
			'U_POINTS'		  => $user_xp['end'],
		];

		$event['template_data'] = array_merge($event['template_data'], $set_data);
	}

	/**
	* Event core.memberlist_view_profile
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function view_profile_stats($event)
	{
		$u_bday = $event['member']['user_birthday'];

		if ($this->config['allow_birthdays'] && $u_bday && $this->config['dls_zodiac'])
		{
			$this->language->add_lang(['zodiac', 'astro'], 'dls/web');

			$u_zodiac = $this->plugin->get('astro');

			// Format date
			$u_bday = str_replace(' ', '', $u_bday);
			$e_date = \DateTime::createFromFormat('d-m-Y', $u_bday);

			foreach ($u_zodiac->get_data('zodiac', $e_date) as $row)
			{
				$sign = $this->language->lang($row['sign']);
				$this->template->assign_block_vars('zodiac_data', [
					'sign'	=> $sign,
					'plant' => $this->language->lang($row['plant']),
					'gems'	=> $this->language->lang($row['gems']),
					'ruler' => $this->language->lang($row['ruler']),
					'extra' => $this->language->lang($row['extra'], $sign),
					'name'	=> $this->language->lang($row['name']),
				]);
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
