<?php

namespace Onepass\Common;

class Article {

	/**
	* @var string $url The url of the article
	* @var string $title The title of the article
	* @var string $author The author's name as a string
	* @var string $description A short description if available
	* @var string $unique_id A unique id for the article (like a guid)
	* @var string $published An ISO8601 date (eg 2016-02-12T03:21:55Z)
	* @var string $last_modified An ISO8601 date (eg 2016-02-12T03:21:55Z)
	* @var string $content The content of the article as HTML
	*/
	public $url, $title, $author, $description, $unique_id, $published, $last_modified, $content;

	public function __construct($attrs) {
		$this->url      		= $attrs['url'];
		$this->title    		= $attrs['title'];
		$this->author   		= $attrs['author'];
		$this->description  	= $attrs['description'];
		$this->unique_id    	= $attrs['unique_id'];
		$this->published		= $attrs['published'];
		$this->last_modified	= $attrs['last_modified'];
		$this->content			= isset( $attrs['content'] ) ? $attrs['content'] : null;
	}

}
