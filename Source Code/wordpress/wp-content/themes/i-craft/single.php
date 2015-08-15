<?php
/**
 * The template for displaying all single posts
 *
 * @package i-craft
 * @since i-craft 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
                <div class="meta-img">
                <?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
                    <div class="entry-thumbnail">
						<?php                        
                        $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                        echo '<a href="' . $large_image_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" alt="" class="tx-colorbox">';
                        the_post_thumbnail('icraft-single-thumb');
                        echo '</a>';
                        ?>

                    </div>
                <?php endif; ?>
                </div>
                
                <div class="post-mainpart">    
                    <header class="entry-header">
                        <div class="entry-meta">
                            <?php icraft_entry_meta(); ?>
                            <?php edit_post_link( __( 'Edit', 'i-craft' ), '<span class="edit-link">', '</span>' ); ?>
                        </div><!-- .entry-meta -->
                    </header><!-- .entry-header -->
                
                    <div class="entry-content">
                        <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'i-craft' ) ); ?>
                        <?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'i-craft' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
                    </div><!-- .entry-content -->

                	<?php if ( is_single() && get_the_author_meta( 'description' ) && is_multi_author() ) : ?>
                    	<footer class="entry-meta">
                            <?php get_template_part( 'author-bio' ); ?>
                    	</footer><!-- .entry-meta -->
					<?php endif; ?>
                </div>
            </article><!-- #post -->    
    

				<?php icraft_post_nav(); ?>
				<?php comments_template(); ?>

			<?php endwhile; ?>

		</div><!-- #content -->
		<?php get_sidebar(); ?>
	</div><!-- #primary -->


<?php get_footer(); ?>