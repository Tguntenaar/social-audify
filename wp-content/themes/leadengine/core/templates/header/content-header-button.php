<?php
  $redux_ThemeTek = get_option( 'redux_ThemeTek' );

  /* Button style and color scheme */
  $button_style_class = '';
  if (isset($redux_ThemeTek['tek-header-button-style'])) {
    if ($redux_ThemeTek['tek-header-button-style'] == 'solid-button') {
      $button_style_class .= 'tt_primary_button';
    } elseif ($redux_ThemeTek['tek-header-button-style'] == 'outline-button') {
      $button_style_class .= 'tt_secondary_button';
    } else {
      $button_style_class .= 'tt_primary_button';
    }
  }

  if (isset($redux_ThemeTek['tek-header-button-color'])) {
    if ($redux_ThemeTek['tek-header-button-color'] == 'primary-color') {
      $button_style_class .= ' btn_primary_color ';
    } elseif ($redux_ThemeTek['tek-header-button-color'] == 'secondary-color') {
      $button_style_class .= ' btn_secondary_color ';
    } else {
      $button_style_class .= ' btn_primary_color ';
    }
  }

  if (isset($redux_ThemeTek['tek-header-button-hover-style'])) {
      $button_style_class .= $redux_ThemeTek['tek-header-button-hover-style'];
  }

  if ($redux_ThemeTek['tek-header-button'] && ($redux_ThemeTek['tek-header-button-action'] == '1')) : ?>
   <a class="modal-menu-item menu-item tt_button <?php echo esc_html($button_style_class); ?>" data-toggle="modal" data-target="#popup-modal"><?php echo esc_html($redux_ThemeTek['tek-header-button-text']);?></a>
<?php elseif ($redux_ThemeTek['tek-header-button'] && ($redux_ThemeTek['tek-header-button-action'] == '2')) : ?>
  <?php if (isset($redux_ThemeTek['tek-scroll-id']) && $redux_ThemeTek['tek-scroll-id'] != '' ) : ?>
     <a class="modal-menu-item menu-item scroll-section tt_button <?php echo esc_html($button_style_class); ?>" href="<?php if( is_page('Home')) { echo esc_html($redux_ThemeTek['tek-scroll-id']); } else { echo esc_url(site_url()) . esc_html($redux_ThemeTek['tek-scroll-id']);} ?>"><?php echo esc_html($redux_ThemeTek['tek-header-button-text']);?></a>
  <?php endif; ?>
<?php elseif ($redux_ThemeTek['tek-header-button'] && ($redux_ThemeTek['tek-header-button-action'] == '3')) : ?>
<?php if (isset($redux_ThemeTek['tek-button-new-page']) && $redux_ThemeTek['tek-button-new-page'] != '' ) : ?>
 <a class="modal-menu-item menu-item tt_button <?php echo esc_html($button_style_class); ?>" <?php echo (isset($redux_ThemeTek['tek-button-target']) &&  $redux_ThemeTek['tek-button-target'] == 'new-page') ? 'target="_blank"' : 'target="_self"'; ?> href="<?php echo esc_url($redux_ThemeTek['tek-button-new-page']); ?>"><?php echo esc_html($redux_ThemeTek['tek-header-button-text']);?></a>
<?php endif; ?>
<?php endif; ?>
