<?php

namespace TrashPostInBlockEditor\Tests\Services;

use WP_Mock;
use WP_REST_Server;
use WP_Mock\Tools\TestCase;

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
		WP_Mock::onFilter( 'tpbe_rest_routes' )
			->with( [ Trash::class ] )
			->reply( [ Trash::class ] );

		WP_Mock::userFunction( 'register_rest_route' )
			->andReturnUsing(
				function ( $names_pace, $route, $args ) {
					$validate = $args['args']['id']['validate_callback'];

					$this->assertSame( 'tpbe/v1', $name_space );
					$this->assertSame( '/trash', $route );
					$this->assertSame( WP_REST_Server::CREATABLE, $args['methods'] );
					$this->assertSame( 'absint', $args['args']['id']['sanitize_callback'] );
					$this->assertTrue( is_callable( $validate ) );
					$this->assertTrue( $validate( '123' ) );
					$this->assertFalse( $validate( 'abc' ) );
				}
			);

		$this->routes->register_rest_routes();

		$this->assertConditionsMet();
	}
}
