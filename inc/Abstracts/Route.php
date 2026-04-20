<?php
/**
 * Route Abstraction.
 *
 * This abstraction defines the base logic from which all
 * Route classes are derived.
 *
 * @package TrashPostInBlockEditor
 */
namespace TrashPostInBlockEditor\Abstracts;

use TrashPostInBlockEditor\Abstracts\Service;
use TrashPostInBlockEditor\Interfaces\Kernel;

abstract class Route extends Service implements Kernel {
	/**
	 * Register to WP.
	 *
	 * Bind concrete logic to WP here.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	abstract public function register(): void;
}
