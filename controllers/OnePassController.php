<?php


namespace Craft;


use Onepass\Common\AtomFeed;
use Onepass\Common\Article;
use Onepass\Common\PublisherAccount;


class OnePassController extends BaseController
{
	/**
	 * Enable anonymous access
	 * @var boolean
	 */
	protected $allowAnonymous = true;


	private $plugin, $settings, $credentials, $publisherAccount, $atomFeed;


	/**
	 * OnePassService constructor.
	 */
	public function __construct()
	{
		$this->plugin = craft()->plugins->getPlugin('onepass');
		$this->settings = $this->plugin->getSettings();


		$this->credentials = ["publishable_key" => $this->settings['apiPublishableKey'], "secret_key" => $this->settings['apiSecretKey']];


		$this->publisherAccount = new PublisherAccount($this->credentials, $this->settings['apiMode']);
	}


	/**
	 * Retrieves a server header attribute
	 *
	 * @param $header
	 * @return string
	 */
	public function getRequestHeader($header)
	{
		return array_key_exists($header, $_SERVER) ? $_SERVER[$header] : '';
	}


	/**
	 * Check for 1Pass authentication headers when feed is requested
	 *
	 * @return bool
	 */
	public function checkAuthenticationHeader()
	{
		// Get feed request header
		// When 1Pass requests the feed, it will attach X-ONEPASS-TIMESTAMP and X-ONEPASS-SIGNATURE headers.
		// Pass the current URL and the timestamp into the buildHash method of 1pass-client and compare with X-ONEPASS-SIGNATURE to determine whether you should reject or accept the request.


		$timestamp = $this->getRequestHeader('X-ONEPASS-TIMESTAMP');
		$signature = $this->getRequestHeader('X-ONEPASS-SIGNATURE');


		return ($this->publisherAccount->buildHash(craft()->getSiteUrl() . 'actions/onePass/feed', $timestamp) === $signature) ? true : false;
	}


	/**
	 * 1Pass Atom feed returning restricted content
	 *
	 * @return AtomFeed or null
	 * @throws Exception
	 */
	public function actionFeed()
	{
		if (($this->checkAuthenticationHeader() == true) || (craft()->request->getParam('skip_auth') == 1 && $this->settings['apiMode'] == 'demo')) {
			$section = craft()->sections->getSectionById($this->settings['section']);
			$contentField = $this->settings['contentField'];
			$paymentRequiredField = $this->settings['paymentRequiredField'];


			// Get the last updated entry from the section chosen in the plugin settings
			$criteria = craft()->elements->getCriteria(ElementType::Entry);
			$criteria->section = $section->handle;
			$criteria->$paymentRequiredField = true;
			$criteria->order = 'dateUpdated DESC';
			$criteria->limit = 1;


			$latestEntry = $criteria->first();


			$this->atomFeed = new AtomFeed([
				'publication_url' => craft()->getSiteUrl(),
				'publication_title' => craft()->getSiteName(),
				'updated_timestamp' => $latestEntry->dateUpdated->rfc3339(),
				'feed_id' => craft()->getSiteUrl() . 'actions/onePass/feed',
				'feed_full_url' => craft()->getSiteUrl() . 'actions/onePass/feed',
				'pagination_options' => null
			]);


			// Get a list of all entries from the section chosen in the plugin settings
			$criteria = craft()->elements->getCriteria(ElementType::Entry);
			$criteria->section = $section->handle;
			$criteria->$paymentRequiredField = true;
			$criteria->limit = null;


			$entries = $criteria->find();


			foreach ($entries as $entry) {
				$article = new Article([
					'url' => $entry->url,
					'unique_id' => craft()->getSiteUid() . ':' . $entry->slug . ':' . $entry->id,
					'title' => $entry->title,
					'author' => craft()->getSiteName(),
					'description' => substr(strip_tags($entry->$contentField), 0, 250),
					'published' => $entry->postDate->rfc3339(),
					'last_modified' => $entry->dateUpdated->rfc3339(),
					'content' => $entry->$contentField
				]);


				$this->atomFeed->addArticle($article);
			}


			echo $this->atomFeed->xml();
			exit();

		 } else {
		 	return null;
		 }
	}

}