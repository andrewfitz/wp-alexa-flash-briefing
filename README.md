# Alexa Flash Briefing Plugin for Wordpress

This is a Wordpress plugin that creates a new post type of "briefing" that lets you create Alexa Flash Briefing compatible posts just like you would anything else in Wordpress. It uses these posts to create a feed for Alexa skills through the API.

You must use a secure domain and secure links to to your audio files (https for the audio files and the feed URL)

Create your briefings like regular posts. Here's how it works to determine a text to speach or audio file. If you have ANY links/URLs in the post content, the plugin takes the FIRST link and uses it as the audio stream. If there's no link in the post content, it will assume you want text to speach.

It is safest to just put a HTTPS url to your audio file, but you may add other text in the post if you are also sending people to your site to listen. Wordpress will oembed the MP3 file you post, so users will be able to listen on the web.

Use this URL for your Alexa skill:

https://your-domain.com/wp-json/alexa-fb/v1/briefings/
