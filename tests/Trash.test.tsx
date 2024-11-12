import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';

import TrashPostInBlockEditor from '../src/index';

const trashPost = jest.fn();

jest.mock( '@wordpress/editor', () => ( {
  PluginSidebar: jest.fn( ( { title, children } ) => {
    return (
      <div className="editor-sidebar">
        <h2 className="interface-complementary-area-header__title">{title}</h2>
        {children}
      </div>
    )
  } ),
} ) );

describe( 'TrashPostInBlockEditor', () => {
  beforeEach( () => {
    trashPost.mockClear();
  } );

  it( 'renders sidebar and Trash Post button', () => {
    const { container } = render( <TrashPostInBlockEditor /> );

    // Test if Sidebar title is displayed.
    expect( screen.getByText( 'Trash Post in Block Editor' ) ).toBeInTheDocument();

    // Test if Trash Post button is displayed.
    const trashButton = screen.getByRole( 'button', { name: 'Trash Post' } );
    expect( trashButton ).toBeInTheDocument();
  } );
} );
