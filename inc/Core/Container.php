<?php
/**
 * Container class.
 *
 * This class is responsible for registering the
 * plugin services.
 *
 * @package TrashPostInBlockEditor
 */

namespace TrashPostInBlockEditor\Core;

use TrashPostInBlockEditor\Interfaces\Kernel;
use TrashPostInBlockEditor\Services\Boot;
use TrashPostInBlockEditor\Services\Routes;

class Container implements Kernel {
	/**
	 * Services.
	 *
	 * @since 1.2.0
	 *
	 * @return mixed[]
	 */
	public static array $bindings = [];

	/**
	 * Prepare Singleton.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		static::$bindings = [
			Boot::class,
			Routes::class,
		];
	}

	/**
	 * Register Service.
	 *
	 * Establish singleton version for each service
	 * concrete class.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function register(): void {
		foreach ( static::$bindings as $binding ) {
			( $binding::get_instance() )->register();
		}
	}
}
