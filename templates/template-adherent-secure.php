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
                        if (isset($_SESSION['adherent_connected']) && $_SESSION['adherent_connected'] && $_SESSION['adherent_infos']->IsPartenaire == 0)
                            while ( have_posts() ){
                                the_post();							// Post Loop
                                mfn_builder_print( get_the_ID() );	// Content Builder & WordPress Editor Content
                            }
                        else
                        {
                           if (isset($_SESSION['adherent_connected']) && $_SESSION['adherent_connected'] && $_SESSION['adherent_infos']->IsPartenaire == 1)
							{
								?>
								<h1>Accès réservé aux partenaires.</h1>
								<?php
							}
							else
							{
								?>
								<h1>Accès réservé aux adhérents et aux partenaires.</h1>
								<?php
							}
                        }?> 
                    
                        
                        <div class="section section-page-footer">
                            <div class="section_wrapper clearfix">
                            
                                <div class="column one page-pager">
                                    <?php
                                        // List of pages
                                        wp_link_pages(array(
                                            'before'			=> '<div class="pager-single">',
                                            'after'				=> '</div>',
                                            'link_before'		=> '<span>',
                                            'link_after'		=> '</span>',
                                            'next_or_number'	=> 'number'
                                        ));
                                    ?>
                                </div>
                                
                            </div>
                        </div>
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