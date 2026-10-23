<?php
/**
 * Title: NextSaaS — Post detail
 * Slug: nextsaas/page-post
 * Categories: nextsaas-pages
 * Description: A single story with its live content, related reading and comments. Hidden from the inserter; use the template.
 * Keywords: post
 * Inserter: no
 * Viewport Width: 1440
 */

?>
<!-- wp:nextsaas/post-article-hero /-->
<!-- wp:nextsaas/post-article-content /-->
<!-- wp:nextsaas/post-related-reading /-->

<!-- wp:group {"className":"article-comments","layout":{"type":"default"}} -->
<div class="wp-block-group article-comments">
<!-- wp:comments -->
<div class="wp-block-comments">
<!-- wp:comments-title {"showPostTitle":false} /-->

<!-- wp:comment-template -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"40px"} -->
<div class="wp-block-column" style="flex-basis:40px"><!-- wp:avatar {"size":40} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:comment-author-name /-->
<!-- wp:comment-date /-->
<!-- wp:comment-content /-->
<!-- wp:comment-reply-link /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:comment-template -->

<!-- wp:comments-pagination -->
<!-- wp:comments-pagination-previous /-->
<!-- wp:comments-pagination-numbers /-->
<!-- wp:comments-pagination-next /-->
<!-- /wp:comments-pagination -->

<!-- wp:post-comments-form /-->
</div>
<!-- /wp:comments -->
</div>
<!-- /wp:group -->
