<?php

namespace BinaryGary\Rocket\Service_Providers;


use BinaryGary\Rocket\Post_Types\Events;
use BinaryGary\Rocket\Post_Types\Feedback;
use BinaryGary\Rocket\Post_Types\Message_Log;
use BinaryGary\Rocket\Post_Types\Slack_Team;
use BinaryGary\Rocket\Post_Types\Slack_URL;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Post_Type_Provider implements ServiceProviderInterface {

	const SLACK_URL   = 'post_types.slack_urls';
	const SLACK_TEAM  = 'post_types.slack_team';
	const MESSAGE_LOG = 'post_types.message_log';
	const EVENTS      = 'post_types.events';
	const FEEDBACK    = 'post_types.feedback';

	public function register( Container $container ) {
		$container[ self::SLACK_URL ]   = function () {
			return new Slack_URL();
		};
		$container[ self::SLACK_TEAM ]  = function () {
			return new Slack_Team();
		};
		$container[ self::MESSAGE_LOG ] = function () {
			return new Message_Log();
		};
		$container[ self::EVENTS ] = function () {
			return new Events();
		};
		$container[ self::FEEDBACK ] = function () {
			return new Feedback();
		};

		add_action( 'init', function () use ( $container ) {
			$container[ self::SLACK_URL ]->register();
			$container[ self::SLACK_TEAM ]->register();
			$container[ self::MESSAGE_LOG ]->register();
			$container[ self::EVENTS ]->register();
			$container[ self::FEEDBACK ]->register();
		} );
	}

}
