<?php
/**
 * Title: NextSaaS — Search
 * Slug: nextsaas/page-search
 * Categories: nextsaas-pages
 * Description: The search surface with its live results loop. Hidden from the inserter; use the template.
 * Keywords: search
 * Inserter: no
 * Viewport Width: 1440
 */

?>
<!-- wp:nextsaas/page-hero {"eyebrow":"NEXTSAAS / SEARCH","heading":"Find your<br><em>next thought.</em>","lead":"Search the workspace concept, plans, common questions, and ideas from the field guide.","breadcrumbCurrentLabel":"Search"} /-->

<!-- wp:nextsaas/search-site-search /-->

<!-- wp:query {"queryId":2,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"className":"nextsaas-search-loop","layout":{"type":"default"}} -->
<div class="wp-block-query nextsaas-search-loop">
<!-- wp:group {"className":"search-results","layout":{"type":"default"}} -->
<div class="wp-block-group search-results">
<!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"className":"log-row","layout":{"type":"default"}} -->
<div class="wp-block-group log-row">
<!-- wp:post-terms {"term":"category","className":"meta"} /-->
<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group">
<!-- wp:post-title {"level":3,"isLink":true} /-->
<!-- wp:post-excerpt {"moreText":"","excerptLength":24} /-->
</div>
<!-- /wp:group -->
<!-- wp:read-more {"content":"Read","className":"round-link"} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->
</div>
<!-- /wp:group -->

<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->
<!-- wp:query-pagination-numbers /-->
<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:group {"className":"search-no-results","layout":{"type":"default"}} -->
<div class="wp-block-group search-no-results">
<!-- wp:query-title {"type":"search"} /-->
<!-- wp:paragraph -->
<p>Nothing matched your search. Try a shorter phrase, or one of the starting points above.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
