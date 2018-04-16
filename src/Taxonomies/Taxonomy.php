<?php

namespace BinaryGary\Rocket\Taxonomies;

abstract class Taxonomy {

	protected $custom_save = [];

	public function register() {
		register_taxonomy( $this->taxonomy(), $this->post_types(), $this->args() );
		$this->defaults();
	}

	abstract public function taxonomy();

	abstract public function post_types();

	abstract public function args();

	public function defaults() {}

}
