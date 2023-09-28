<?php
/**
 * Template Name: ArchivesPartenaire
 *
 */

get_header(); 
?>

<!-- #Content -->
<div id="Content">
	<div class="content_wrapper clearfix">

		<!-- .sections_group -->
		<div class="sections_group">

			<div class="section <?php echo $section_class; ?>">
				<div class="section_wrapper clearfix">
                
                	<div class="container">
                	<div class="column one-fourth">
                    	<?php dynamic_sidebar( 'Sidebar adherent' );?>
                    </div>
                	<div class="column three-fourth">
						<div class="blog_wrapper isotope_wrapper">

							<?php
							if (isset($_SESSION['adherent_connected']) && $_SESSION['adherent_connected'] && $_SESSION['adherent_infos']->IsPartenaire == 0)
							{
								?>
                                <div class="posts_group lm_wrapper">
                                    <?php
    
                                        // Loop attributes
                                        $attr = array(
                                            'featured_image' 	=> false,
                                            'filters' 				=> $filters,
                                        );
    
                                        if( $load_more ){
                                            $attr['featured_image'] = 'no_slider';	// no slider if load more
                                        }
                                        if( mfn_opts_get( 'blog-images' ) ){
                                            $attr['featured_image'] = 'image';	// images only option
                                        }
    
                                        echo mfn_content_post( false, false, $attr );
                                    ?>
                                </div>
    
                                <?php
                                    // pagination
                                    if( function_exists( 'mfn_pagination' ) ):
    
                                        echo mfn_pagination( false, $load_more );
    
                                    else:
                                        ?>
                                            <div class="nav-next"><?php next_posts_link(__('&larr; Older Entries', 'betheme')) ?></div>
                                            <div class="nav-previous"><?php previous_posts_link(__('Newer Entries &rarr;', 'betheme')) ?></div>
                                        <?php
                                    endif;
                                ?>
    
                            </div>
                            <?php
						}
						else
						{
							if (isset($_SESSION['adherent_connected']) && $_SESSION['adherent_connected'] && $_SESSION['adherent_infos']->IsPartenaire == 1)
							{
								?>
								<h1>Accès réservé aux adhérents.</h1>
								<?php
							}
							else
							{
								?>
								<h1>Accès réservé aux adhérents et aux partenaires.</h1>
								<?php
							}
						}
						?>
					</div>

				</div>
			</div>


		</div>

		<!-- .four-columns - sidebar -->
		<?php get_sidebar( 'blog' ); ?>

	</div>
</div>

<?php get_footer();

// Omit Closing PHP Tags