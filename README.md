# trash-post-in-block-editor
Delete a Post from within the WP Block Editor.

<img width="446" alt="trash-post-in-block-editor" src="https://github.com/user-attachments/assets/909ee6cf-954a-4975-96d8-2210dd33e954">

## Download

Download from [WordPress plugin repository](https://wordpress.org/plugins/trash-post-in-block-editor/).

You can also get the latest version from any of our [release tags](https://github.com/badasswp/trash-post-in-block-editor/releases).

## Why Trash Post in Block Editor?

This plugin provides a quick way to __delete__ or __trash__ a Post from __within__ the __Block Editor__. Previously, the only way to achieve this required a user to exit the article or post before deleting. Not anymore! Now, from the comfort of your Block Editor, you could easily delete the Post you're working on with ease.

https://github.com/user-attachments/assets/f02442d8-ff46-49d3-9bb7-9907b8d7174b

### Hooks

#### `tpbe_redirect_url`

This custom hook provides a simple way to filter the redirect URL the user is taken to after the post is trashed or deleted.

```php
add_filter( 'tpbe_redirect_url', [ $this, 'custom_redirect_url' ] );

public function custom_redirect_url( $url ): string {
    $site_url = 'https://example.com/redirect';

    if ( false === strpos( $url, $site_url ) ) {
        return esc_url( $site_url );
    }

    return $url;
}
```

**Parameters**

- url _`{string}`_ By default, this will be a the redirect URL the user is taken to after a post is trashed.
<br/>

#### `tpbe.afterTrashPost`

This custom hook (action) provides the ability to fire events after the post is trashed on the JS side:

```js
import { addAction } from '@wordpress/hooks';

addAction(
	'tpbe.afterTrashPost',
	'your-namespace',
	( postId, redirectUrl ) => {
		wp.data.dispatch( YOUR_STORE ).performSomeAction( postId );
	}
);
```

**Parameters**

- postId _`{number}`_ The trashed Post ID.
- redirectUrl _`{string}`_ The Redirect URL where the user is taken after the post is trashed.
<br/>

---

## Contribute

Contributions are __welcome__ and will be fully __credited__. To contribute, please fork this repo and raise a PR (Pull Request) against the `master` branch.

### Pre-requisites

You should have the following tools before proceeding to the next steps:

- Composer
- Yarn
- Docker

To enable you start development, please run:

```bash
yarn start
```

This should spin up a local WP env instance for you to work with at:

```bash
http://tpbe.localhost:5437
```

You should now have a functioning local WP env to work with. To login to the `wp-admin` backend, please username as `admin` & password as `password`.

__Awesome!__ - Thanks for being interested in contributing your time and code to this project!
