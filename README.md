# Dude Tweets feed
WordPress plugin to get latest tweets for user and/or hashtag.

Basically this plugin fetches tweets from Twitter, saves those to transient and next requests will be served from there. After transient has expired and deleted, new fetch from Twitter will be made and saved to transient. This implements very simple cache.

Handcrafted with love at [Digitoimisto Dude Oy](http://dude.fi), a Finnish boutique digital agency in the center of Jyväskylä.

Plugin uses [twitteroauth library](https://github.com/abraham/twitteroauth) made by [Abraham Williams](https://abrah.am/).

## Table of contents
1. [Please note before using](#please-note-before-using)
2. [License](#license)
3. [Usage](#usage)
  1. [Usage example for displaying a Twitter user feed](#usage-example-for-displaying-a-twitter-user-feed)
  2. [Limiting feed items](#limiting-feed-items)
4. [Hooks](#hooks)
5. [Composer](#composer)
6. [Contributing](#contributing)

## Please note before using
This plugin is not meant to be "plugin for everyone", it needs at least some basic knowledge about php and css to add it to your site and making it look beautiful.

This is a plugin in development for now, so it may update very often.

## License
Dude Tweets feed is released under the GNU GPL 2 or later.

## Usage
This plugin does not have settings page or provide anything visible on front-end. So it's basically dumb plugin if you don't use any filters listed below.

Use of [authentication](#authentication) filters is mandatory.

Get user tweets by calling function `dude_twitter_feed()->get_user_tweets()`, pass username as a only argument.
Get hashtag tweets by calling function `dude_twitter_feed()->get_hashtag_tweets()`, pass hashtag as a only argument.

### Usage example for displaying a Twitter user feed

1. Go to [apps.twitter.com](hhttps://apps.twitter.com/) and create app for your WordPress site
2. Get your **Consumer Key (API Key)** from **Details** tab and **Consumer Secret (API Secret)** from *Keys and Access Tokens* tab. In the bottom of *Keys and Access Tokens*, click **Create my access token**.
3. Add this snippet to **functions.php** and copy your tokens accordingly:

```php
/**
 * Init Twitter
 */
add_filter( 'dude-twitter-feed/oauth_consumer_key', function() { return 'token_here'; } );
add_filter( 'dude-twitter-feed/oauth_consumer_secret', function() { return 'token_here'; } );
add_filter( 'dude-twitter-feed/oauth_access_token', function() { return 'token_here'; } );
add_filter( 'dude-twitter-feed/oauth_access_token_secret', function() { return 'token_here'; } );
```

### Limiting feed items

```php
/**
 * Tweet settings
 */
add_filter( 'dude-twitter-feed/user_tweets_parameters', function( $args ) {
  $args['count'] = 4;

  return $args;
});
```

## Hooks
All the settings are set with filters, and there is also few filters to change basic functionality and manipulate data before caching.

## Authentication
Authentication is made with four different strings, you can obtain all of those from Twitter [application manager](https://apps.twitter.com/).

Filters are `dude-twitter-feed/oauth_consumer_key`, `dude-twitter-feed/oauth_consumer_secret`, `dude-twitter-feed/oauth_access_token` and `dude-twitter-feed/oauth_access_token_secret`.

Defaults to empty string.

##### `dude-twitter-feed/user_tweets_transient`
Change name of the transient for user tweets, must be unique for every user. Passed arguments are default name and username.

Defaults to `dude-twitter-user-$username`.

##### `dude-twitter-feed/user_tweets_endpoint`
Change endpoint for user tweets fetch. Only passed argument is default endpoint.

Defaults to `statuses/user_timeline`.

##### `dude-twitter-feed/user_tweets_parameters`
Modify api call parameters, example count of results. Only passed argument is array of default parameters.

Defaults to result count of five, retweet include and user information trimming.

Possible parameters are listed in Twitter [documentation](https://dev.twitter.com/rest/reference/get/statuses/user_timeline).

##### `dude-twitter-feed/user_tweets`
Manipulate or use data before it's cached to transient. Only passed argument is array of tweets.

##### `dude-twitter-feed/user_tweets_lifetime`
Change activity cache lifetime. Only passed argument is default lifetime in seconds.

Defaults to 600 (= ten minutes).

##### `dude-twitter-feed/hashtag_tweets_transient`
Change name of the transient for hashtag tweets, must be unique for every hashtag. Passed arguments are default name and hashtag.

Defaults to `dude-twitter-hashtag-$hashtag`.

##### `dude-twitter-feed/user_tweets_endpoint`
Change endpoint for hashtag tweets fetch. Only passed argument is default endpoint.

Defaults to `search/tweets`.

##### `dude-twitter-feed/hashtag_tweets_parameters`
Modify api call parameters, example count of results. Only passed argument is array of default parameters.

Defaults to result count of five.

Possible parameters are listed in Twitter [documentation](https://dev.twitter.com/rest/reference/get/search/tweets).

##### `dude-twitter-feed/hashtag_tweets`
Manipulate or use data before it's cached to transient. Only passed argument is array of tweets.

##### `dude-twitter-feed/hashtag_tweets_lifetime`
Change activity cache lifetime. Only passed argument is default lifetime in seconds.

Defaults to 600 (= ten minutes).

## Composer

To use with composer, run `composer require digitoimistodude/dude-twitter-feed dev-master` in your project directory or add `"digitoimistodude/dude-twitter-feed":"dev-master"` to your composer.json require.

## Contributing
If you have ideas about the theme or spot an issue, please let us know. Before contributing ideas or reporting an issue about "missing" features or things regarding to the nature of that matter, please read [Please note section](#please-note-before-using). Thank you very much.
