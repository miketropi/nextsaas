<?php
/** @var array<string,mixed> $attributes */
declare( strict_types=1 ); if( !defined( 'ABSPATH' ) ){exit;}
$nextsaas_query=$GLOBALS['wp_query']??null;
$nextsaas_count=$nextsaas_query instanceof WP_Query?(int)$nextsaas_query->found_posts:0;
$nextsaas_count_text=sprintf( _n( '%s story', '%s stories', $nextsaas_count, 'nextsaas' ), number_format_i18n( $nextsaas_count ) );
$nextsaas_terms=get_terms( array( 'taxonomy'=>'category', 'hide_empty'=>true ) );
$nextsaas_terms=is_wp_error( $nextsaas_terms )?array():$nextsaas_terms;
$nextsaas_active=isset( $_GET['category_name'] )?sanitize_title( wp_unslash( $_GET['category_name'] ) ):'';
$nextsaas_current=home_url( wp_unslash( $_SERVER['REQUEST_URI']??'/' ) );
$nextsaas_all=remove_query_arg( array( 'category_name', 'paged' ), $nextsaas_current );
?>
<section <?php echo nextsaas_section_attrs( 'archive-journal', $attributes, array( 'section', 'ruled' ) ); ?>>
<div class="container">
<article class="featured-story"><div class="editorial-art " aria-hidden="true"><span class="meta"><span data-nextsaas-rich="artLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'artLabel' ) ); ?></span><span data-nextsaas-rich="artNumber"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'artNumber' ) ); ?></span></span><strong data-nextsaas-rich="artTitle"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'artTitle' ) ); ?></strong><span class="meta"><span data-nextsaas-rich="artMeta"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'artMeta' ) ); ?></span> <?php echo nextsaas_icon( 'arrow-up-right' ); ?></span></div><div><p class="eyebrow" data-nextsaas-rich="eyebrow"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'eyebrow' ) ); ?></p><h2 data-nextsaas-rich="heading"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'heading' ) ); ?></h2><p class="lead" data-nextsaas-rich="lead"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'lead' ) ); ?></p><div class="page-actions"><a class="text-link" href="<?php echo esc_url( nextsaas_resolve_url( nextsaas_attr( $attributes, 'ctaUrl' ) ) ); ?>"><span data-nextsaas-rich="ctaLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'ctaLabel' ) ); ?></span> <?php echo nextsaas_icon( 'arrow-up-right' ); ?></a></div></div></article>
<div class="filter-bar"><nav class="filter-options" id="archive-filters" aria-label="<?php echo esc_attr( nextsaas_attr( $attributes, 'filterLabel' ) ); ?>"><a href="<?php echo esc_url( $nextsaas_all ); ?>"<?php echo ''===$nextsaas_active?' aria-current="true"':''; ?>><span data-nextsaas-rich="allLabel"><?php echo nextsaas_rich( nextsaas_attr( $attributes, 'allLabel' ) ); ?></span></a><?php foreach( $nextsaas_terms as $nextsaas_term ): $nextsaas_url=add_query_arg( 'category_name', $nextsaas_term->slug, $nextsaas_all ); ?><a href="<?php echo esc_url( $nextsaas_url ); ?>"<?php echo $nextsaas_active===$nextsaas_term->slug?' aria-current="true"':''; ?>><?php echo esc_html( $nextsaas_term->name ); ?></a><?php endforeach; ?></nav><span class="meta" id="archive-count" role="status"><?php echo esc_html( $nextsaas_count_text ); ?></span></div>
</div></section>
