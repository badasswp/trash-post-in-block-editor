/* eslint-disable no-undef */
import '@testing-library/jest-dom';

jest.mock( '@wordpress/components', () => {
	const original = jest.requireActual( '@wordpress/components' );
	return {
		...original,
		Fill: ( { name, children } ) => (
			<div
				className={
					'PinnedItems/core' === name
						? 'interface-pinned-items'
						: 'interface-pinned'
				}
			>
				{ children }
			</div>
		),
	};
} );
