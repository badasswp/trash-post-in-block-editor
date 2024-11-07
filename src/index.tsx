import { __ } from '@wordpress/i18n';
import { PluginSidebar } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { Fragment, useState } from '@wordpress/element';
import { Modal, PanelBody, Button } from '@wordpress/components';

import { trashPost } from './utils';

import './styles/app.scss';

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
  const [ isModalVisible, setIsModalVisible ] = useState(false);

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
              onClick={ () => setIsModalVisible(true) }
            >
              { __( 'Trash Post', 'trash-post-in-block-editor' ) }
            </Button>
          </div>
        </PanelBody>
      </PluginSidebar>
      {
        isModalVisible && (
          <Modal
            title={ __( 'Trash Post', 'search-replace-for-block-editor' ) }
            onRequestClose={ () => setIsModalVisible(false) }
            className="trash-post-modal"
          >
            <p>Are you sure you want to delete this Post?</p>
            <div id="trash-post-modal__button-group">
              <Button
                variant="primary"
                onClick={trashPost}
              >
                { __( 'Yes' ) }
              </Button>
              <Button
                variant="secondary"
                onClick={ () => setIsModalVisible(false) }
              >
                { __( 'No' ) }
              </Button>
            </div>
          </Modal>
        )
      }
    </Fragment>
  );
};

registerPlugin( 'trash-post-in-block-editor', {
  render: TrashPostInBlockEditor,
} );
