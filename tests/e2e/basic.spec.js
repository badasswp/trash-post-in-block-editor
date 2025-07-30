import { test, expect } from '@wordpress/e2e-test-utils-playwright';

export async function createNewPost( page ) {
	await page.goto( '/wp-admin/post-new.php' );
	await page.waitForSelector( '.edit-post-layout' );
}

test.describe( 'Trash Post', () => {
	test.beforeEach( async ( { page } ) => {
		createNewPost( page );
	} );

	test( 'displays the trash icon', async ( { page } ) => {
		const trashButton = page.getByTestId( 'tpbe-trash-btn' );

		await expect( trashButton ).toBeVisible();
	} );

	test( 'displays the modal when the trash icon is clicked', async ( {
		page,
	} ) => {
		const trashButton = page.getByTestId( 'tpbe-trash-btn' );

		await expect( trashButton ).toBeVisible();

		await trashButton.click();

		const modalCaption = page.getByText(
			'Are you sure you want to delete this Post?'
		);
		const yesButton = page.getByRole( 'button', {
			name: 'Yes',
			exact: true,
		} );

		await expect( modalCaption ).toBeVisible();
		await expect( yesButton ).toBeVisible();
	} );

	test( 'trashes the post when the YES button is clicked', async ( {
		page,
	} ) => {
		const trashButton = page.getByTestId( 'tpbe-trash-btn' );

		await expect( trashButton ).toBeVisible();

		await trashButton.click();

		const modalCaption = page.getByText(
			'Are you sure you want to delete this Post?'
		);
		const yesButton = page.getByRole( 'button', {
			name: 'Yes',
			exact: true,
		} );

		await expect( modalCaption ).toBeVisible();
		await expect( yesButton ).toBeVisible();

		await yesButton.click();

		await expect(
			page.getByRole( 'heading', { name: 'Posts', exact: true } )
		).toBeVisible();
	} );
} );
