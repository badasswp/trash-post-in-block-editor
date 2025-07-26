/* eslint-disable no-undef */
import '@testing-library/jest-dom';
import '@testing-library/jest-dom/extend-expect.js';
import '@wordpress/jest-preset-default/scripts/setup-globals';

jest.mock( '@wordpress/components', () => {
	const original = jest.requireActual( '@wordpress/components' );
	return {
		...original,
		Fill: ( { children } ) => <div>{ children }</div>,
	};
} );
