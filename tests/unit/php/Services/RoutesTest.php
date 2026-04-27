<?php

namespace TrashPostInBlockEditor\Tests\Services;

use WP_Mock;
use WP_REST_Server;
use WP_Mock\Tools\TestCase;
use Mockery;

use TrashPostInBlockEditor\Services\Routes;
use TrashPostInBlockEditor\Routes\Trash;

/**
 * @covers TrashPostInBlockEditor\Services\Routes::__construct
 * @covers TrashPostInBlockEditor\Services\Routes::register
 * @covers TrashPostInBlockEditor\Services\Routes::register_rest_routes
 * @covers TrashPostInBlockEditor\Routes\Trash::register_route
 */
class RoutesTest extends TestCase {
	public Routes $routes;

	public function setUp(): void {
		WP_Mock::setUp();

		$this->routes = new Routes();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_register() {
		WP_Mock::expectActionAdded( 'rest_api_init', [ $this->routes, 'register_rest_routes' ] );

		$this->routes->register();

		$this->assertConditionsMet();
	}

	public function test_service_contains_routes_at_instantiation() {
		$this->assertSame(
			$this->routes->routes,
			[
				Trash::class,
			],
		);

		$this->assertConditionsMet();
	}

	public function test_register_rest_routes() {
		WP_Mock::expectFilter( 'tpbe_rest_routes', [ Trash::class ] );

		$trash = new Trash();

		WP_Mock::userFunction( 'register_rest_route' )
			->with(
				'tpbe/v1',
				'/trash',
				[
					'args'                => [
						'id' => [
							'validate_callback' => 'is_numeric',
							'sanitize_callback' => 'absint',
						],
					],
					'methods'             => 'POST',
					'callback'            => [ $trash, 'get_rest_response' ],
					'permission_callback' => [ $trash, 'is_user_permissible' ],
				]
			)
			->andReturn( null );

		$this->routes->register_rest_routes();

		$this->assertConditionsMet();
	}
}
