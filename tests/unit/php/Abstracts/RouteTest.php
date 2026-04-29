<?php

namespace TrashPostInBlockEditor\Tests\Abstracts;

use WP_Mock;
use Mockery;
use WP_Error;
use WP_REST_Request;
use WP_Mock\Tools\TestCase;
use TrashPostInBlockEditor\Abstracts\Route;

/**
 * @covers TrashPostInBlockEditor\Abstracts\Route::is_user_permissible
 * @covers TrashPostInBlockEditor\Abstracts\Route::get_rest_response
 * @covers TrashPostInBlockEditor\Abstracts\Route::register_route
 */
class RouteTest extends TestCase {
	public Route $route;

	public function setUp(): void {
		WP_Mock::setUp();

		$this->route = $this->getMockForAbstractClass( Route::class );
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_is_user_permissible_returns_error_if_current_user_cannot_edit_posts() {
		WP_Mock::userFunction( 'rest_authorization_required_code' )
			->andReturn( 403 );

		WP_Mock::userFunction( 'current_user_can' )
			->with( 'edit_posts' )
			->andReturn( false );

		$wp_error = Mockery::mock( WP_Error::class )->makePartial();
		$request  = Mockery::mock( WP_REST_Request::class )->makePartial();
		$request->shouldAllowMockingProtectedMethods();

		$this->assertInstanceOf(
			WP_Error::class,
			$this->route->is_user_permissible( $request ),
		);
		$this->assertConditionsMet();
	}

	public function test_is_user_permissible_returns_error_if_nonce_fails() {
		WP_Mock::userFunction( 'rest_authorization_required_code' )
			->andReturn( 403 );

		WP_Mock::userFunction( 'current_user_can' )
			->with( 'edit_posts' )
			->andReturn( true );

		WP_Mock::userFunction( 'wp_verify_nonce' )
			->with( 'a8ceg59jeqwvk', 'wp_rest' )
			->andReturn( false );

		$wp_error = Mockery::mock( WP_Error::class )->makePartial();
		$request  = Mockery::mock( WP_REST_Request::class )->makePartial();
		$request->shouldAllowMockingProtectedMethods();

		$request->shouldReceive( 'get_header' )
			->with( 'X-WP-Nonce' )
			->andReturn( 'a8ceg59jeqwvk' );

		$this->assertInstanceOf(
			WP_Error::class,
			$this->route->is_user_permissible( $request ),
		);
		$this->assertConditionsMet();
	}

	public function test_is_user_permissible_passes_correctly() {
		WP_Mock::userFunction( 'rest_authorization_required_code' )
			->andReturn( 403 );

		WP_Mock::userFunction( 'current_user_can' )
			->with( 'edit_posts' )
			->andReturn( true );

		$request = Mockery::mock( WP_REST_Request::class )->makePartial();
		$request->shouldAllowMockingProtectedMethods();

		$request->shouldReceive( 'get_header' )
			->with( 'X-WP-Nonce' )
			->andReturn( 'a8ceg59jeqwvk' );

		WP_Mock::userFunction( 'wp_verify_nonce' )
			->with( 'a8ceg59jeqwvk', 'wp_rest' )
			->andReturn( true );

		$this->assertTrue( $this->route->is_user_permissible( $request ) );
		$this->assertConditionsMet();
	}

	public function test_get_rest_response() {
		$this->route->expects( $this->once() )
			->method( 'get_rest_response' );

		$request = Mockery::mock( WP_REST_Request::class )->makePartial();
		$this->route->get_rest_response( $request );

		$this->assertConditionsMet();
	}

	public function test_register_route() {
		$this->route->expects( $this->once() )
			->method( 'register_route' );

		$this->route->register_route();

		$this->assertConditionsMet();
	}
}
