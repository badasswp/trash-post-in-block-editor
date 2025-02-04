import { __ } from '@wordpress/i18n';
import { PluginSidebar } from '@wordpress/editor';
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
const TrashPostInBlockEditor = (): JSX.Element => {
  const [ isModalVisible, setIsModalVisible ] = useState( false );

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
              onClick={ () => setIsModalVisible( true ) }
            >
              { __( 'Trash Post', 'trash-post-in-block-editor' ) }
            </Button>
          </div>
        </PanelBody>
      </PluginSidebar>
      {
        isModalVisible && (
          <Modal
            title={ __( 'Trash Post', 'trash-post-in-block-editor' ) }
            onRequestClose={ () => setIsModalVisible( false ) }
            className="trash-post-modal"
          >
            <p style={{ textAlign: 'center' }}>
              { __( 'Are you sure you want to delete this Post?', 'trash-post-in-block-editor' ) }
              </p>
            <div id="trash-post-modal__button-group">
              <Button
                variant="primary"
                onClick={trashPost}
              >
                { __( 'Yes', 'trash-post-in-block-editor' ) }
              </Button>
              <Button
                variant="secondary"
                onClick={ () => setIsModalVisible( false ) }
              >
                { __( 'No', 'trash-post-in-block-editor' ) }
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

export default TrashPostInBlockEditor;
