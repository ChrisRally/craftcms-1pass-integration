# Craft CMS 1Pass Integration

Add 1Pass integration to Craft CMS

-------------------------------------------

## Requirements

- [Craft 2.5+](https://craftcms.com/)

## Installation

1. Download the latest release of the plugin.
2. Drop the plugin files into `craft/plugins/onepass`.
3. Add your API keys to the 1Pass settings page. Get keys from [https://1pass.me](https://1pass.me).
4. In `Settings > Fields`, define a Lightswitch field (eg `paymentRequired`) that expects 'true' for payment required.
5. In `Settings > Sections`, add the Lightswitch to the Section for entries you wish to require payment for.
6. Return to the 1Pass settings page to specify the Section, the content field that contains the content for sale, and the name of the Lightswitch field (eg `paymentRequired`).
7. 1Pass fetches the full text of your content for paying users via an Atom feed. This feed will be available via http://yourdomain.com/actions/onePass/feed.

NB By default the Atom feed requires signed 1Pass headers to display. For testing, the feed can be viewed by ensuring the 1Pass plugin is in 'Demo API mode' and adding ?skip_auth=true as an additional parameter to the feed URL, eg `http://yourdomain.com/actions/onePass/feed?skip_auth=true`

## Templating

The 1Pass integration has a Twig variable named craft.onePass.getHTMLEmbedCode which expects

1. entry EntryModel.
2. strap, summary or brief description field name - for the 1Pass embed code specifically.
3. content body field name.
4. number of characters to limit the content body field before displaying the 1Pass button.

With your entry template place the following (as an example).

1. *entry.paymentRequired* refers to the lightswitch field setup to indicate entries requiring payment, used as an example.
2. *strap*, *body* and *entry.body* refers to the entry's content fields, used as an example.

```
{% if entry.paymentRequired %}
	{{ craft.onePass.getHTMLEmbedCode(entry, 'strap', 'body', 250) | raw }}

{% else  %}
	{{ entry.body }}
{% endif %}
```
