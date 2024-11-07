import { __ } from '@wordpress/i18n';
import { Fragment } from '@wordpress/element';
import { PanelBody, Button } from '@wordpress/components';
import { registerPlugin } from '@wordpress/plugins';
import { PluginSidebar } from '@wordpress/edit-post';

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
              onClick={ () => {} }
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
