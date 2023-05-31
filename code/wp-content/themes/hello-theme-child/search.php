<?php
/**
 * The template for displaying search results.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

$stack = []; // Search data

?>

<div class="page-header search-header-main">
	<div class="search-header-in">
		<h1 class="entry-title">
			<?php esc_html_e( 'Search results: ', 'hello-elementor' ); ?>
		</h1>
		<div class="search-sub-title">“<?php echo get_search_query(); ?>”</div>
		<div class="search-total"></div>
	</div>
</div>

<main id="content" class="site-main" role="main">

	<div class="page-content search-wrap">

		<?php if( trim($_GET['s']) == "" ) :  ?>

			<p class="no-results"><?php esc_html_e( 'No such a criteria, It seems we can\'t find what you\'re looking for.', 'hello-elementor' ); ?></p>

		<?php else: ?>

			<?php if ( have_posts() ) : ?>
				<?php
				while ( have_posts() ) : the_post();
					$categories = get_the_category();
					if ( ! empty( $categories ) ) {
						$type = esc_html( $categories[0]->name );	
					}else { $type = ''; }
					$stack[] = array(
						'title' => esc_html( get_the_title() ),
						'url'   => esc_url( get_permalink() ),
						'desc'  => wp_strip_all_tags( get_the_excerpt() ),
						'type'  => esc_html( $type ),
					);
				endwhile;

				wp_localize_script( 'disqo-search-ajax', 'disqo_search', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'searchstack'=> $stack,
					's' => $_GET['s'],
				));
				?>
			<?php endif; ?>

			<div id="search-page" 
				data-current-page="<?php echo $_GET['page'] < 1 ? 1 : (int) $_GET['page']; ?>">
				<div class="loader-over"><div class="loader"></div></div>
				<div class="search-results"></div>
			</div>

		<?php endif; ?>

	</div>

	<?php if( is_search() ) : ?>

	<script type="text/javascript">
	jQuery(document).ready(function($){
		jQuery(".search-form, .search-form-enable").on('click',function(){
			jQuery('body').removeClass("screen-overflow");
		});
		jQuery(".search-form-input").slideToggle();
		jQuery(".search-form-enable").toggleClass("hide");
		jQuery(".search-form").toggleClass("hide");
		$('input[name="s"]').val('<?php echo $_GET["s"] ?>');
	});

	</script>

	<?php endif; ?>
</main>

<?php get_footer(); ?>