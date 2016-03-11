<?php

namespace Onepass\Common;

class HtmlEmbed {

	private $publisher_account;
	private $article;

	public function __construct(PublisherAccount $publisher_account) {
		$this->publisher_account 	= $publisher_account;
	}

	/**
	 * Get HTML for the button tag
	 * @return string
	 */
	public function getButtonTag(Article $article, $ts = null) {
		$ts				 = $ts ? $ts : time();
		$publishable_key = $this->publisher_account->getPublishableKey();
		$hash            = $this->publisher_account->buildHash( $article->unique_id, $ts );

		ob_start();
		include( 'Views/ButtonTag.php' );
		return ob_get_clean();
	}

	/**
	 * Get HTML for the JS tag (required for the button tag to work)
	 * @return string
	 */
	public function getJavaScriptTag() {
		$onepassDomain = $this->publisher_account->get1PassDomain();

		ob_start();
		include( 'Views/JavaScriptTag.php' );
		return ob_get_clean();
	}
}
