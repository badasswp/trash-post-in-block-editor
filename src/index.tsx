import { __ } from '@wordpress/i18n';
import { Fragment, useState } from '@wordpress/element';
import { registerPlugin } from '@wordpress/plugins';
import { PluginSidebar } from '@wordpress/edit-post';
import { Modal, PanelBody, Button } from '@wordpress/components';

import { trashPost } from './utils';

/**
 * Trash Post In Block Editor.
 *
 * This function returns a JSX component that comprises
 * the Plugin Sidebar and Trash icon.
 *
 * @since 1.0.0
 *
 * @returns {JSX.Element}
 */
const TrashPostInBlockEditor = () => {
  const { getCurrentPostId } = select('core/editor');

  const trashPost = async () => {
    const postID = getCurrentPostId();

    try {
      await apiFetch(
        {
          path: '/tpbe/v1/trash',
          method: 'POST',
          data: {
            id: postID
          },
        }
      );

      window.location.href = `${tpbe.url}`
    } catch (e) {
      console.log(e);
    }
  }

  return (
    <Fragment>
      <PluginSidebar
        name="tpbe-sidebar"
        title={ __( 'Trash Post in Block Editor', 'trash-post-in-block-editor' ) }
        icon="trash"
      >
        <PanelBody>
          <div id="tpbe">
            <Button
              variant="primary"
              onClick={trashPost}
            >
              { __( 'Trash Post', 'trash-post-in-block-editor' ) }
            </Button>
          </div>
        </PanelBody>
      </PluginSidebar>
    </Fragment>
  );
};

registerPlugin( 'trash-post-in-block-editor', {
  render: TrashPostInBlockEditor,
} );
