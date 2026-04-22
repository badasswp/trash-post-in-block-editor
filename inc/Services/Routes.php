<?php
/**
 * REST Service.
 *
 * This Service is responsible for binding REST routes to
 * WordPress. All routes are defined within the routes folder.
 * custom routes can also be injected here.
 *
 * @package TrashPostInBlockEditor
 */
namespace TrashPostInBlockEditor\Services;

use TrashPostInBlockEditor\Routes\Trash;
use TrashPostInBlockEditor\Abstracts\Service;

class Routes extends Service implements Kernel {
	/**
	 * REST routes
	 *
	 * @since 1.2.0
	 *
	 * @return mixed[]
	 */
	public array $routes;

	/**
	 * Set up
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->routes = [
			Trash::class,
		];
	}

	/**
	 * Bind to WP
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	/**
	 * Register Routes.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function register_rest_routes(): void {
		/**
		 * Filter list of WP REST Routes
		 *
		 * @since 1.2.0
		 *
		 * @param mixed[] $routes WP REST Routes
		 * @return mixed[]
		 */
		$this->routes = (array) apply_filters( 'tpbe_rest_routes', $this->routes );

		/**
		 * Specify Routes instance type
		 *
		 * @since 1.2.0
		 *
		 * @var parse $route
		 */
		foreach ( $this->routes as $route ) {
			( new $route() )->register_route();
		}
	}
}
