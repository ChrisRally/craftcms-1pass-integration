# Craft CMS 1Pass Integration

Add 1Pass integration to Craft CMS

-------------------------------------------

## Requirements

- [Craft 2.5+](https://craftcms.com/)

## Installation

1. Download the latest release of the plugin.
2. Drop the `onepass` plugin folder to `craft/plugins`.
3. Add your API keys to the 1Pass settings page. Get keys from [https://1pass.me](https://1pass.me).
4. Add a lightswitch field to the Section for entries you wish to require payment for, expects 'true' for payment required.
5. Define the section, content field and the lightswitch field setup above.
6. Atom feed will be available via http://yourdomain.com/actions/onePass/feed when requested with 1Pass headers.
7. For testing, when in demo mode, the feed can be viewed by adding ?skip_auth=true as an additional parameter to the feed URL.

## Templating

The 1Pass integration has a Twig variable named craft.onePass.getHTMLEmbedCode which expects

1. entry EntryModel.
2. strap, summary or brief description field name - for the 1Pass embed code specifically.
3. content body field name.
4. number of characters to limit the content body field before displaying the 1Pass button.

With your entry template place the following (as an example).

1. *entry.paymentRequired* refers to the lightswitch field setup to indicate entries requiring payment, used as an example.
2. *strap*, *body* and *entry.body* refers to the entry's content fields, used as an example.


    {% if entry.paymentRequired %}
	    {{ craft.onePass.getHTMLEmbedCode(entry, 'strap', 'body', 250) | raw }}

    {% else  %}
	    {{ entry.body }}
    {% endif %}