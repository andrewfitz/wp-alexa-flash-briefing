=== WP Alexa Flash Briefing ===
Contributors: andrewfitz
Tags: alexa,amazon,flash briefing,voice
Donate link: https://www.paypal.me/andrewfitz
Requires at least: 4.7
Tested up to: 4.9.8
Requires PHP: 5.6
Stable tag: trunk
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

This plugin lets you create Amazon Alexa and Echo flash briefing posts and automatically generates an Alexa-formatted feed. Supports text and audio.

== Description ==
# Alexa Flash Briefing Plugin for Wordpress

This is a Wordpress plugin that creates a new post type of \"briefing\" that lets you create Alexa Flash Briefing compatible posts just like you would anything else in Wordpress. It uses these posts to create a feed for Alexa skills through the API.

Create your briefings like regular posts. Here\'s how it works to determine a text to speech or audio file:

* If you have ANY links/URLs in the post content, the plugin takes the FIRST link and uses it as the audio stream. It does not validate or catch errors!
* If there\'s no link in the post content, it will assume you want text to speech.

It is safest to just put a HTTPS url to your audio file, but you may add other text in the post if you are also sending people to your site to listen. Wordpress will oembed the MP3 file you post, so users will be able to listen on the web.

## Alexa Usage

Use this URL for your Alexa skill: `https://your-domain.com/wp-json/alexa-fb/v1/briefings/`

That will post the latest briefing from all categories (1). To change the limit, use `/wp-json/alexa-fb/v1/briefings/?limit=5` (be advised Amazon\'s limit is 5)

If you want to create multiple feeds, use categories.

Just add the category param to the end point: `/wp-json/alexa-fb/v1/briefings/?category=myflashbriefcat`

## Notes

* You MUST use a secure domain and secure links to your audio files (https for the audio files and the feed URL).
* Your briefing content shouldn\'t contain any HTML tags if you want to use TTS (text to speech).
* You can also draft and schedule briefings like regular posts.
* You can use the excerpt and featured image options if you will be sharing your briefing on social media without interfering with Alexa functionality.

(This plugin is not associated with Amazon in any way.)

== Screenshots ==
1. Make briefings just like posts

== Changelog ==
2018-12-2 - v1.5
-added banners and assets for wp.org

2018-09-21 - v1.4
-changed feed output limit default from 5->1
-added limit API endpoint param to change feed item amount

2018-09-18 - v1.3.3
-added featured image capability
-added excerpt capability
-spelling
-removed custom taxonomy, used standard wordpress categories instead for compatability

2018-09-17 - v1.2
-added transient cache for API feed with category support

2018-09-17 - v1.1
-Initial release