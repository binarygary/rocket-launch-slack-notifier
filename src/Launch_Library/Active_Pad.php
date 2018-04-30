<?php

namespace BinaryGary\Rocket\Launch_Library;


class Active_Pad extends Active {

	const PAD_TRANSIENT = 'active_pads';

	public function get_active() {
		foreach ( $this->launches as $launch ) {
			foreach ( $launch->location->pads as $pad ) {
				$this->active[] = $pad->id;
			}
		}
	}

}