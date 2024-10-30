<?php

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

add_filter( 'molongui_contributors/single_content', function()
{
    return array
    (
        'thumbnail',
        'header',
        'content',
    );
});

require MOLONGUI_CONTRIBUTORS_DIR . 'templates/post/single-content-1.php';