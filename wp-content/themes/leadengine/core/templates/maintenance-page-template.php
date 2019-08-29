<?php
  $redux_ThemeTek = get_option( 'redux_ThemeTek' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ) ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php if ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) : ?>
      <link href="<?php echo esc_url($redux_ThemeTek['tek-favicon']['url']); ?>" rel="icon">
    <?php endif; ?>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class('maintenance-mode'); ?>>
    <div class="container section">
      <?php if ($redux_ThemeTek['tek-maintenance-title']) :?>
        <h2 class="maintenance-title"><?php echo esc_attr($redux_ThemeTek['tek-maintenance-title']); ?></h2>
      <?php endif; ?>

      <?php if ($redux_ThemeTek['tek-maintenance-content']) :?>
        <div class="maintenance-content"><?php echo wp_kses_post($redux_ThemeTek['tek-maintenance-content']); ?></div>
      <?php endif; ?>

      <?php if ($redux_ThemeTek['tek-maintenance-countdown']) :?>
        <?php if ($redux_ThemeTek['tek-maintenance-count-day'] && $redux_ThemeTek['tek-maintenance-count-month'] && $redux_ThemeTek['tek-maintenance-count-year']) : ?>
          <?php echo do_shortcode('[tek_countdown starting_year="'.esc_attr($redux_ThemeTek['tek-maintenance-count-year']).'" starting_month="'.esc_attr($redux_ThemeTek['tek-maintenance-count-month']).'" starting_day="'.esc_attr($redux_ThemeTek['tek-maintenance-count-day']).'" cd_text_days="Days" cd_text_hours="Hours" cd_text_minutes="Minutes" cd_text_seconds="Seconds"]'); ?>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ($redux_ThemeTek['tek-maintenance-subscribe']) : ?>
         <?php if ($redux_ThemeTek['tek-maintenance-form-select'] == '1' && $redux_ThemeTek['tek-maintenance-contactf7-formid'] != '') : ?>
           <div class="inline-cf">
             <?php echo do_shortcode('[contact-form-7 id="'. esc_attr($redux_ThemeTek['tek-maintenance-contactf7-formid']).'"]'); ?>
           </div>
         <?php elseif ($redux_ThemeTek['tek-maintenance-form-select'] == '2' && $redux_ThemeTek['tek-maintenance-ninja-formid'] != '') : ?>
            <?php echo do_shortcode('[ninja_form id="'. esc_attr($redux_ThemeTek['tek-maintenance-ninja-formid']).'"]'); ?>
         <?php elseif ($redux_ThemeTek['tek-maintenance-form-select'] == '3' && $redux_ThemeTek['tek-maintenance-gravity-formid'] != '') : ?>
            <?php echo do_shortcode('[gravityform id="'. esc_attr($redux_ThemeTek['tek-maintenance-gravity-formid']).'"]'); ?>
         <?php elseif ($redux_ThemeTek['tek-maintenance-form-select'] == '4' && $redux_ThemeTek['tek-maintenance-wp-formid'] != '') : ?>
            <?php echo do_shortcode('[wpforms id="'. esc_attr($redux_ThemeTek['tek-maintenance-wp-formid']).'"]'); ?>
        <?php endif; ?>
      <?php endif; ?>
      <?php wp_footer(); ?>
    </div>
  </body>
</html>
