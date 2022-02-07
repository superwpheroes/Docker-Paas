<?php
/**
 * Blog-layout template.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) { exit( 'Direct script access denied.' ); }

global $wp_query;

// Set the correct post container layout classes.
$blog_layout = avada_get_blog_layout();
$post_class  = 'fusion-post-' . $blog_layout;

$container_class = 'fusion-posts-container ';
$wrapper_class = 'fusion-blog-layout-' . $blog_layout . '-wrapper ';
if ( 'grid' == $blog_layout ) {
	$container_class = 'fusion-blog-layout-' . $blog_layout . ' fusion-blog-layout-' . $blog_layout . '-' . Avada()->settings->get( 'blog_grid_columns' ) . ' isotope ';
} elseif ( 'timeline' !== $blog_layout ) {
	$container_class .= 'fusion-blog-layout-' . $blog_layout . ' ';
}

// Set class for scrolling type.
if ( Avada()->settings->get( 'blog_pagination_type' ) == 'infinite_scroll' ) {
	$container_class .= 'fusion-posts-container-infinite';
	$wrapper_class .= 'fusion-blog-infinite ';
} elseif ( Avada()->settings->get( 'blog_pagination_type' ) == 'load_more_button' ) {
	$container_class .= 'fusion-posts-container-infinite fusion-posts-container-load-more';
} else {
	$container_class .= 'fusion-blog-pagination ';
}

if ( ! Avada()->settings->get( 'featured_images' ) ) {
	$container_class .= 'fusion-blog-no-images ';
}

$number_of_pages = $wp_query->max_num_pages;
if ( is_search() && Avada()->settings->get( 'search_results_per_page' ) ) {
	$number_of_pages = ceil( $wp_query->found_posts / Avada()->settings->get( 'search_results_per_page' ) );
}
?>
<div id="posts-container" class="fusion-blog-archive <?php echo esc_attr( $wrapper_class ); ?>fusion-clearfix">
	<div class="<?php echo esc_attr( $container_class ); ?>" data-pages="<?php echo (int) $number_of_pages; ?>">
		<?php
		// Add the timeline icon.
		if ( 'timeline' == $blog_layout ) : ?>
			<div class="fusion-timeline-icon"><i class="fusion-icon-bubbles"></i></div>
			<div class="fusion-blog-layout-timeline fusion-clearfix">
		<?php endif; ?>
			<?php if ( 'timeline' == $blog_layout ) : ?>
				<?php
				// Initialize the time stamps for timeline month/year check.
				$post_count = 1;
				$prev_post_timestamp = null;
				$prev_post_month = null;
				$prev_post_year = null;
				$first_timeline_loop = false;
				?>

				<?php // Add the container that holds the actual timeline line. ?>
				<div class="fusion-timeline-line"></div>
			<?php endif; ?>

			<?php // Start the main loop. ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php
				// Set the time stamps for timeline month/year check.
				$alignment_class = '';
				if ( 'timeline' == $blog_layout ) {
					$post_timestamp = get_the_time( 'U' );
					$post_month     = date( 'n', $post_timestamp );
					$post_year      = get_the_date( 'Y' );
					$current_date   = get_the_date( 'Y-n' );

					// Set the correct column class for every post.
					if ( $post_count % 2 ) {
						$alignment_class = 'fusion-left-column';
					} else {
						$alignment_class = 'fusion-right-column';
					}

					// Set the timeline month label.
					if ( $prev_post_month != $post_month || $prev_post_year != $post_year ) {

						if ( $post_count > 1 ) {
							echo '</div>';
						}
						echo '<h3 class="fusion-timeline-date">' . get_the_date( Avada()->settings->get( 'timeline_date_format' ) ) . '</h3>';
						echo '<div class="fusion-collapse-month">';
					}
				}

				// Set the has-post-thumbnail if a video is used. This is needed if no featured image is present.
				$thumb_class = '';
				if ( get_post_meta( get_the_ID(), 'pyre_video', true ) ) {
					$thumb_class = ' has-post-thumbnail';
				}

				$post_classes = array(
					$post_class,
					$alignment_class,
					$thumb_class,
					'post',
					'fusion-clearfix',
				);
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( $post_classes ); ?>>
					<?php if ( 'grid' == $blog_layout ) : ?>
						<?php // Add an additional wrapper for grid layout border. ?>
						<div class="fusion-post-wrapper">
					<?php endif; ?>

					<?php if ( ( ( is_search() && Avada()->settings->get( 'search_featured_images' ) ) || ( ! is_search() && Avada()->settings->get( 'featured_images' ) ) ) && 'large-alternate' == $blog_layout ) : ?>
						<?php // Get featured images for all but large-alternate layout. ?>
						<?php get_template_part( 'new-slideshow' ); ?>
					<?php endif; ?>

					<?php if ( 'large-alternate' == $blog_layout || 'medium-alternate' == $blog_layout ) : ?>
						<?php // Get the post date and format box for alternate layouts. ?>
						<div class="fusion-date-and-formats">
							<?php
							/**
							 * The avada_blog_post_date_adn_format hook.
							 *
							 * @hooked avada_render_blog_post_date - 10 (outputs the HTML for the date box).
							 * @hooked avada_render_blog_post_format - 15 (outputs the HTML for the post format box).
							 */
							do_action( 'avada_blog_post_date_and_format' );
							?>
						</div>
					<?php endif; ?>

					<?php if ( ( ( is_search() && Avada()->settings->get( 'search_featured_images' ) ) || ( ! is_search() && Avada()->settings->get( 'featured_images' ) ) ) && 'large-alternate' != $blog_layout ) : ?>
						<?php // Get featured images for all but large-alternate layout. ?>
						<?php /*get_template_part( 'new-slideshow' ); */?>

						<?php
						$post_image_id = get_post_thumbnail_id(get_the_ID());
						if ($post_image_id) {
							$thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
							if ($thumbnail) (string)$thumbnail = $thumbnail[0];
						}
						echo '<div class="fusion-post-content-wrapper" style="background-image: url('.$thumbnail.'); background-repeat: no-repeat;
    						background-position: center; -webkit-background-size: cover; background-size: cover; ">';

    						echo '<div class="fusion-post-content post-content">';
								echo '<div class="post-category">';
										// Render the post category
										$categories = get_the_category();
										if ( ! empty( $categories ) ) {
										    echo esc_html( $categories[0]->name );
										}
								echo '</div>';
								// Render the post title
								echo avada_render_post_title( get_the_ID() );

								// Render the caption
								echo '<div class="post-caption">';
								$thumbnail_id    = get_post_thumbnail_id($post->ID);
		  						$thumbnail_image = get_posts(array('p' => $thumbnail_id, 'post_type' => 'attachment'));

							  if ($thumbnail_image && isset($thumbnail_image[0])) {
							    echo '<span>'.$thumbnail_image[0]->post_excerpt.'</span>';
							  }

							  echo '</div>';

						echo '</div>';


					?>







					<?php endif; ?>

					<?php if ( 'grid' == $blog_layout || 'timeline' == $blog_layout ) : ?>
						<?php // The post-content-wrapper is only needed for grid and timeline. ?>
						<div class="fusion-post-content-wrapper">
					<?php endif; ?>

					<?php if ( 'timeline' == $blog_layout ) : ?>
						<?php // Add the circles for timeline layout. ?>
						<div class="fusion-timeline-circle"></div>
						<div class="fusion-timeline-arrow"></div>
					<?php endif; ?>

					<div class="fusion-post-content post-content">
						<?php // Render the post title. ?>
						<?php //echo wp_kses_post( avada_render_post_title( get_the_ID() ) ); ?>

						<?php // Render post meta for grid and timeline layouts. ?>
						<?php if ( 'grid' == $blog_layout || 'timeline' == $blog_layout ) : ?>
							<?php echo wp_kses_post( avada_render_post_metadata( 'grid_timeline' ) ); ?>

							<?php if ( ( Avada()->settings->get( 'post_meta' ) && ( Avada()->settings->get( 'post_meta_author' ) || Avada()->settings->get( 'post_meta_date' ) || Avada()->settings->get( 'post_meta_cats' ) || Avada()->settings->get( 'post_meta_tags' ) || Avada()->settings->get( 'post_meta_comments' ) || Avada()->settings->get( 'post_meta_read' ) ) ) && 0 < Avada()->settings->get( 'excerpt_length_blog' ) ) : ?>
								<div class="fusion-content-sep"></div>
							<?php endif; ?>

						<?php elseif ( 'large-alternate' == $blog_layout || 'medium-alternate' == $blog_layout ) : ?>
							<?php // Render post meta for alternate layouts. ?>
							<?php echo wp_kses_post( avada_render_post_metadata( 'alternate' ) ); ?>
						<?php endif; ?>

						<div class="fusion-post-content-container">
							<?php
							/**
							 * The avada_blog_post_content hook.
							 *
							 * @hooked avada_render_blog_post_content - 10 (outputs the post content wrapped with a container).
							 */
							do_action( 'avada_blog_post_content' );
							?>
						</div>
					</div>

					<?php if ( 'medium' == $blog_layout || 'medium-alternate' == $blog_layout ) : ?>
						<div class="fusion-clearfix"></div>
					<?php endif; ?>

					<?php if ( ( Avada()->settings->get( 'post_meta' ) && ( Avada()->settings->get( 'post_meta_author' ) || Avada()->settings->get( 'post_meta_date' ) || Avada()->settings->get( 'post_meta_cats' ) || Avada()->settings->get( 'post_meta_tags' ) || Avada()->settings->get( 'post_meta_comments' ) || Avada()->settings->get( 'post_meta_read' ) ) ) ) : ?>
						<?php // Render post meta data according to layout. ?>
						<div class="fusion-meta-info">
							<?php if ( 'grid' == $blog_layout || 'timeline' == $blog_layout ) : ?>
								<?php // Render read more for grid/timeline layouts. ?>
								<div class="fusion-alignleft">
									<?php if ( Avada()->settings->get( 'post_meta_read' ) ) : ?>
										<?php $link_target = ( 'yes' === fusion_get_page_option( 'link_icon_target', get_the_ID() ) || 'yes' === fusion_get_page_option( 'post_links_target', get_the_ID() ) ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
										<a href="<?php echo esc_url_raw( get_permalink() ); ?>" class="fusion-read-more"<?php echo wp_kses_post( $link_target ); ?>>
											<?php echo esc_textarea( apply_filters( 'avada_blog_read_more_link', esc_attr__( 'Read More', 'Avada' ) ) ); ?>
										</a>
									<?php endif; ?>
								</div>

								<?php // Render comments for grid/timeline layouts. ?>
								<div class="fusion-alignright">
									<?php if ( Avada()->settings->get( 'post_meta_comments' ) ) : ?>
										<?php if ( ! post_password_required( get_the_ID() ) ) : ?>
											<?php comments_popup_link( '<i class="fusion-icon-bubbles"></i>&nbsp;0', '<i class="fusion-icon-bubbles"></i>&nbsp;1', '<i class="fusion-icon-bubbles"></i>&nbsp;%' ); ?>
										<?php else : ?>
											<i class="fusion-icon-bubbles"></i>&nbsp;<?php esc_attr_e( 'Protected', 'Avada' ); ?>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							<?php else : ?>
								<?php // Render all meta data for medium and large layouts. ?>
								<?php if ( 'large' == $blog_layout || 'medium' == $blog_layout ) : ?>
									<?php echo wp_kses_post( avada_render_post_metadata( 'standard' ) ); ?>
								<?php endif; ?>

								<?php // Render read more for medium/large and medium/large alternate layouts. ?>
								<div class="fusion-alignright">
									<?php if ( Avada()->settings->get( 'post_meta_read' ) ) : ?>
										<?php $link_target = ( 'yes' === fusion_get_page_option( 'link_icon_target', get_the_ID() ) || 'yes' === fusion_get_page_option( 'post_links_target', get_the_ID() ) ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
										<a href="<?php echo esc_url_raw( get_permalink() ); ?>" class="fusion-read-more"<?php echo wp_kses_post( $link_target ); ?>>
											<?php echo esc_textarea( apply_filters( 'avada_read_more_name', esc_attr__( 'Read More', 'Avada' ) ) ); ?>
										</a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( 'grid' == $blog_layout || 'timeline' == $blog_layout ) : ?>
						</div>
					<?php endif; ?>

					<?php if ( 'grid' == $blog_layout ) : ?>
						</div>
					<?php endif; ?>
				</article>

				<?php
				// Adjust the timestamp settings for next loop.
				if ( 'timeline' == $blog_layout ) {
					$prev_post_timestamp = $post_timestamp;
					$prev_post_month     = $post_month;
					$prev_post_year      = $post_year;
					$post_count++;
				}
				?>

			<?php endwhile; ?>

			<?php if ( 'timeline' == $blog_layout && 1 < $post_count ) : ?>
				</div>
			<?php endif; ?>

		</div>



		<?php // If infinite scroll with "load more" button is used. ?>
		<?php if ( Avada()->settings->get( 'blog_pagination_type' ) == 'load_more_button' ) : ?>
			<div class="fusion-load-more-button fusion-blog-button fusion-clearfix">
				<?php echo esc_textarea( apply_filters( 'avada_load_more_posts_name', esc_attr__( 'Load More Posts', 'Avada' ) ) ); ?>
			</div>
		<?php endif; ?>
		<?php if ( 'timeline' == $blog_layout ) : ?>
		</div>
		<?php endif; ?>
	<?php // Get the pagination. ?>
	<?php fusion_pagination( $pages = '', $range = 2 ); ?>
	</div>
<?php

wp_reset_postdata();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
