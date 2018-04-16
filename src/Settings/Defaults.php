<?php

namespace BinaryGary\Rocket\Settings;

class Defaults {

	const SETTINGS_PAGE_NAME = 'rocket-launch-slack-notifier';

	const SETTINGS_GROUP = 'rocket-launch-slack-notifier-group';

	const SLACK_APP_ID = 'slack_app_id';

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
			self::SLACK_APP_ID => __( 'Slack APP ID', 'textsmash' ),
		];
	}

	public function register_settings() {
		register_setting( self::SETTINGS_GROUP, self::SLACK_APP_ID );

		$this->input_settings();

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

	private function input_settings() {
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

}
