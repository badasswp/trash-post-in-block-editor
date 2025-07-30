import { __ } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';
import { useCallback } from '@wordpress/element';
import { useShortcut } from '@wordpress/keyboard-shortcuts';

import { getShortcut } from '../utils';

/**
 * Shortcut for Block Editor.
 *
 * This method implements the custom shortcut
 * functionality for this plugin.
 *
 * @since 1.0.1
 *
 * @param {Object}   props           - The properties object.
 * @param {Function} props.onKeyDown - The function to call when the shortcut is triggered.
 *
 * @return {JSX.Element|null} Shortcut.
 */
export const Shortcut = ( { onKeyDown } ): JSX.Element | null => {
	const dispatch = useDispatch();

	dispatch( 'core/keyboard-shortcuts' ).registerShortcut( {
		name: 'trash-post-in-block-editor/shortcut',
		keyCombination: getShortcut(),
		category: 'global',
		description: __(
			'Trash Post in Block Editor',
			'trash-post-in-block-editor'
		),
	} );

	useShortcut(
		'trash-post-in-block-editor/shortcut',
		useCallback( () => {
			onKeyDown();
			// eslint-disable-next-line react-hooks/exhaustive-deps
		}, [] )
	);

	return null;
};
