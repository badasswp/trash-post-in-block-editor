#!/bin/bash

wp-env run cli wp theme activate twentytwentythree
wp-env run cli wp rewrite structure /%postname%
wp-env run cli wp option update blogname "Trash Post in Block Editor"
wp-env run cli wp option update blogdescription "Delete a Post from within the WP Block Editor."
