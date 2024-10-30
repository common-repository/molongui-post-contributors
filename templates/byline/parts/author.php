<?php

use Molongui\Contributors\Template;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

?>
<div class="molongui-post-byline__column molongui-post-author">
    <?php Template::the_byline( $post_id, $config['by'] ); ?>
</div>