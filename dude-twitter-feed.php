<?php
/**
 * Plugin Name: Dude Tweets feed
 * Plugin URL: https://www.dude.fi
 * Description: Fetches the latest tweets for spesified users and hashtgs
 * Version: 0.2.0
 * Author: Timi Wahalahti / DUDE
 * Author URL: http://dude.fi
 * Requires at least: 4.4.2
 * Tested up to: 4.4.2
 *
 * Text Domain: dude-twitter-feed
 * Domain Path: /languages
 */

if( !defined( 'ABSPATH' )  )
	exit();

require 'twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

Class Dude_Twitter_Feed {
  private static $_instance = null;

  /**
   * Construct everything and begin the magic!
   *
   * @since   0.1.0
   * @version 0.1.0
   */
  public function __construct() {
    // Add actions to make magic happen
    add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
  } // end function __construct

  /**
   *  Prevent cloning
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dude-twitter-feed' ) );
	} // end function __clone

  /**
   *  Prevent unserializing instances of this class
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function __wakeup() {
    _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dude-twitter-feed' ) );
  } // end function __wakeup

  /**
   *  Ensure that only one instance of this class is loaded and can be loaded
   *
   *  @since   0.1.0
   *  @version 0.1.0
	 *  @return  Main instance
   */
  public static function instance() {
    if( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }

    return self::$_instance;
  } // end function instance

  /**
   *  Load plugin localisation
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function load_plugin_textdomain() {
    load_plugin_textdomain( 'dude-twitter-feed', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
  } // end function load_plugin_textdomain

	public function get_user_tweets( $twitter_handle = '' ) {
		if( empty( $twitter_handle ) )
			return;

		$transient_name = apply_filters( 'dude-twitter-feed/user_tweets_transient', 'dude-twitter-user-'.$twitter_handle, $twitter_handle );
		$tweets = get_transient( $transient_name );
	  if( !empty( $tweets ) || false != $tweets )
	    return $tweets;

		$endpoint = apply_filters( 'dude-twitter-feed/user_tweets_endpoint', 'statuses/user_timeline' );
		$parameters = array(
			'screen_name'	=> $twitter_handle,
			'count'				=> '5',
			'include_rts'	=> 'true',
			'trim_user'		=> true,
		);

		$response = self::_call_api( $endpoint, apply_filters( 'dude-twitter-feed/user_tweets_parameters', $parameters ) );
		if( $response === FALSE )
			return;

		$response = apply_filters( 'dude-twitter-feed/user_tweets', $response );
		set_transient( $transient_name, $response, apply_filters( 'dude-twitter-feed/user_tweets_lifetime', '600' ) );

		return $response;
	} // end function get_users_tweets

	public function get_hashtag_tweets( $hashtag = '' ) {
		if( empty( $hashtag ) )
			return;

		$transient_name = apply_filters( 'dude-twitter-feed/hashtag_tweets_transient', 'dude-twitter-hashtag-'.$hashtag, $hashtag );
		$tweets = get_transient( $transient_name );
	  if( !empty( $tweets ) || false != $tweets )
	    return $tweets;

		$endpoint = apply_filters( 'dude-twitter-feed/hashtag_tweets_endpoint', 'search/tweets' );
		$parameters = array(
			'q'			=> $hashtag,
			'count'	=> '5',
		);

		$response = self::_call_api( $endpoint, apply_filters( 'dude-twitter-feed/hashtag_tweets_parameters', $parameters ) );
		if( $response === FALSE )
			return;

		$response = apply_filters( 'dude-twitter-feed/hashtag_tweets', $response );
		set_transient( $transient_name, $response, apply_filters( 'dude-twitter-feed/hashtag_tweets_lifetime', '600' ) );

		return $response;
	} // end function get_hashtag_tweets
	
	public function get_user_info( $screen_name = '' ) {
		if( empty( $screen_name ) )
			return;

		$transient_name = apply_filters( 'dude-twitter-feed/user_info_transient', 'dude-twitter-userinfo-'.$screen_name, $screen_name );
		$info = get_transient( $transient_name );
	  if( !empty( $info ) || false != $info )
	    return $info;

		$endpoint = apply_filters( 'dude-twitter-feed/user_info_endpoint', 'users/show' );
		$parameters = array(
			'screen_name'  => $screen_name
		);

		$response = self::_call_api( $endpoint, apply_filters( 'dude-twitter-feed/user_info_parameters', $parameters ) );
		if( $response === FALSE )
			return;

		$response = apply_filters( 'dude-twitter-feed/user_info', $response );
		set_transient( $transient_name, $response, apply_filters( 'dude-twitter-feed/user_info_lifetime', '600' ) );

		return $response;
	} // end function get_user_info

	private function _call_api( $endpoint = '', $parameters = array() ) {
		if( empty( $endpoint ) || empty( $parameters ) )
			return false;

		$connection = new TwitterOAuth(
			apply_filters( 'dude-twitter-feed/oauth_consumer_key', '' ),
			apply_filters( 'dude-twitter-feed/oauth_consumer_secret', '' ),
			apply_filters( 'dude-twitter-feed/oauth_access_token', '' ),
			apply_filters( 'dude-twitter-feed/oauth_access_token_secret', '' )
		);

		$content = $connection->get("account/verify_credentials");
		$response = $connection->get( $endpoint, $parameters );

		if( $connection->getLastHttpCode() !== 200 ) {
			self::_write_log( 'response status code not 200 OK, function: '.$endpoint );
			return false;
		}

		return $response;
	} // end function _call_api

	private function _write_log ( $log )  {
    if( true === WP_DEBUG ) {
      if( is_array( $log ) || is_object( $log ) ) {
        error_log( print_r( $log, true ) );
      } else {
        error_log( $log );
      }
    }
  } // end _write_log
} // end class Dude_Twitter_Feed

function dude_twitter_feed() {
  return new Dude_Twitter_Feed();
} // end function dude_twitter_feed
