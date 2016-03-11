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


	public function actionFeed()
	{
		if (craft()->request->getParam('skip_auth') == 1) {
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