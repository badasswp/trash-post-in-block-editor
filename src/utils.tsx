import apiFetch from '@wordpress/api-fetch';
import { applyFilters, doAction } from '@wordpress/hooks';
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
export const trashPost = async (): Promise< void > => {
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

		/**
		 * Fires after post is deleted.
		 *
		 * @since 1.1.0
		 *
		 * @param {number} postId      Post ID.
		 * @param {string} redirectUrl Redirect URL.
		 *
		 * @return {void}
		 */
		doAction( 'tpbe.afterTrashPost', getCurrentPostId(), tpbe.url );

		window.location.href = `${ tpbe.url }`;
	} catch ( e ) {
		createWarningNotice( e.message );
	}
};

/**
 * Get ShortCut.
 *
 * This function filters the user's preferred
 * shortcut option.
 *
 * @since 1.0.5
 *
 * @return {Object} Shortcut Option.
 */
export const getShortcut = (): { modifier: string; character: string } => {
	const options = {
		CMD: {
			modifier: 'primary',
			character: 'v',
		},
		SHIFT: {
			modifier: 'primaryShift',
			character: 'v',
		},
		ALT: {
			modifier: 'primaryAlt',
			character: 'v',
		},
	};

	/**
	 * Filter Keyboard Shortcut.
	 *
	 * By default the passed option would be SHIFT which
	 * represents `CMD + SHIFT + V`.
	 *
	 * @since 1.0.5
	 *
	 * @param {Object} Shortcut Option.
	 * @return {Object}
	 */
	return applyFilters(
		'trash-post-in-block-editor.keyboardShortcut',
		options.SHIFT
	) as { modifier: string; character: string };
};
