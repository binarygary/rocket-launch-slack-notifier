<?php
namespace BinaryGary\Rocket\V2\Space_Devs;

use BinaryGary\Rocket\Launch_Library\Launch;

class Retriever extends Cacheable {

	const ENDPOINT = 'https://ll.thespacedevs.com/2.0.0/launch/upcoming/?format=json&limit=5&mode=detailed';

	public function get_launches() {
		$result = $this->get( self::ENDPOINT );
		if ( is_wp_error( $result ) ) {
			return;
		}

		$launches = json_decode( $result['body'] );

		foreach( $launches->results as $launch ) {
			$launch_object = new Launch();


			$launch_object->set( 'title', 'Launch Notice' );
			$launch_object->set( 'color', '#42f4bc' );
			$launch_object->set( 'launch_name', $launch->name );
			$launch_object->set( 'vehicle', $launch->rocket->configuration->full_name );
//			launch_pad
//			description
//			netstamp
//			isonet
//			net
//			video_url

			error_log( 'space_devs launch: ' . print_r( $launch,1 ) );
		}
	}

}