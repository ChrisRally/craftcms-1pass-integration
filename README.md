# Craft CMS 1Pass Integration

Add 1Pass integration to Craft CMS

-------------------------------------------

## Requirements

- [Craft 2.5+](https://craftcms.com/)

## Installation

1. Download the latest release of the plugin.
2. Drop the `onepass` plugin folder to `craft/plugins`.
3. Add your API keys to the 1Pass settings page. Get keys from [https://1pass.me](https://1pass.me).
4. Add a lightswitch field to the Section for entries you wish to require payment for.
5. Define the section, content field and the lightswitch field setup above.
6. Atom feed should be available via http://yourdomain.com/actions/onePass/feed when requested with 1Pass headers.
7. For testing, when in demo mode, the feed can be viewed by adding ?skip_auth=true as an additional parameter to the feed URL.
