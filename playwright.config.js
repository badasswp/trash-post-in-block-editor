import { defineConfig } from '@playwright/test';

export default defineConfig( {
	testDir: './tests/e2e',
	outputDir: './tests/e2e/test-results',
	globalSetup: './playwright.setup.js',
	use: {
		baseURL: 'http://tpbe.localhost:5438',
		headless: true,
		viewport: { width: 1280, height: 720 },
		ignoreHTTPSErrors: true,
		storageState: './tests/e2e/storage/storageState.json',
	},
} );
