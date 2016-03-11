<?php

namespace Onepass\Common;

class AtomFeed {

	private $charset, $html_media_type, $language, $publication_url, $publication_title,
	$updated_timestamp, $feed_id, $feed_full_url, $pagination_options;

	private $articles = [];

	public function __construct($attrs) {
		$this->charset = isset($attrs['charset']) ? $attrs['charset'] : 'UTF-8';
		$this->html_media_type = isset($attrs['html_media_type']) ? $attrs['html_media_type'] : 'text/html';
		$this->language = isset($attrs['language']) ? $attrs['language'] : 'en-US';

		$this->publication_url = $attrs['publication_url'];
		$this->publication_title = $attrs['publication_title'];
		$this->updated_timestamp = $attrs['updated_timestamp'];
		$this->feed_id = $attrs['feed_id'];
		$this->feed_full_url = $attrs['feed_full_url'];
		$this->pagination_options = $attrs['pagination_options'];
	}

	public function addArticle($article) {
		$this->articles[] = $article;
	}

	public function headers() {
		return [
			'Cache-Control: private, no-cache',
			'Content-Type: application/atom+xml; charset=' . $this->charset
		];
	}

	public function xml() {
		$feed = $this->initFeed();
		$this->addMetadataElementsTo($feed);
		$this->addPaginationLinks($feed);
		foreach ($this->articles as $article) {
			$this->addArticleToFeed($article, $feed);
		}

		$feed->endElement(); // 'feed'
		$feed->endDocument();

		return $feed->outputMemory();
	}

	private function initFeed() {
		$feed = new \XmlWriter();

		$feed->openMemory();
		$feed->setIndent(true);
		$feed->setIndentString('  ');
		$feed->startDocument('1.0', 'UTF-8');

		$feed->startElement('feed');

		$feed->writeAttribute('xmlns', "http://www.w3.org/2005/Atom");
		$feed->writeAttribute('xmlns:thr', "http://purl.org/syndication/thread/1.0");
		$feed->writeAttribute('xml:lang', $this->language);
		$feed->writeAttribute('xml:base', $this->publication_url);

		return $feed;
	}

	private function addMetadataElementsTo($feed) {
		$feed->startElement('title');
		$feed->writeAttribute('type', 'text');
		$feed->text($this->publication_title);
		$feed->endElement();

		$feed->writeElement('updated', $this->updated_timestamp);
		$feed->writeElement('id', $this->feed_id);
		$feed->startElement('link');
		foreach( [ 'rel'=>'self', 'type'=>'application/atom+xml', 'href'=>$this->feed_full_url ] as $key => $val ) {
			$feed->writeAttribute($key, $val);
		}
		$feed->endElement();
	}

	private function addPaginationLinks($feed) {
		$links = [ 'first', 'last', 'previous', 'next' ];
		foreach ($links as $relType) {
			if( isset( $this->pagination_options[$relType . '_page_href'] ) ) {
				$feed->startElement('link');
				$feed->writeAttribute('rel', $relType);
				$feed->writeAttribute('href', $this->pagination_options[$relType . '_page_href']);
				$feed->endElement();
			}
		}
	}

	private function addArticleToFeed($article, $feed) {
		$entry = $feed->startElement('entry');

		// author
		$author_name = $article->author;
		if ( !empty($author_name) ) {
			$feed->startElement('author');
			$feed->writeElement('name', $author_name);
			$feed->endElement();
		}

		// title
		$feed->startElement('title');
		$feed->writeAttribute('type', $this->rss_html_type());
		$feed->writeCData($article->title);
		$feed->endElement();

		// other stuff
		$feed->startElement('link');
		foreach( [ 'rel' => 'alternate', 'type' => $this->html_media_type, 'href' => $article->url ] as $key => $val ) {
			$feed->writeAttribute($key, $val);
		}
		$feed->endElement();
		$feed->writeElement('id', $article->unique_id);
		$feed->writeElement('updated', $article->last_modified);
		$feed->writeElement('published', $article->published);

		// summary
		$feed->startElement('summary');
		$feed->writeAttribute('type', $this->rss_html_type());
		$feed->writeCData($article->description);
		$feed->endElement();

		// content
		$feed->startElement('content');
		$feed->writeAttribute('type', $this->rss_html_type());
		$feed->writeAttribute('xml:base', $article->url);
		$feed->writeCData($article->content);
		$feed->endElement();

		$feed->endElement(); // 'entry'
	}

	function rss_html_type() {
		if (strpos($this->html_media_type, 'xhtml') !== false)
		return 'xhtml';
		else
		return 'html';
	}
}
