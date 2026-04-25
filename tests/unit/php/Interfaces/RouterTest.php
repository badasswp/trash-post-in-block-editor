<?php

namespace TrashPostInBlockEditor\Tests\Interfaces;

use WP_Mock;
use Mockery;
use WP_REST_Request;
use WP_Mock\Tools\TestCase;
use TrashPostInBlockEditor\Interfaces\Router;

/**
 * @covers TrashPostInBlockEditor\Interfaces\Router::is_user_permissible
 * @covers TrashPostInBlockEditor\Interfaces\Router::get_rest_response
 * @covers TrashPostInBlockEditor\Interfaces\Router::register_route
 */
class RouterTest extends TestCase {
	public Router $router;

	public function setUp(): void {
		WP_Mock::setUp();

		$this->router = $this->getMockForAbstractClass( Router::class );
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_is_user_permissible() {
		$this->router->expects( $this->once() )
			->method( 'is_user_permissible');

		$request = Mockery::mock( WP_REST_Request::class )->makePartial();

		$this->router->is_user_permissible( $request );

		$this->assertConditionsMet();
	}

	public function test_get_rest_response() {
		$this->router->expects( $this->once() )
			->method( 'get_rest_response' );

		$request = Mockery::mock( WP_REST_Request::class )->makePartial();

		$this->router->get_rest_response( $request );

		$this->assertConditionsMet();
	}

	public function test_register_route() {
		$this->router->expects( $this->once() )
			->method( 'register_route' );

		$this->router->register_route();

		$this->assertConditionsMet();
	}
}
