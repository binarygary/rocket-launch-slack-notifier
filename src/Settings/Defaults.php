<?php

namespace BinaryGary\Rocket\Settings;

use BinaryGary\Rocket\Post_Types\Slack_Team;
use Slack\Team;

class Defaults {

	const SETTINGS_PAGE_NAME = 'rocket-launch-slack-notifier';

	const SETTINGS_GROUP = 'rocket-launch-slack-notifier-group';

	const SLACK_APP_ID     = 'slack_app_id';
	const SLACK_APP_SECRET = 'slack_app_secret';

	const SUCCESS_MESSAGE = 'slack_success_message';

	const SUCCESS_PAGE = 'slack_api_success';
	const FAILURE_PAGE = 'slack_api_failure';

	const TWITTER_CONUMER_KEY    = 'twitter_consumer_key';
	const TWITTER_CONUMER_SECRET = 'twitter_consumer_secret';
	const ACCESS_TOKEN           = 'twitter_access_token';
	const ACCESS_TOKEN_SECRET    = 'twitter_access_token_secret';

	const PRIMARY_TEAM = 'rocket-launch-primary-team';

	public function create_menu() {
		add_submenu_page(
			'options-general.php',
			__( 'Rocket App Settings', 'tribe' ),
			__( 'Rocket App Settings', 'tribe' ),
			'edit_posts',
			self::SETTINGS_PAGE_NAME,
			[ $this, 'default_settings' ]
		);
	}

	public function text_fields() {
		return [
			self::SLACK_APP_ID     => __( 'Slack APP ID', 'tribe' ),
			self::SLACK_APP_SECRET => __( 'Slack APP Secret', 'tribe' ),
			self::SUCCESS_MESSAGE  => __( 'Success Message', 'tribe' ),
		];
	}

	public function twitter_text_fields() {
		return [
			self::TWITTER_CONUMER_KEY    => __( 'Twitter Consumer Key', 'tribe' ),
			self::TWITTER_CONUMER_SECRET => __( 'Twitter Consumer Secret', 'tribe' ),
			self::ACCESS_TOKEN           => __( 'Twitter Access Token', 'tribe' ),
			self::ACCESS_TOKEN_SECRET    => __( 'Twitter Access Token Secret', 'tribe' ),
		];
	}

	public function page_fields() {
		return [
			self::SUCCESS_PAGE => __( 'Success Page', 'tribe' ),
			self::FAILURE_PAGE => __( 'Failure Page', 'tribe' ),
		];
	}

	public function register_settings() {
		register_setting( self::SETTINGS_GROUP, self::SLACK_APP_ID );
		register_setting( self::SETTINGS_GROUP, self::SLACK_APP_SECRET );
		register_setting( self::SETTINGS_GROUP, self::SUCCESS_MESSAGE );
		register_setting( self::SETTINGS_GROUP, self::SUCCESS_PAGE );
		register_setting( self::SETTINGS_GROUP, self::FAILURE_PAGE );
		register_setting( self::SETTINGS_GROUP, self::TWITTER_CONUMER_KEY );
		register_setting( self::SETTINGS_GROUP, self::TWITTER_CONUMER_SECRET );
		register_setting( self::SETTINGS_GROUP, self::ACCESS_TOKEN );
		register_setting( self::SETTINGS_GROUP, self::ACCESS_TOKEN_SECRET );
		register_setting( self::SETTINGS_GROUP, self::PRIMARY_TEAM );

		$this->text_input_settings();
		$this->page_input_settings();

		$this->twitter_text_input_settings();

		$this->add_primary_team_selector();

	}

	public function default_settings() {

		?>
		<div class="wrap">
			<h1>Rocket App Settings</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::SETTINGS_GROUP );
				do_settings_sections( self::SETTINGS_PAGE_NAME );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	private function text_input_settings() {
		foreach ( $this->text_fields() as $field => $title ) {
			add_settings_section(
				$field . '_section',
				$title,
				function () use ( $field ) {
					printf( '<input value="%s" name="%s">',
						get_option( $field ),
						$field
					);
				},
				self::SETTINGS_PAGE_NAME
			);
		}
	}

	private function twitter_text_input_settings() {
		foreach ( $this->twitter_text_fields() as $field => $title ) {
			add_settings_section(
				$field . '_section',
				$title,
				function () use ( $field ) {
					printf( '<input value="%s" name="%s">',
						get_option( $field ),
						$field
					);
				},
				self::SETTINGS_PAGE_NAME
			);
		}
	}

	private function page_input_settings() {
		foreach ( $this->page_fields() as $field => $title ) {
			add_settings_section(
				$field . '_section',
				$title,
				function () use ( $field ) {
					wp_dropdown_pages( [
						'name'     => $field,
						'selected' => get_option( $field ),
					] );
				},
				self::SETTINGS_PAGE_NAME
			);
		}
	}

    private function add_primary_team_selector() {
        add_settings_section(
            self::PRIMARY_TEAM . '_section',
            __( 'Primary Team', 'gary' ),
            function () {
                $teams = get_posts( [
                    'numberposts' => 1000,
                    'post_type'   => Slack_Team::POST_TYPE,
                ] );

                printf( '<select name="%s" id="%s">', self::PRIMARY_TEAM, self::PRIMARY_TEAM );
                foreach ( $teams as $team ) {
                    printf( '<option class="level-0" value="%s">%s</option>', $team->ID, get_post_meta( $team->ID, 'response', true )->team_name );
                }
                printf( '</select>' );
            },
            self::SETTINGS_PAGE_NAME
        );
    }
}
