<?php

namespace Members\Cap;

use Members\Tools\Collection;

class Groups extends Collection {

	public function add( $name, $value ) {

		parent::add( $name, new Group( $name, $value ) );
	}
}