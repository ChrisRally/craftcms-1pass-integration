# 1Pass Common

This is a [Composer package](https://getcomposer.org) with helpers for building 1Pass PHP plugins.

The steps necessary for integration with 1Pass are documented in the [1Pass technical documentation](http://onepass-reference.herokuapp.com/).

# What's included in this package

## `HtmlEmbed`

[`HtmlEmbed`](https://github.com/1Pass/1pass-common/blob/master/lib/Onepass/Common/HtmlEmbed.php)

* generates HTML for the 1Pass JavaScript include (which loads 1Pass on the page)
* generates HTML for the 1Pass embed, given an article (necessary to render the 1Pass button on the page)

[Usage examples](https://github.com/1Pass/1pass-common/blob/master/tests/Onepass/Common/HtmlEmbedTest.php)

## `AtomFeed`

[`AtomFeed`](https://github.com/1Pass/1pass-common/blob/master/lib/Onepass/Common/AtomFeed.php)

* generates the content and the headers of the 1Pass Atom feed (which is used by 1Pass to consume and then to serve the publication's content)

[Usage examples](https://github.com/1Pass/1pass-common/blob/master/tests/Onepass/Common/AtomFeedTest.php)

**Note**: This class supports pagination but makes no assumptions about the naming conventions; it's the responsibility of the caller to provide appropriate `first`, `last`, `next` and `previous` links.

## `PublisherAccount`

 [`PublisherAccount`](https://github.com/1Pass/1pass-common/blob/master/lib/Onepass/Common/PublisherAccount.php)

 * builds the 1Pass signed hash (used in the construction of the 1Pass embed)
 * checks if an incoming request is an authentic 1Pass request (used in the authentication of the 1Pass Atom feed)

[Usage examples](https://github.com/1Pass/1pass-common/blob/master/tests/Onepass/Common/PublisherAccountTest.php)
