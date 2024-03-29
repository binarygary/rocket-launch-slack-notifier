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
        register_setting( self::SETTINGS_GROUP, self::PRIMARY_TEAM );

        $this->text_input_settings();
        $this->page_input_settings();
        
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
                $team_posts = wp_list_pluck( get_posts( [
                    'numberposts' => 1000,
                    'post_type'   => Slack_Team::POST_TYPE,
                ] ), 'ID' );

                $teams = [];
                foreach ( $team_posts as $team_post ) {
                    global $wpdb;

                    $token = get_the_content( $team_post );
                    $post_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%$token%'" );
                    $teams[ $post_id ] = get_post_meta( $post_id, 'response', true )->team_name;
                }

                asort( $teams );

                $selected = get_option( self::PRIMARY_TEAM );

                printf( '<select name="%s" id="%s">', self::PRIMARY_TEAM, self::PRIMARY_TEAM );
                foreach ( $teams as $id => $name ) {
                    printf( '<option class="level-0" value="%s" %s>%s</option>', $id, selected( $selected, $id, false ), $name );
                }
                printf( '</select>' );
            },
            self::SETTINGS_PAGE_NAME
        );
    }
}
