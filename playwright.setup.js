import { chromium } from '@playwright/test';

export default async () => {
	const browser = await chromium.launch();
	const page = await browser.newPage();

	// Go to WP login page
	await page.goto( 'http://tpbe.localhost:5438/wp-login.php' );

	// Fill in login form (defaults for wp-env)
	await page.fill( '#user_login', 'admin' );
	await page.fill( '#user_pass', 'password' );
	await page.click( '#wp-submit' );

	// Make sure we’re logged in
	await page.waitForURL( '**/wp-admin/' );

	// Save storage state to file
	await page
		.context()
		.storageState( { path: './tests/e2e/storage/storageState.json' } );

	await browser.close();
};
