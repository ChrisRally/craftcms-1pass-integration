<?php

namespace Onepass\Common;

use PHPUnit_Framework_TestCase;

class AtomFeedTest extends PHPUnit_Framework_TestCase {
    private $expectedAtomFeed = <<<STR
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:thr="http://purl.org/syndication/thread/1.0" xml:lang="en-US" xml:base="http://www.example.com">
  <title type="text">The Example</title>
  <updated>2016-02-14T16:53:57Z</updated>
  <id>http://example.org/feed/atom/1pass/</id>
  <link rel="self" type="application/atom+xml" href="http://example.org/feed/atom/1pass/?paged=3"/>
  <link rel="first" href="http://www.example.com/atom/feed/1pass/?paged=1"/>
  <link rel="last" href="http://www.example.com/atom/feed/1pass/?paged=5"/>
  <link rel="previous" href="http://www.example.com/atom/feed/1pass/?paged=2"/>
  <link rel="next" href="http://www.example.com/atom/feed/1pass/?paged=4"/>
  <entry>
    <author>
      <name>Bill Shakespeare</name>
    </author>
    <title type="html"><![CDATA[To be or not to be]]></title>
    <link rel="alternate" type="text/html" href="http://www.example.com/to-be-or-not-to-be"/>
    <id>http://www.example.com/articles/12345</id>
    <updated>2016-02-14T16:53:57Z</updated>
    <published>2016-02-14T15:53:57Z</published>
    <summary type="html"><![CDATA[Lorem ipsum dolor sit amet, ex vis error consetetur. Vix ea possim molestie liberavisse, etiam ubique doming his ex, mei altera prodesset eu]]></summary>
    <content type="html" xml:base="http://www.example.com/to-be-or-not-to-be"><![CDATA[Ei eam everti sensibus laboramus, civibus molestiae philosophia eu vel, duo at esse instructior. Per graece consequuntur ei. Tota posse erroribus est ex, id docendi offendit pro. Eos id eius splendide, has vide cetero eloquentiam ei.]]></content>
  </entry>
</feed>

STR;

	private $atomFeed;

	public function setUp() {
		$this->atomFeed = new AtomFeed([
			'publication_url'    => 'http://www.example.com',
			'publication_title'  => 'The Example',
			'updated_timestamp'  => '2016-02-14T16:53:57Z',
			'feed_id'            => 'http://example.org/feed/atom/1pass/',
			'feed_full_url'      => 'http://example.org/feed/atom/1pass/?paged=3',
			'pagination_options' => [
				'first_page_href' => 'http://www.example.com/atom/feed/1pass/?paged=1',
				'last_page_href' => 'http://www.example.com/atom/feed/1pass/?paged=5',
				'previous_page_href' => 'http://www.example.com/atom/feed/1pass/?paged=2',
				'next_page_href' => 'http://www.example.com/atom/feed/1pass/?paged=4',
			],
		]);
	}

	public function testFeedXMLGeneration() {
		$article = new Article([
			'url' => "http://www.example.com/to-be-or-not-to-be",
			'title' => "To be or not to be",
			'author' => "Bill Shakespeare",
			'description' => "Lorem ipsum dolor sit amet, ex vis error consetetur. Vix ea possim molestie liberavisse, etiam ubique doming his ex, mei altera prodesset eu",
			'unique_id' => 'http://www.example.com/articles/12345',
			'published' => '2016-02-14T15:53:57Z',
			'last_modified' => '2016-02-14T16:53:57Z',
			'content' => 'Ei eam everti sensibus laboramus, civibus molestiae philosophia eu vel, duo at esse instructior. Per graece consequuntur ei. Tota posse erroribus est ex, id docendi offendit pro. Eos id eius splendide, has vide cetero eloquentiam ei.',
		]);

		$this->atomFeed->addArticle($article);

		$this->assertEquals($this->expectedAtomFeed, $this->atomFeed->xml());
	}

	public function testAddingMultipleArticlesToFeed() {
		$article = new Article([
			'url' => "http://www.example.com/to-be-or-not-to-be",
			'title' => "To be or not to be",
			'author' => "Bill Shakespeare",
			'description' => "Lorem ipsum dolor sit amet, ex vis error consetetur. Vix ea possim molestie liberavisse, etiam ubique doming his ex, mei altera prodesset eu",
			'unique_id' => 'http://www.example.com/articles/12345',
			'published' => '2016-02-14T15:53:57Z',
			'last_modified' => '2016-02-14T16:53:57Z',
			'content' => 'Ei eam everti sensibus laboramus, civibus molestiae philosophia eu vel, duo at esse instructior. Per graece consequuntur ei. Tota posse erroribus est ex, id docendi offendit pro. Eos id eius splendide, has vide cetero eloquentiam ei.',
		]);

		$this->atomFeed->addArticle($article);

		$article = new Article([
			'url' => "http://www.example.com/tomorrow-and-tomorrow-and-tomorrow",
			'title' => "To be or not to be",
			'author' => "Bill Shakespeare",
			'description' => "Out, out, brief candle!",
			'unique_id' => 'http://www.example.com/articles/out-out',
			'published' => '2016-02-14T15:57:57Z',
			'last_modified' => '2016-02-14T16:57:57Z',
			'content' => 'Tomorrow and tomorrow and tomorrow/ Creeps in this petty pace from day to day',
		]);

		$this->atomFeed->addArticle($article);

		$xml = $this->atomFeed->xml();

		$el = new \SimpleXMLElement( $xml );

		$this->assertEquals(count($el->entry), 2);
	}

	public function testHeaders() {
		$expectedHeaders = [
			'Cache-Control: private, no-cache',
			'Content-Type: application/atom+xml; charset=UTF-8'
		];
		$this->assertEquals($expectedHeaders, $this->atomFeed->headers());
	}
}
