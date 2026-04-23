<?php
/**
 * Kernel Interface.
 *
 * Establish base methods for Concrete classes
 * used across plugin.
 *
 * @package TrashPostInBlockEditor
 */

namespace TrashPostInBlockEditor\Interfaces;

interface Kernel {
	/**
	 * Register logic.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function register(): void;
}
