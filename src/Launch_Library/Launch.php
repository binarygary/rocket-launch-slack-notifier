<?php

namespace BinaryGary\Rocket\Launch_Library;


class Launch {

	const LAUNCH = 'launch';

	protected $title;
	protected $launch_name;
	protected $description;
	protected $color = '#42f4bc';
	protected $netstamp;
	protected $isonet;
	protected $net;
	protected $vehicle;
	protected $launch_pad;
	protected $video_button;
	protected $video_url;

	protected function required_fields() {
		return [
			$this->launch_name,
			$this->vehicle,
			$this->launch_pad,
			$this->isonet,
			$this->net,
		];
	}

	public function set( $param, $value ) {
		if ( self::LAUNCH === $param ) {
			$this->setup_launch( $value );

			return;
		}

		$this->$param = $value;
	}

	private function setup_launch( $launch ) {
		isset( $launch->name ) ? $this->set( 'name', $launch->name ) : null;
		isset( $launch->rocket->name ) ? $this->set( 'vehicle', $launch->rocket->name ) : null;
		isset( $launch->location->nam ) ? $this->set( 'launch_pad', $launch->location->name ) : null;
		isset( $launch->missions[0]->description ) ? $this->set( 'description', $launch->missions[0]->description ) : null;
		isset( $launch->netstamp ) ? $this->set( 'netstamp', $launch->netstamp ) : null;
		isset( $launch->isonet ) ? $this->set( 'isonet', $launch->isonet ) : null;
		isset( $launch->net ) ? $this->set( 'net', $launch->net ) : null;
		isset( $launch->vidURLs[0] ) ? $this->set( 'video_url', $launch->vidURLs[0] ) : null;
	}

	public function message() {
		if ( ! $this->required_fields_set() ) {
			throw new \Exception( 'Required params were not met' );
		}

		$message['attachments'][0] = [
			'pretext' => $this->title,
			'color'   => $this->color,
			'title'   => $this->launch_name,
			'fields'  => [
				[
					'title' => 'Net Launch',
					'value' => sprintf( '<!date^%s^{date_num} {time}|%s>', $this->netstamp, $this->net ),
					'short' => false,
				],
				[
					'title' => 'Vehicle',
					'value' => $this->vehicle,
					'short' => false,
				],
				[
					'title' => 'Launch Pad',
					'value' => $this->launch_pad,
					'short' => false,
				],
			],
		];

		if ( ! ( $this->netstamp ) ) {
			$message['attachments'][0]['fields'][0] = [
				'title' => 'Expected Launch Date',
				'value' => sprintf( '<!date^%s^{date_num}|%s>', strtotime( $this->isonet ), $this->net ),
				'short' => false,
			];
		}

		if ( isset( $this->description ) ) {
			$message['attachments'][0]['text'] = $this->description;
		}

		if ( isset( $this->video_button ) ) {
			$message['attachments'][0]['actions'] = [
				'type' => 'button',
				'text' => $this->video_button,
				'url'  => $this->video_url,
			];
		}

		return $message;
	}

	private function required_fields_set() {
		foreach ( $this->required_fields() as $field ) {
			if ( ! isset( $this->$field ) ) {
				return false;
			}
		}

		return true;
	}

}