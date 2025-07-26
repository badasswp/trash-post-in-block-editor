import { render, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom';

import TrashPostInBlockEditor from '../src/index';
import { trashPost } from '../src/utils';

jest.mock( '../src/utils', () => ( {
	trashPost: jest.fn(),
} ) );

describe( 'TrashPostInBlockEditor', () => {
	beforeEach( () => {
		global.tpbe = {
			wpVersion: '6.6',
			url: 'https://example.com/wp-admin/edit.php',
		};
	} );

	afterEach( () => {
		jest.clearAllMocks();
	} );

	it( 'matches snapshot', () => {
		const { container } = render( <TrashPostInBlockEditor /> );

		expect( container ).toMatchSnapshot();
	} );

	it( 'renders trash button in pinned item area', () => {
		const { getByTestId } = render( <TrashPostInBlockEditor /> );

		// Test if Trash Post button is displayed.
		const trashButton = getByTestId( 'tpbe-trash-btn' );
		expect( trashButton ).toBeVisible();
	} );

	it( 'displays modal when trash button is clicked', () => {
		const { getByTestId, getByText, getByRole } = render(
			<TrashPostInBlockEditor />
		);

		// Click Trash Post button to open modal.
		fireEvent.click( getByTestId( 'tpbe-trash-btn' ) );

		// Test that modal content is displayed.
		expect(
			getByText( 'Are you sure you want to delete this Post?' )
		).toBeVisible();
		expect( getByRole( 'button', { name: 'Yes' } ) ).toBeVisible();
		expect( getByRole( 'button', { name: 'No' } ) ).toBeVisible();
	} );

	it( 'calls trashPost function if the `Yes` button is clicked', () => {
		const { getByTestId, getByText, getByRole } = render(
			<TrashPostInBlockEditor />
		);

		// Click Trash Post button to open modal.
		fireEvent.click( getByTestId( 'tpbe-trash-btn' ) );

		// Test that modal content is displayed.
		expect(
			getByText( 'Are you sure you want to delete this Post?' )
		).toBeVisible();
		expect( getByRole( 'button', { name: 'Yes' } ) ).toBeVisible();
		expect( getByRole( 'button', { name: 'No' } ) ).toBeVisible();

		// click Yes button.
		fireEvent.click( getByRole( 'button', { name: 'Yes' } ) );

		// Test that trashPost function is called.
		expect( trashPost ).toHaveBeenCalledTimes( 1 );
	} );

	it( 'closes modal if the `No` button is clicked', () => {
		const { getByTestId, getByText, getByRole, queryByText } = render(
			<TrashPostInBlockEditor />
		);

		// Click Trash Post button to open modal.
		fireEvent.click( getByTestId( 'tpbe-trash-btn' ) );

		// Test that modal content is displayed.
		expect(
			getByText( 'Are you sure you want to delete this Post?' )
		).toBeVisible();
		expect( getByRole( 'button', { name: 'Yes' } ) ).toBeVisible();
		expect( getByRole( 'button', { name: 'No' } ) ).toBeVisible();

		// Click No button.
		fireEvent.click( getByRole( 'button', { name: 'No' } ) );

		// Test that modal content is no longer in the document.
		expect(
			queryByText( 'Are you sure you want to delete this Post?' )
		).not.toBeInTheDocument();
	} );
} );
