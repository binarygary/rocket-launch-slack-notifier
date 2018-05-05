<?php

namespace BinaryGary\Rocket;

use BinaryGary\Rocket\Service_Providers\Endpoints_Provider;
use BinaryGary\Rocket\Service_Providers\Launch_Library_Provider;
use BinaryGary\Rocket\Service_Providers\Post_Type_Provider;
use BinaryGary\Rocket\Service_Providers\Shortcodes_Provider;
use BinaryGary\Rocket\Service_Providers\Slack_Provider;
use BinaryGary\Rocket\Service_Providers\Twitter_Service_Provider;
use Pimple\Container;
use BinaryGary\Rocket\Service_Providers\Settings_Provider;

class Core {

	protected static $_instance;

	/** @var Container */
	protected $container = null;

	/** @var Service_Loader */
	protected $service_loader = null;

	/**
	 * @param Container $container
	 */
	public function __construct( $container ) {
		$this->container = $container;
	}

	static public function url() {
		return plugin_dir_url( __DIR__ );
	}

	public function init() {
		$this->load_service_providers();
	}

	private function load_service_providers() {
		$this->container->register( new Settings_Provider() );
		$this->container->register( new Endpoints_Provider() );
		$this->container->register( new Shortcodes_Provider() );
		$this->container->register( new Slack_Provider() );
		$this->container->register( new Post_Type_Provider() );
		$this->container->register( new Launch_Library_Provider() );
		$this->container->register( new Twitter_Service_Provider() );
	}

	public function container() {
		return $this->container;
	}

	/**
	 * @param null|\ArrayAccess $container
	 *
	 * @return Core
	 * @throws \Exception
	 */
	public static function instance( $container = null ) {
		if ( ! isset( self::$_instance ) ) {
			if ( empty( $container ) ) {
				throw new \Exception( 'You need to provide a Pimple container' );
			}

			$className       = __CLASS__;
			self::$_instance = new $className( $container );
		}

		return self::$_instance;
	}

}
