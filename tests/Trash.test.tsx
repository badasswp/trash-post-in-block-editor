import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
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

  it( 'displays modal when Trash Post button is clicked', () => {
    render( <TrashPostInBlockEditor /> );

    // Click Trash Post button to open modal.
    fireEvent.click( screen.getByRole('button', { name: 'Trash Post' } ) );

    // Test that modal content is displayed.
    expect( screen.getByText( 'Are you sure you want to delete this Post?') ).toBeInTheDocument();
    expect( screen.getByRole( 'button', { name: 'Yes' } ) ).toBeInTheDocument();
    expect( screen.getByRole( 'button', { name: 'No' } ) ).toBeInTheDocument();
  } );
} );
