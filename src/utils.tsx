import apiFetch from '@wordpress/api-fetch';
import { select, dispatch } from '@wordpress/data';

/**
 * Trash Post
 *
 * This function trashes the post based
 * on the Post ID.
 *
 * @since 1.0.0
 *
 * @return {void}
 */
export const trashPost = async () => {
	const { getCurrentPostId } = select( 'core/editor' );
	const { createWarningNotice } = dispatch( 'core/notices' ) as any;

	try {
		await apiFetch( {
			path: '/tpbe/v1/trash',
			method: 'POST',
			data: {
				id: getCurrentPostId(),
			},
		} );

		window.location.href = `${ tpbe.url }`;
	} catch ( e ) {
		createWarningNotice( e );
	}
};
