<?php


namespace Craft;


use Onepass\Common\Article;
use Onepass\Common\PublisherAccount;
use Onepass\Common\HtmlEmbed;


/**
 * 1Pass Integration Service
 */
/**
 * Class OnePassService
 * @package Craft
 */
class OnePassService extends BaseApplicationComponent
{
	private $plugin, $settings, $credentials, $publisherAccount;


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
	 * Output the 1pass button, JS and a summary of the content wrapped in an identifiable class for 1Pass to replace.
	 *
	 *
	 * @param EntryModel $entry
	 * @param $summaryField
	 * @param $contentField
	 * @param $charLimit
	 * @return string
	 */
	public function getHTMLEmbedCode(EntryModel $entry, $summaryField, $contentField, $charLimit)
	{
		$embed = new HtmlEmbed($this->publisherAccount);


		$charLimit = (is_numeric($charLimit)) ? $charLimit : 200;


		$article = new Article([
			'url' => $entry->url,
			'unique_id' => craft()->getSiteUrl() . craft()->getSiteUid() . ':' . $entry->slug . ':' . $entry->id,
			'title' => $entry->title,
			'author' => craft()->getSiteName(),
			'description' => strip_tags($entry->$summaryField),
			'published' => $entry->postDate->iso8601(),
			'last_modified' => $entry->dateUpdated->iso8601()
		]);


		$onePassButtonHtml = $embed->getButtonTag($article, time());
		$onePassButtonJS = $embed->getJavaScriptTag();


		$ctaHtml = '<div class="article-body">';
			$ctaHtml .= '<p>' . substr(strip_tags($entry->$contentField), 0, $charLimit) . '...</p>';
			$ctaHtml .= $onePassButtonHtml;
		$ctaHtml .= '</div>';


		$ctaHtml .= $onePassButtonJS;


		return $ctaHtml;
	}
}