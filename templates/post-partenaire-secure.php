<?php
/*
  Template Name: Pages adherent
 */
 
get_header();

?>
	
<!-- #Content -->
<div id="Content">
	<div class="content_wrapper clearfix">

		<!-- .sections_group -->
		<div class="sections_group">
		
			<div class="entry-content" itemprop="mainContentOfPage">
			
            	<div class="container">
                	<div class="column one-fourth">
                    	<?php dynamic_sidebar( 'Sidebar adherent' );?>
                    </div>
                	<div class="column three-fourth">
						<?php 
                        if (isset($_SESSION['adherent_connected']) && $_SESSION['adherent_connected'] && $_SESSION['adherent_infos']->IsPartenaire == 1)
						{
                         	if( get_post_meta( get_the_ID(), 'mfn-post-template', true ) == 'builder' ){
								
								// Template | Builder -----------------------------------------------
			
								// prev & next post navigation
								mfn_post_navigation_sort();
								
								$single_post_nav = array(
									'hide-sticky'	=> false,
									'in-same-term'	=> false,
								);
								
								$opts_single_post_nav = mfn_opts_get( 'prev-next-nav' );
								if( isset( $opts_single_post_nav['hide-sticky'] ) ){
									$single_post_nav['hide-sticky'] = true;
								}
								
								// single post navigation | sticky
								if( ! $single_post_nav['hide-sticky'] ){
									if( isset( $opts_single_post_nav['in-same-term'] ) ){
										$single_post_nav['in-same-term'] = true;
									}
								
									$post_prev = get_adjacent_post( $single_post_nav['in-same-term'], '', true, 'portfolio-types' );
									$post_next = get_adjacent_post( $single_post_nav['in-same-term'], '', false, 'portfolio-types' );
								
									echo mfn_post_navigation_sticky( $post_prev, 'prev', 'icon-left-open-big' );
									echo mfn_post_navigation_sticky( $post_next, 'next', 'icon-right-open-big' );
								}
								
			
								while( have_posts() ){
									the_post();							// Post Loop
									mfn_builder_print( get_the_ID() );	// Content Builder & WordPress Editor Content
								}
								
							} else {
								
								// Template | Default -----------------------------------------------
								
								while( have_posts() ){
									the_post();
									get_template_part( 'includes/content', 'single' );
								}
								
								if( mfn_opts_get('portfolio-comments') ){
									echo '<div class="section section-page-comments">';
										echo '<div class="section_wrapper clearfix">';
									
											echo '<div class="column one comments">';
												comments_template( '', true );
											echo '</div>';
											
										echo '</div>';
									echo '</div>';
								}
								
							}
							
						}
                        else
                        {
                            ?>
                            <h1>Accès réservé aux partenaires.</h1>
                            <?php
                        }?> 
                    </div>
				</div>
			</div>
			
			<?php if( mfn_opts_get('page-comments') ): ?>
				<div class="section section-page-comments">
					<div class="section_wrapper clearfix">
					
						<div class="column one comments">
							<?php comments_template( '', true ); ?>
						</div>
						
					</div>
				</div>
			<?php endif; ?>
	
		</div>
		
		<!-- .four-columns - sidebar -->
        <?php get_sidebar(); ?>

	</div>
</div>

<?php get_footer();

// Omit Closing PHP Tags