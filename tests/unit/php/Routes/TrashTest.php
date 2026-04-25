<?php

namespace TrashPostInBlockEditor\Tests\Routes;

use Mockery;
use WP_Mock;
use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use WP_Mock\Tools\TestCase;
use TrashPostInBlockEditor\Routes\Trash;

/**
 * @covers TrashPostInBlockEditor\Routes\Trash::register_route
 * @covers TrashPostInBlockEditor\Routes\Trash::get_rest_response
 */
class TrashTest extends TestCase {
	public Trash $trash;

	public function setUp(): void {
		WP_Mock::setUp();

		$this->trash = new Trash();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_register_route() {
		WP_Mock::userFunction( 'register_rest_route' )
			->andReturnUsing(
				function ( $name_space, $route, $args ) {
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

		$this->trash->register_route();

		$this->assertConditionsMet();
	}

	public function test_get_rest_response_returns_error_when_delete_fails() {
		$request = Mockery::mock( WP_REST_Request::class )->makePartial();
		$request->shouldAllowMockingProtectedMethods();

		$request->shouldReceive( 'get_json_params' )
			->once()
			->andReturn(
				[
					'id' => 1,
				]
			);

		WP_Mock::userFunction( 'wp_delete_post' )
			->with( 1 )
			->once()
			->andReturn( false );

		$wp_error = Mockery::mock( WP_Error::class )->makePartial();

		$response = $this->trash->get_rest_response( $request );

		$this->assertInstanceOf( WP_Error::class, $response );
		$this->assertConditionsMet();
	}

	public function test_get_rest_response_returns_success_when_delete_succeeds() {
		$request = Mockery::mock( WP_REST_Request::class )->makePartial();
		$request->shouldAllowMockingProtectedMethods();

		$request->shouldReceive( 'get_json_params' )
			->once()
			->andReturn(
				[
					'id' => 1,
				]
			);

		WP_Mock::userFunction( 'wp_delete_post' )
			->with( 1 )
			->once()
			->andReturn( true );

		WP_Mock::userFunction( 'rest_ensure_response' )
			->with( [ 'ID' => 1 ] )
			->once()
			->andReturn( [ 'ID' => 1 ] );

		$response = $this->trash->get_rest_response( $request );

		$this->assertSame( [ 'ID' => 1 ], $response );
		$this->assertConditionsMet();
	}
}
