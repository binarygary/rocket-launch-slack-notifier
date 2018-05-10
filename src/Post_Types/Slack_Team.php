<?php

namespace BinaryGary\Rocket\Post_Types;


class Slack_Team extends Post_Type {

	const POST_TYPE = 'slack_team';

	public function post_type() {
		return self::POST_TYPE;
	}

	public function args() {
		return [
			'public' => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'labels'       => [
				'menu_name' => __( 'Slack Team', 'tribe' ),
			],
		];
	}

	public function add_column( $columns ) {
		$columns['team_name'] = 'Team Name';
		return $columns;
	}

	public function populate_column( $column, $post_id ) {
		if ( 'team_name' !== $column ) {
			return;
		}

		global $wpdb;

		$token = get_the_content( $post_id );
		$post_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%$token%'" );

		$meta = get_post_meta( $post_id, 'response', true );

		if ( isset ( $meta->team_name ) ) {
			return $meta->team_name;
		}
	}

}