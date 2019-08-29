<?php
/**
*ReduxFramework Sample Config File
*For full documentation, please visit: https://docs.reduxframework.com
**/
if (!class_exists('keydesign_Redux_Framework_config')) {
    class keydesign_Redux_Framework_config
    {
        public $args = array();
        public $sections = array();
        public $theme;
        public $ReduxFramework;
        public function __construct()
        {
            if (!class_exists('ReduxFramework')) {
                return;
            }
            // This is needed. Bah WordPress bugs.  ;)
            if (true == Redux_Helpers::isTheme(__FILE__)) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array(
                    $this,
                    'initSettings'
                ), 10);
            }
        }
        public function initSettings()
        {
            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();
            // Set the default arguments
            $this->setArguments();
            // Set a few help tabs so you can see how it's done
            $this->setSections();
            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }
            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }
        /**
        * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
        * Simply include this function in the child themes functions.php file.

        * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
        * so you must use get_template_directory_uri() if you want to use any of the built in icons
        **/
        function dynamic_section($sections)
        {
            //$sections = array();
            $sections[] = array(
                'title' => esc_html__('Section via hook', 'leadengine'),
                'desc' => esc_html__('This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.', 'leadengine'),
                'icon' => 'el-icon-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );
            return $sections;
        }
        /**
        *Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
        **/
        function change_arguments($args)
        {
            //$args['dev_mode'] = true;
            return $args;
        }
        /**
        * Filter hook for filtering the default value of any given field. Very useful in development mode.
        **/
        function change_defaults($defaults)
        {
            $defaults['str_replace'] = 'Testing filter hook!';
            return $defaults;
        }
        public function setSections()
        {
            /**
            *Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
            **/
            // Background Patterns Reader
            $sample_patterns_path = ReduxFramework::$_dir . '../sample/patterns/';
            $sample_patterns_url  = ReduxFramework::$_url . '../sample/patterns/';
            $sample_patterns      = array();
            ob_start();
            $ct              = wp_get_theme();
            $this->theme     = $ct;
            $item_name       = $this->theme->get('Name');
            $tags            = $this->theme->Tags;
            $screenshot      = $this->theme->get_screenshot();
            $class           = $screenshot ? 'has-screenshot' : '';
            $customize_title = sprintf(esc_html__('Customize &#8220;%s&#8221;', 'leadengine'), $this->theme->display('Name'));
?>
    <div id="current-theme" class="<?php
            echo esc_attr($class);
?>
        ">
        <?php
            if ($screenshot):
?>
        <?php
                if (current_user_can('edit_theme_options')):
?>
        <a href="<?php
                    echo esc_url(wp_customize_url());
?>
            " class="load-customize hide-if-no-customize" title="
            <?php
                    echo esc_attr($customize_title);
?>
            ">
            <img src="<?php
                    echo esc_url($screenshot);
?>
            " alt="
            <?php
                    esc_attr_e('Current theme preview','leadengine');
?>" /></a>
        <?php
                endif;
?>
        <img class="hide-if-customize" src="<?php
                echo esc_url($screenshot);
?>
        " alt="
        <?php
                esc_attr_e('Current theme preview','leadengine');
?>
        " />
        <?php
            endif;
?>

        <h4>
            <?php
            echo esc_attr($this->theme->display('Name'));
?></h4>

        <div>
            <ul class="theme-info">
                <li>
                    <?php
            printf(esc_html__('By %s', 'leadengine'), $this->theme->display('Author'));
?></li>
                <li>
                    <?php
            printf(esc_html__('Version %s', 'leadengine'), $this->theme->display('Version'));
?></li>
                <li>
                    <?php
            echo '<strong>' . esc_html__('Tags', 'leadengine') . ':</strong>
                ';
?>
                <?php
            printf($this->theme->display('Tags'));
?></li>
        </ul>
        <p class="theme-description">
            <?php
            echo esc_attr($this->theme->display('Description'));
?></p>

    </div>
</div>

<?php
            $item_info = ob_get_contents();
            ob_end_clean();
            $sampleHTML = '';
            // ACTUAL DECLARATION OF SECTIONS

            $this->sections[] = array(
                'icon' => 'el-icon-bookmark',
                'title' => esc_html__('Business Info', 'leadengine'),
                'fields' => array(
                    array(
                        'id' => 'tek-business-phone',
                        'type' => 'text',
                        'title' => esc_html__('Business phone', 'leadengine'),
                        'default' => '(222) 400-630'
                    ),
                    array(
                        'id' => 'tek-business-email',
                        'type' => 'text',
                        'title' => esc_html__('Business email', 'leadengine'),
                        'default' => 'contact@leadengine-theme.com'
                    ),
                    array(
                        'id'            => 'tek-social-profiles',
                        'type'          => 'social_profiles',
                        'title'         => 'Social Icons',
                        'subtitle'      => 'Click an icon to activate it, drag and drop to change the icon order.',
                    ),
                )
            );

            $this->sections[] = array(
                'icon' => 'el-icon-globe',
                'title' => esc_html__('Global Options', 'leadengine'),
                'fields' => array(
                    array(
                        'id' => 'tek-preloader',
                        'type' => 'switch',
                        'title' => esc_html__('Enable preloader', 'leadengine'),
                        'subtitle' => esc_html__('If enabled, a loader screen will appear before loading the page.', 'leadengine'),
                        'default' => true
                    ),
                    array(
                        'id' => 'tek-backtotop',
                        'type' => 'switch',
                        'title' => esc_html__('Enable go to top button', 'leadengine'),
                        'subtitle' => esc_html__('If enabled, a go to top button will appear in the right bottom corner the page.', 'leadengine'),
                        'default' => true
                    ),
                    array(
                        'id' => 'tek-google-api',
                        'type' => 'text',
                        'title' => __('Google Map API Key', 'leadengine'),
                        'default' => '',
                        'subtitle' => esc_html__('Generate, copy and paste here Google Maps API Key', 'leadengine'),
                    ),
                    array(
                        'id' => 'tek-disable-animations',
                        'type' => 'switch',
                        'title' => esc_html__('Disable animations on mobile', 'leadengine'),
                        'subtitle' => esc_html__('Globally turn on/off element animations on mobile', 'leadengine'),
                        'default' => false
                    ),
                )
            );

            $this->sections[] = array(
                'title' => esc_html__('Color Schemes', 'leadengine'),
                'subsection' => true,
                'fields' => array(
                  array(
                    'id' => 'tek-main-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => esc_html__('Primary accent color', 'leadengine'),
                    'default' => '#4f6df5',
                    'validate' => 'color'
                  ),

                  array(
                    'id' => 'tek-secondary-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => esc_html__('Secondary accent color', 'leadengine'),
                    'default' => '',
                    'validate' => 'color'
                  ),

                  array(
                    'id' => 'tek-titlebar-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => esc_html__('Title bar background color', 'leadengine'),
                    'default' => '',
                    'subtitle' => esc_html__('Use this colorpicker to override the title bar default background color.', 'leadengine'),
                    'validate' => 'color'
                  ),

                  array(
                      'id' => 'tek-titlebar-text-color',
                      'type' => 'color',
                      'transparent' => false,
                      'title' => esc_html__('Title bar text color', 'leadengine'),
                      'default' => '',
                      'subtitle' => esc_html__('Use this colorpicker to override the title bar default text color.', 'leadengine'),
                      'validate' => 'color'
                  ),

                  array(
                      'id' => 'tek-global-border-color',
                      'type' => 'color',
                      'title' => esc_html__('Border color', 'leadengine'),
                      'default' => '',
                      'subtitle' => esc_html__('Use this colorpicker to override the default theme border color. ', 'leadengine'),
                      'validate' => 'color'
                  ),
              )
          );

          $this->sections[] = array(
              'icon' => 'el-icon-star',
              'title' => esc_html__('Logo', 'leadengine'),
              'fields' => array(
                array(
                    'id' => 'tek-logo-style',
                    'type' => 'select',
                    'title' => esc_html__('Logo style', 'leadengine'),
                    'options'  => array(
                        '1' => 'Image logo',
                        '2' => 'Text logo'
                    ),
                    'default' => '2'
                ),
                array(
                    'id' => 'tek-logo',
                    'type' => 'media',
                    'readonly' => false,
                    'url' => true,
                    'title' => esc_html__('Primary image logo', 'leadengine'),
                    'subtitle' => esc_html__('Upload primary logo image. Recommended image size: 120x60px', 'leadengine'),
                    'required' => array('tek-logo-style','equals','1'),
                ),
                array(
                    'id' => 'tek-logo2',
                    'type' => 'media',
                    'readonly' => false,
                    'url' => true,
                    'title' => esc_html__('Secondary image logo', 'leadengine'),
                    'subtitle' => esc_html__('Upload secondary image logo. Recommended image size: 120x60px', 'leadengine'),
                    'required' => array('tek-logo-style','equals','1'),
                ),
                array(
                    'id' => 'tek-logo-size',
                    'type' => 'dimensions',
                    'height' => false,
                    'units'    => array('px'),
                    'url' => true,
                    'title' => esc_html__('Image Logo Size', 'leadengine'),
                    'subtitle' => esc_html__('Choose logo width - the image will constrain proportions', 'leadengine'),
                    'required' => array('tek-logo-style','equals','1'),
                ),
                array(
                    'id' => 'tek-text-logo',
                    'type' => 'text',
                    'title' => esc_html__('Text logo', 'leadengine'),
                    'required' => array('tek-logo-style','equals','2'),
                    'default' => 'LeadEngine'
                ),
                array(
                    'id' => 'tek-text-logo-typo',
                    'type' => 'typography',
                    'title' => esc_html__('Text logo font settings', 'leadengine'),
                    'required' => array('tek-logo-style','equals', '2'),
                    'google' => true,
                    'font-family' => true,
                    'font-style' => true,
                    'font-size' => true,
                    'line-height' => false,
                    'color' => false,
                    'text-align' => false,
                    'all_styles' => false,
                    'units' => 'px',
                ),
                array(
                    'id' => 'tek-main-logo-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => esc_html__('Primary logo text color', 'leadengine'),
                    'required' => array('tek-logo-style','equals','2'),
                    'default' => '',
                    'validate' => 'color'
                ),
                array(
                    'id' => 'tek-secondary-logo-color',
                    'type' => 'color',
                    'transparent' => false,
                    'title' => esc_html__('Secondary logo text color', 'leadengine'),
                    'subtitle' => esc_html__('Logo text color for sticky navigation', 'leadengine'),
                    'required' => array('tek-logo-style','equals','2'),
                    'default' => '',
                    'validate' => 'color'
                ),
                array(
                    'id' => 'tek-favicon',
                    'type' => 'media',
                    'readonly' => false,
                    'preview' => false,
                    'url' => true,
                    'title' => esc_html__('Favicon', 'leadengine'),
                    'subtitle' => esc_html__('Upload favicon image', 'leadengine'),
                    'default' => array(
                        'url' => get_template_directory_uri() . '/core/assets/images/favicon.png'
                    )
                ),
              )
          );

            $this->sections[] = array(
                'icon' => 'el-icon-lines',
                'title' => esc_html__('Header', 'leadengine'),
                'fields' => array(
                    array(
                        'id' => 'tek-menu-style',
                        'type' => 'button_set',
                        'title' => esc_html__('Header bar width', 'leadengine'),
                        'subtitle' => esc_html__('You can choose between full width and contained.', 'leadengine'),
                        'options' => array(
                            '1' => 'Contained',
                            '2' => 'Full-Width'
                         ),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'tek-menu-behaviour',
                        'type' => 'button_set',
                        'title' => esc_html__('Header bar behaviour', 'leadengine'),
                        'subtitle' => esc_html__('You can choose between a sticky or a fixed top menu.', 'leadengine'),
                        'options' => array(
                            '1' => 'Sticky',
                            '2' => 'Fixed'
                         ),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'tek-menu-typo',
                        'type' => 'typography',
                        'title' => esc_html__('Menu font settings', 'leadengine'),
                        'google' => true,
                        'font-style' => true,
                        'font-size' => true,
                        'line-height' => false,
                        'text-transform' => true,
                        'color' => false,
                        'text-align' => false,
                        'all_styles' => false,
                        'default' => array(
                            'font-weight' => '',
                            'font-family' => '',
                            'font-size' => '',
                            'text-transform' => '',
                        ),
                        'units' => 'px',
                    ),
                    array(
                        'id' => 'tek-header-menu-bg',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Navigation background color', 'leadengine'),
                        'default' => '',
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-header-menu-color',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Navigation text color', 'leadengine'),
                        'default' => '',
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-header-menu-color-hover',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Navigation text color on mouse over', 'leadengine'),
                        'default' => '',
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-header-menu-bg-sticky',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Sticky navigation background color', 'leadengine'),
                        'default' => '',
                        'required' => array('tek-menu-behaviour','equals', '1'),
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-header-menu-color-sticky',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Sticky navigation text color', 'leadengine'),
                        'default' => '',
                        'required' => array('tek-menu-behaviour','equals', '1'),
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-header-menu-color-sticky-hover',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Sticky navigation text color on mouse over', 'leadengine'),
                        'default' => '',
                        'required' => array('tek-menu-behaviour','equals', '1'),
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-sticky-nav-logo',
                        'type' => 'select',
                        'title' => esc_html__('Sticky navigation logo image', 'leadengine'),
                        'subtitle' => esc_html__('Select which logo image to be used with the sticky navigation bar.', 'leadengine'),
                        'options'  => array(
                            'nav-primary-logo' => 'Primary logo image',
                            'nav-secondary-logo' => 'Secondary logo image',
                        ),
                        'default' => 'nav-primary-logo',
                        'required' => array('tek-logo-style','equals','1'),
                    ),
                    array(
                       'id' => 'tek-transparent-homepage-menu',
                       'type' => 'switch',
                       'title' => esc_html__('Homepage transparent navigation', 'leadengine'),
                       'subtitle' => esc_html__('Enable/disable Homepage transparent navigation', 'leadengine'),
                       'default' => false
                   ),
                   array(
                       'id' => 'tek-transparent-nav-logo',
                       'type' => 'select',
                       'title' => esc_html__('Transparent navigation logo image', 'leadengine'),
                       'subtitle' => esc_html__('Select which logo image to be used with the homepage transparent navigation bar.', 'leadengine'),
                       'options'  => array(
                           'nav-primary-logo' => 'Primary logo image',
                           'nav-secondary-logo' => 'Secondary logo image',
                       ),
                       'default' => 'nav-secondary-logo',
                       'required' => array('tek-logo-style','equals','1'),
                   ),
                   array(
                       'id' => 'tek-transparent-homepage-menu-colors',
                       'type' => 'color',
                       'transparent' => false,
                       'title' => esc_html__('Navigation text color', 'leadengine'),
                       'subtitle' => esc_html__('Homepage navigation color when transparent background', 'leadengine'),
                       'default' => '',
                       'validate' => 'color',
                       'required' => array('tek-transparent-homepage-menu','equals', true),
                   ),
                  )
              );
              $this->sections[] = array(
                  'title' => esc_html__('Topbar', 'leadengine'),
                  'subsection' => true,
                  'fields' => array(
                    array(
                        'id' => 'tek-topbar',
                        'type' => 'switch',
                        'title' => esc_html__('Enable Topbar', 'leadengine'),
                        'subtitle' => esc_html__('Activate to display topbar.', 'leadengine'),
                        'default' => true
                    ),
		                array(
                        'id' => 'tek-topbar-sticky',
                        'type' => 'switch',
                        'title' => esc_html__('Sticky topbar', 'leadengine'),
                        'required' => array('tek-topbar','equals', true),
                        'subtitle' => esc_html__('Display topbar with sticky navigation.', 'leadengine'),
                        'default' => false
                    ),
                    array(
                        'id' => 'tek-topbar-template',
                        'type' => 'select',
                        'title' => esc_html__('Topbar template', 'leadengine'),
                        'required' => array('tek-topbar','equals', true),
                        'options'  => array(
                            '1' => 'Business info (left) + Social icons (right)',
                            '2' => 'Social icons (left) + Business info (right)',
                            '3' => 'Business info (left) + Topbar menu (right)',
                            '4' => 'Social icons (left) + Topbar menu (right)',
                        ),
                        'default' => '3'
                    ),
                    array(
                        'id' => 'tek-topbar-search',
                        'type' => 'switch',
                        'title' => esc_html__('Display search bar', 'leadengine'),
                        'required' => array(
                          array ('tek-topbar','equals', true),
                          array ('tek-topbar-template','greater', '2'),
                        ),
                        'default' => true
                    ),
                    array(
                        'id' => 'tek-topbar-typo',
                        'type' => 'typography',
                        'title' => esc_html__('Topbar font settings', 'leadengine'),
                        'required' => array('tek-topbar','equals', true),
                        'google' => false,
                        'font-family' => false,
                        'font-style' => true,
                        'font-size' => true,
                        'line-height' => false,
                        'color' => false,
                        'text-align' => false,
                        'all_styles' => false,
                        'units' => 'px',
                    ),
                    array(
                        'id' => 'tek-topbar-bg-color',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Topbar background color', 'leadengine'),
                        'default' => '',
                        'validate' => 'color',
                        'required' => array('tek-topbar','equals', true),
                    ),
                    array(
                        'id' => 'tek-topbar-text-color',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Topbar text color', 'leadengine'),
                        'default' => '',
                        'validate' => 'color',
                        'required' => array('tek-topbar','equals', true),
                    ),
                    array(
                        'id' => 'tek-topbar-hover-text-color',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Topbar text color on mouse over', 'leadengine'),
                        'default' => '',
                        'validate' => 'color',
                        'required' => array('tek-topbar','equals', true),
                    ),
                )
            );

            $this->sections[] = array(
                'title' => esc_html__('Header Button', 'leadengine'),
                'subsection' => true,
                'fields' => array(
                    array(
                        'id' => 'tek-header-button',
                        'type' => 'switch',
                        'title' => esc_html__('Show button in header', 'leadengine'),
                        'default' => false
                    ),
                    array(
                        'id' => 'tek-header-button-text',
                        'type' => 'text',
                        'title' => esc_html__('Button text', 'leadengine'),
                        'required' => array('tek-header-button','equals', true),
                        'default' => 'Get a quote'
                    ),
                    array(
                        'id' => 'tek-header-button-style',
                        'type' => 'select',
                        'title' => esc_html__('Button style', 'leadengine'),
                        'required' => array('tek-header-button','equals', true),
                        'options'  => array(
                            'solid-button' => 'Solid',
                            'outline-button' => 'Outline',
                        ),
                        'default' => 'outline-button'
                    ),
                    array(
                        'id' => 'tek-header-button-color',
                        'type' => 'select',
                        'title' => esc_html__('Button color scheme', 'leadengine'),
                        'required' => array('tek-header-button','equals', true),
                        'options'  => array(
                            'primary-color' => 'Primary color',
                            'secondary-color' => 'Secondary color',
                        ),
                        'default' => 'primary-color'
                    ),
                    array(
                        'id' => 'tek-header-button-hover-style',
                        'type' => 'select',
                        'title' => esc_html__('Button hover state', 'leadengine'),
                        'required' => array('tek-header-button','equals', true),
                        'options'  => array(
                            'default_header_btn' => 'Default',
                            'hover_solid_primary' => 'Solid - Primary color',
                            'hover_solid_secondary' => 'Solid - Secondary color',
                            'hover_outline_primary' => 'Outline - Primary color',
                            'hover_outline_secondary' => 'Outline - Secondary color',
                        ),
                        'default' => 'default_header_btn'
                    ),
                    array(
                        'id' => 'tek-header-button-action',
                        'type' => 'select',
                        'title' => esc_html__('Button action', 'leadengine'),
                        'required' => array('tek-header-button','equals', true),
                        'options'  => array(
                            '1' => 'Open modal window with contact form',
                            '2' => 'Scroll to section',
                            '3' => 'Open a new page'
                        ),
                        'default' => '3'
                    ),
                    array(
                        'id' => 'tek-modal-title',
                        'type' => 'text',
                        'title' => esc_html__('Modal title', 'leadengine'),
                        'required' => array('tek-header-button-action','equals','1'),
                        'default' => 'Lets get in touch'
                    ),
                    array(
                        'id' => 'tek-modal-subtitle',
                        'type' => 'editor',
                        'title' => esc_html__('Modal subtitle', 'leadengine'),
                        'required' => array('tek-header-button-action','equals','1'),
                        'default' => '',
		                    'args'   => array(
                          'teeny'  => true,
                          'textarea_rows'    => 10,
                          'media_buttons'	   => false,
			                  ),
                    ),
                    array(
                        'id' => 'tek-modal-bg-image',
                        'type' => 'media',
                        'readonly' => false,
                        'url' => true,
                        'title' => esc_html__('Modal background image', 'leadengine'),
                        'subtitle' => esc_html__('Upload modal background image.', 'leadengine'),
                        'required' => array('tek-header-button-action','equals','1'),
                        'default' => '',
                    ),
                    array(
                        'id' => 'tek-modal-form-select',
                        'type' => 'select',
                        'title' => esc_html__('Contact form plugin', 'leadengine'),
                        'required' => array('tek-header-button-action','equals','1'),
                        'options'  => array(
                            '1' => 'Contact Form 7',
                            '2' => 'Ninja Forms',
                            '3' => 'Gravity Forms',
                            '4' => 'WP Forms',
                        ),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'tek-modal-contactf7-formid',
                        'type' => 'select',
                        'data' => 'posts',
                        'args' => array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1, ),
                        'title' => esc_html__('Contact Form 7 Title', 'leadengine'),
                        'required' => array('tek-modal-form-select','equals','1'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-modal-ninja-formid',
                        'type' => 'text',
                        'title' => esc_html__('Ninja Form ID', 'leadengine'),
                        'required' => array('tek-modal-form-select','equals','2'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-modal-gravity-formid',
                        'type' => 'text',
                        'title' => esc_html__('Gravity Form ID', 'leadengine'),
                        'required' => array('tek-modal-form-select','equals','3'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-modal-wp-formid',
                        'type' => 'text',
                        'title' => esc_html__('WP Form ID', 'leadengine'),
                        'required' => array('tek-modal-form-select','equals','4'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-scroll-id',
                        'type' => 'text',
                        'title' => esc_html__('Scroll to section ID', 'leadengine'),
                        'required' => array('tek-header-button-action','equals','2'),
                        'default' => '#download-leadengine'
                    ),
                    array(
                        'id' => 'tek-button-new-page',
                        'type' => 'text',
                        'title' => esc_html__('Button link', 'leadengine'),
                        'required' => array('tek-header-button-action','equals','3'),
                        'default' => '#'
                    ),
                    array(
                        'id' => 'tek-button-target',
                        'type' => 'select',
                        'title' => esc_html__('Link target', 'leadengine'),
                        'required' => array('tek-header-button-action','equals','3'),
                        'options'  => array(
                            'new-page' => 'Open in a new page',
                            'same-page' => 'Open in same page'
                        ),
                        'default' => 'new-page'
                    ),
                )
            );

            $this->sections[] = array(
                'icon' => 'el-icon-website',
                'title' => esc_html__('Layout', 'leadengine'),
                'fields' => array(
                    array(
                        'id' => 'tek-layout-style',
                        'type' => 'button_set',
                        'title' => esc_html__('Layout style', 'leadengine'),
                        'subtitle' => esc_html__('Select the layout appearance.', 'leadengine'),
                        'options' => array(
                            'full-width' => 'Full-Width',
                            'boxed' => 'Boxed'
                         ),
                        'default' => 'full-width'
                    ),
                    array(
                        'id' => 'tek-layout-boxed-body-bg',
                        'type' => 'background',
                        'transparent' => false,
                        'title' => esc_html__('Body background settings', 'leadengine'),
                        'required' => array('tek-layout-style','equals','boxed'),
                    ),
                    array(
                        'id' => 'tek-layout-fw-content-bg',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Content background color', 'leadengine'),
                        'default' => '',
                        'validate' => 'color'
                    ),
                )
            );

            $this->sections[] = array(
                'icon' => 'el-icon-photo',
                'title' => esc_html__('Footer', 'leadengine'),
                'fields' => array(

                    array(
                        'id' => 'tek-footer-fixed',
                        'type' => 'switch',
                        'title' => esc_html__('Set footer fixed to bottom', 'leadengine'),
                        'subtitle' => esc_html__('Enable to activate this feature.', 'leadengine'),
                        'default' => false
                    ),
                    array(
                        'id' => 'tek-upper-footer-color',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Upper footer background', 'leadengine'),
                        'default' => '',
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-lower-footer-color',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Lower footer background', 'leadengine'),
                        'default' => '',
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-footer-heading-color',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Footer headings color', 'leadengine'),
                        'default' => '',
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-footer-text-color',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Footer text color', 'leadengine'),
                        'default' => '',
                        'validate' => 'color'
                    ),
                    array(
                          'id' => 'tek-footer-link-color',
                          'type' => 'link_color',
                          'title' => esc_html__( 'Footer links color', 'leadengine' ),
                          'active' => false,
                          'visited' => false,
                      ),
                    array(
                        'id' => 'tek-footer-text',
                        'type' => 'text',
                        'title' => esc_html__('Copyright text', 'leadengine'),
                        'subtitle' => esc_html__('Enter footer bottom copyright text', 'leadengine'),
                        'default' => 'LeadEngine by KeyDesign. All rights reserved.'
                    ),

                )
            );

            $this->sections[] = array(
                'icon' => 'el-icon-fontsize',
                'title' => esc_html__('Typography', 'leadengine'),
                'fields' => array(
                    array(
                        'id' => 'tek-default-typo',
                        'type' => 'typography',
                        'title' => esc_html__('Body typography', 'leadengine'),
                        'line-height' => true,
                        'text-align' => false,
                        'units' => 'px',
                        'default' => array(
                            'color' => '',
                            'font-weight' => '',
                            'font-family' => '',
                            'font-size' => '',
                            'line-height' => ''
                        ),
                    ),
                    array(
                        'id' => 'tek-h1-heading',
                        'type' => 'typography',
                        'title' => esc_html__('H1 heading', 'leadengine'),
                        'line-height' => true,
                        'text-align' => true,
                        'text-transform' => true,
                        'letter-spacing' => true,
                        'units' => 'px',
                    ),
                    array(
                        'id' => 'tek-h2-heading',
                        'type' => 'typography',
                        'title' => esc_html__('H2 heading', 'leadengine'),
                        'line-height' => true,
                        'text-align' => true,
                        'text-transform' => true,
                        'letter-spacing' => true,
                        'units' => 'px',
                    ),
                    array(
                        'id' => 'tek-h3-heading',
                        'type' => 'typography',
                        'title' => esc_html__('H3 heading', 'leadengine'),
                        'line-height' => true,
                        'text-align' => true,
                        'text-transform' => true,
                        'letter-spacing' => true,
                        'units' => 'px',
                    ),
                    array(
                        'id' => 'tek-h4-heading',
                        'type' => 'typography',
                        'title' => esc_html__('H4 heading', 'leadengine'),
                        'line-height' => true,
                        'text-align' => true,
                        'text-transform' => true,
                        'letter-spacing' => true,
                        'units' => 'px',
                    ),
                    array(
                        'id' => 'tek-h5-heading',
                        'type' => 'typography',
                        'title' => esc_html__('H5 heading', 'leadengine'),
                        'line-height' => true,
                        'text-align' => true,
                        'text-transform' => true,
                        'letter-spacing' => true,
                        'units' => 'px',
                    ),
                    array(
                        'id' => 'tek-h6-heading',
                        'type' => 'typography',
                        'title' => esc_html__('H6 heading', 'leadengine'),
                        'line-height' => true,
                        'text-align' => true,
                        'text-transform' => true,
                        'letter-spacing' => true,
                        'units' => 'px',
                    ),
                )
            );

            $this->sections[] = array(
                'title' => esc_html__('Typekit Fonts', 'leadengine'),
                'subsection' => true,
                'fields' => array(
                  array(
                      'id' => 'tek-typekit-switch',
                      'type' => 'switch',
                      'title' => esc_html__('Enable Typekit', 'leadengine'),
                      'subtitle' => esc_html__('Select to enable Typekit fonts and display options below.', 'leadengine'),
                      'default' => true
                  ),
                  array(
                      'id' => 'tek-typekit',
                      'type' => 'text',
                      'title' => __('Typekit ID', 'leadengine'),
                      'subtitle' => esc_html__('Enter in the ID for your kit here. Only published data is accessible, so make sure that any changes you make to your kit are updated.', 'leadengine'),
                      'mode' => 'text',
                      'default' => '',
                      'theme' => 'chrome',
                      'required' => array('tek-typekit-switch','equals', true),
                  ),
                  array(
                      'id' => 'tek-body-typekit-selector',
                      'type' => 'text',
                      'title' => __('Body typography', 'leadengine'),
                      'subtitle' => esc_html__('Add the Typekit font family name.', 'leadengine'),
                      'default' => '',
                      'required' => array('tek-typekit-switch','equals', true),
                  ),
                  array(
                      'id' => 'tek-heading-typekit-selector',
                      'type' => 'text',
                      'title' => __('Headings typography', 'leadengine'),
                      'subtitle' => esc_html__('Add the Typekit font family name.', 'leadengine'),
                      'default' => '',
                      'required' => array('tek-typekit-switch','equals', true),
                  ),
                )
            );

            $this->sections[] = array(
                'title' => esc_html__('Button Fonts', 'leadengine'),
                'subsection' => true,
                'fields' => array(
                  array(
                      'id' => 'tek-button-typo',
                      'type' => 'typography',
                      'title' => esc_html__('Button typography', 'leadengine'),
                      'line-height' => true,
                      'text-align' => false,
                      'color' => true,
                      'text-transform' => true,
                      'letter-spacing' => false,
                      'units' => 'px',
                  ),
                )
            );

            if (class_exists('WooCommerce')) {
              $this->sections[] = array(
                  'icon' => 'el-icon-shopping-cart',
                  'title' => esc_html__('WooCommerce', 'leadengine'),
                  'fields' => array(
                      array(
                          'id' => 'tek-woo-catalog-style',
                          'type' => 'select',
                          'title' => esc_html__('Catalog style', 'leadengine'),
                          'options'  => array(
                              'woo-minimal-style' => 'Minimal',
                              'woo-detailed-style' => 'Detailed',
                          ),
                          'default' => 'woo-detailed-style'
                      ),
                      array(
                          'id' => 'tek-woo-products-number',
                          'type' => 'text',
                          'title' => __('Products per page', 'leadengine'),
                          'subtitle' => esc_html__('Change the products number listed per page.', 'leadengine'),
                          'default' => '9',
                      ),
                      array(
                          'id' => 'tek-woo-shop-columns',
                          'type' => 'select',
                          'title' => esc_html__('Shop columns', 'leadengine'),
                          'subtitle' => esc_html__('Note: Shop sidebar can be displayed only with 2 columns layout', 'leadengine'),
                          'options'  => array(
                              'woo-2-columns' => '2',
                              'woo-3-columns' => '3',
                              'woo-4-columns' => '4',
                          ),
                          'default' => 'woo-2-columns'
                      ),
                      array(
                          'id' => 'tek-woo-sidebar-position',
                          'type' => 'select',
                          'title' => esc_html__('Sidebar position', 'leadengine'),
                          'options'  => array(
                              'woo-sidebar-left' => 'Left',
                              'woo-sidebar-right' => 'Right',
                          ),
                          'required' => array('tek-woo-shop-columns','equals','woo-2-columns'),
                          'default' => 'woo-sidebar-right'
                      ),
                      array(
                        'id' => 'tek-woo-hide-cart-icon',
                        'type' => 'switch',
                        'title' => esc_html__('Hide cart icon', 'leadengine'),
                        'subtitle' => esc_html__('Hide/Show cart icon from topbar.', 'leadengine'),
                        'default' => '0',
                        '1' => 'Hide',
                        '0' => 'Show',
                    ),
                  )
              );

              $this->sections[] = array(
                  'title' => esc_html__('Single Product', 'leadengine'),
                  'subsection' => true,
                  'fields' => array(
                    array(
                        'id' => 'tek-woo-single-sidebar',
                        'type' => 'switch',
                        'title' => esc_html__('Product sidebar', 'leadengine'),
                        'subtitle' => esc_html__('Enable/Disable Shop sidebar on single product page.', 'leadengine'),
                        'default' => '1',
                        '1' => 'Yes',
                        '0' => 'No',
                    ),
                    array(
                        'id' => 'tek-woo-single-sidebar-position',
                        'type' => 'select',
                        'title' => esc_html__('Sidebar position', 'leadengine'),
                        'options'  => array(
                            'woo-single-sidebar-left' => 'Left',
                            'woo-single-sidebar-right' => 'Right',
                        ),
                        'required' => array('tek-woo-single-sidebar','equals','1'),
                        'default' => 'woo-single-sidebar-right'
                    ),
                    array(
                        'id' => 'tek-woo-single-image-position',
                        'type' => 'select',
                        'title' => esc_html__('Featured image position', 'leadengine'),
                        'options' => array(
                            'woo-image-left' => 'Left',
                            'woo-image-right' => 'Right',
                        ),
                        'default' => 'woo-image-left'
                    ),
                    array(
                        'id' => 'tek-woo-single-gallery',
                        'type' => 'select',
                        'title' => esc_html__('Gallery template', 'leadengine'),
                        'options' => array(
                            'woo-gallery-thumbnails' => 'Thumbnails',
                            'woo-gallery-list' => 'List',
                        ),
                        'default' => 'woo-gallery-thumbnails'
                    ),
                  )
              );
            }

            $this->sections[] = array(
                'icon' => 'el-icon-th-list',
                'title' => esc_html__('Portfolio', 'leadengine'),
                'fields' => array(
                  array(
                      'id' => 'tek-portfolio-single-nav',
                      'type' => 'switch',
                      'title' => esc_html__('Previous/Next pagination', 'leadengine'),
                      'subtitle' => esc_html__('Enable to display the previous/next portfolio pagination. Pagination section will be displayed below the content.', 'leadengine'),
                      'default' => true
                  ),
                  array(
                      'id' => 'tek-portfolio-related-posts',
                      'type' => 'switch',
                      'title' => esc_html__('Related projects', 'leadengine'),
                      'subtitle' => esc_html__('Enable to display related projects on single portfolio pages.', 'leadengine'),
                      'default' => true
                  ),
                  array(
                      'id' => 'tek-portfolio-related-posts-title',
                      'type' => 'text',
                      'title' => esc_html__('Related projects title', 'leadengine'),
                      'default' => 'Related projects',
                      'required' => array(
                        'tek-portfolio-related-posts',
                        'equals',
                        true
                      ),
                  ),
                  array(
                      'id' => 'tek-portfolio-related-posts-number',
                      'type' => 'slider',
                      'title' => esc_html__( 'Number of related projects', 'leadengine' ),
                      'subtitle' => esc_html__( 'Controls the number of items that display under related projects section.', 'leadengine' ),
                      'default' => 3,
                      'max' => 20,
                      'required' => array(
                        'tek-portfolio-related-posts',
                        'equals',
                        true
                      ),
                  ),
                  array(
                      'id' => 'tek-portfolio-comments',
                      'type' => 'switch',
                      'title' => esc_html__('Comments section', 'leadengine'),
                      'subtitle' => esc_html__('Enable to display the comments section below the content.', 'leadengine'),
                      'default' => false
                  ),
                )
            );

            $this->sections[] = array(
                'icon' => 'el-icon-pencil-alt',
                'title' => esc_html__('Blog', 'leadengine'),
                'fields' => array(
                    array(
                        'id'=>'tek-blog-settings-section-start',
                        'type' => 'section',
                        'title' => esc_html__('General Settings', 'leadengine'),
                        'indent' => true,
                    ),
                    array(
                        'id' => 'tek-blog-transparent-nav',
                        'type' => 'switch',
                        'title' => esc_html__('Transparent Navbar', 'leadengine'),
                        'subtitle' => esc_html__('If enabled, the navbar section will take a transparent color. This option is linked with the homepage transparent settings.', 'leadengine'),
                        'default' => false
                    ),
                    array(
                        'id' => 'tek-blog-template',
                        'type' => 'select',
                        'title' => esc_html__('Blog page template', 'leadengine'),
                        'subtitle' => esc_html__('Select the blog page template style.', 'leadengine'),
                        'options'  => array(
                            'img-top-list' => 'List - Top image',
                            'img-left-list' => 'List - Left image',
                            'minimal-list' => 'List minimal',
                            'minimal-grid' => 'Grid minimal',
                            'detailed-grid' => 'Grid detailed',
                        ),
                        'default' => 'img-top-list'
                    ),
                    array(
                        'id' => 'tek-blog-sidebar',
                        'type' => 'switch',
                        'title' => esc_html__('Display sidebar', 'leadengine'),
                        'subtitle' => esc_html__('Turn on to display the blog sidebar.', 'leadengine'),
                        'default' => true
                    ),
                    array(
                        'id' => 'tek-blog-excerpt',
                        'type' => 'text',
                        'title' => esc_html__('Blog post excerpt', 'leadengine'),
                        'default' => '27'
                    ),
                    array(
                      	'id'=>'tek-blog-settings-section-end',
                      	'type' => 'section',
                      	'indent' => false,
                    ),
                    array(
                        'id'=>'tek-blog-title-section-start',
                        'type' => 'section',
                        'title' => esc_html__('Blog Title Bar', 'leadengine'),
                        'indent' => true,
                    ),
                    array(
                        'id' => 'tek-blog-title-switch',
                        'type' => 'switch',
                        'title' => esc_html__('Blog page title', 'leadengine'),
                        'subtitle' => esc_html__('Turn on to show the page title of the assigned blog page.', 'leadengine'),
                        'default' => true
                    ),
                    array(
                        'id' => 'tek-blog-particles',
                        'type' => 'switch',
                        'title' => esc_html__('Enable particles', 'leadengine'),
                        'subtitle' => esc_html__('Turn on to load a particles effect in the title bar area.', 'leadengine'),
                        'default' => false
                    ),
                    array(
                        'id' => 'tek-blog-subtitle',
                        'type' => 'text',
                        'title' => esc_html__('Blog page subtitle', 'leadengine'),
                        'subtitle' => esc_html__('Add the subtitle text that displays in the page title bar of the assigned blog page.', 'leadengine'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-blog-header-text-align',
                        'type' => 'select',
                        'title' => esc_html__('Title bar text alignment', 'leadengine'),
                        'options'  => array(
                            'blog-title-left' => 'Left',
                            'blog-title-center' => 'Center',
                        ),
                        'required' => array('tek-blog-title-switch','equals', true),
                        'subtitle' => esc_html__('Select text alignment in the title bar area.', 'leadengine'),
                        'default' => 'blog-title-left'
                    ),
                    array(
                        'id' => 'tek-blog-titlebar-background',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Title bar background color', 'leadengine'),
                        'default' => '',
                        'subtitle' => esc_html__('Use this colorpicker to override the title bar default background color.', 'leadengine'),
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-blog-text-color',
                        'type' => 'color',
                        'transparent' => false,
                        'title' => esc_html__('Title bar text color', 'leadengine'),
                        'default' => '',
                        'subtitle' => esc_html__('Use this colorpicker to override the title bar default text color.', 'leadengine'),
                        'validate' => 'color'
                    ),
                    array(
                        'id' => 'tek-blog-header-form',
                        'type' => 'switch',
                        'title' => esc_html__('Show newsletter form', 'leadengine'),
                        'subtitle' => esc_html__('Turn on to display a form in the header area.', 'leadengine'),
                        'default' => false
                    ),
                    array(
                        'id' => 'tek-blog-header-form-id',
                        'type' => 'select',
                        'data' => 'posts',
                        'args' => array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1, ),
                        'title' => esc_html__('Newsletter form title', 'leadengine'),
                        'subtitle' => esc_html__('Select the Contact Form 7 to be used as a newsletter in the title bar area.', 'leadengine'),
                        'required' => array('tek-blog-header-form','equals', true),
                    ),
                    array(
                      	'id'=>'tek-blog-title-section-end',
                      	'type' => 'section',
                      	'indent' => false,
                    ),
                )
            );

            $this->sections[] = array(
                'title' => esc_html__('Single Post', 'leadengine'),
                'subsection' => true,
                'fields' => array(
                  array(
                      'id' => 'tek-blog-single-sidebar',
                      'type' => 'switch',
                      'title' => esc_html__('Display sidebar', 'leadengine'),
                      'subtitle' => esc_html__('Turn on/off single post sidebar', 'leadengine'),
                      'default' => true
                  ),
                  array(
                      'id' => 'tek-blog-social-sharing',
                      'type' => 'switch',
                      'title' => esc_html__('Social sharing', 'leadengine'),
                      'subtitle' => esc_html__('Enable to display the social sharing buttons on single posts.', 'leadengine'),
                      'default' => false
                  ),
                  array(
                      'id' => 'tek-author-box',
                      'type' => 'switch',
                      'title' => esc_html__('Author box', 'leadengine'),
                      'subtitle' => esc_html__('Enable to display author box below post content.', 'leadengine'),
                      'default' => true
                  ),
                  array(
                      'id' => 'tek-blog-single-nav',
                      'type' => 'switch',
                      'title' => esc_html__('Previous/Next pagination', 'leadengine'),
                      'subtitle' => esc_html__('Enable to display the previous/next post pagination for single posts.', 'leadengine'),
                      'default' => false
                  ),
                  array(
                      'id' => 'tek-related-posts',
                      'type' => 'switch',
                      'title' => esc_html__('Related posts', 'leadengine'),
                      'subtitle' => esc_html__('Enable to display related posts on single post pages.', 'leadengine'),
                      'default' => true
                  ),
                  array(
                      'id' => 'tek-related-posts-title',
                      'type' => 'text',
                      'title' => esc_html__('Related posts title', 'leadengine'),
                      'default' => 'You may also like',
                      'required' => array(
                                'tek-related-posts',
                                'equals',
                                true
                            ),
                  ),
                  array(
                            'id'       => 'tek-related-posts-number',
                      'type'     => 'slider',
                            'title'    => esc_html__( 'Number of related posts', 'leadengine' ),
                            'subtitle' => esc_html__( 'Controls the number of posts that display under related posts section.', 'leadengine' ),
                            'default'  => 3,
                            'max'      => 20,
                            'required' => array(
                                'tek-related-posts',
                                'equals',
                                true
                            ),
                    )
                )
            );

            $this->sections[] = array(
                'icon' => 'el-icon-error-alt',
                'title' => esc_html__('404 page', 'leadengine'),
                'fields' => array(
                    array(
                        'id' => 'tek-404-title',
                        'type' => 'text',
                        'title' => esc_html__('Page title', 'leadengine'),
                        'default' => 'Error 404'
                    ),
                    array(
                        'id' => 'tek-404-subtitle',
                        'type' => 'text',
                        'title' => esc_html__('Page subtitle', 'leadengine'),
                        'default' => 'This page could not be found!'
                    ),
                    array(
                        'id' => 'tek-404-back',
                        'type' => 'text',
                        'title' => esc_html__('Back to homepage button text', 'leadengine'),
                        'default' => 'Back to homepage'
                    )
                )
            );
            $this->sections[] = array(
                'icon' => 'el-icon-wrench-alt',
                'title' => esc_html__('Maintenance page', 'leadengine'),
                'fields' => array(
                    array(
                        'id' => 'tek-maintenance-mode',
                        'type' => 'switch',
                        'title' => __('Enable Maintenance mode', 'leadengine'),
                        'subtitle' => esc_html__('Activate to enable maintenance mode.', 'leadengine'),
                        'default' => false
                    ),
                    array(
                        'id' => 'tek-maintenance-title',
                        'type' => 'text',
                        'title' => esc_html__('Page title', 'leadengine'),
                        'required' => array('tek-maintenance-mode','equals', true),
                        'default' => 'LeadEngine is in the works!'
                    ),
                    array(
                        'id' => 'tek-maintenance-content',
                        'type' => 'editor',
                        'title' => esc_html__('Page content', 'leadengine'),
                        'required' => array('tek-maintenance-mode','equals', true),
                        'default' => '',
		                    'args'   => array(
                          'teeny'  => true,
                          'textarea_rows' => 10,
                          'media_buttons' => false,
			                  )
                    ),
                    array(
                        'id' => 'tek-maintenance-countdown',
                        'type' => 'switch',
                        'title' => __('Enable countdown', 'leadengine'),
                        'subtitle' => esc_html__('Activate to enable the countdown timer.', 'leadengine'),
                        'required' => array('tek-maintenance-mode','equals', true),
                        'default' => false
                    ),
                    array(
                        'id' => 'tek-maintenance-count-day',
                        'type' => 'text',
                        'title' => esc_html__('End day', 'leadengine'),
                        'subtitle' => esc_html__('Enter day value. Eg. 05', 'leadengine'),
                        'required' => array('tek-maintenance-countdown','equals', true),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-maintenance-count-month',
                        'type' => 'text',
                        'title' => esc_html__('End month', 'leadengine'),
                        'subtitle' => esc_html__('Enter month value. Eg. 09', 'leadengine'),
                        'required' => array('tek-maintenance-countdown','equals', true),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-maintenance-count-year',
                        'type' => 'text',
                        'title' => esc_html__('End year', 'leadengine'),
                        'subtitle' => esc_html__('Enter year value. Eg. 2020', 'leadengine'),
                        'required' => array('tek-maintenance-countdown','equals', true),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-maintenance-days-text',
                        'type' => 'text',
                        'title' => esc_html__('Days label', 'leadengine'),
                        'subtitle' => esc_html__('Enter days text label.', 'leadengine'),
                        'required' => array('tek-maintenance-countdown','equals', true),
                        'default' => 'Days'
                    ),
                    array(
                        'id' => 'tek-maintenance-hours-text',
                        'type' => 'text',
                        'title' => esc_html__('Hours label', 'leadengine'),
                        'subtitle' => esc_html__('Enter hours text label.', 'leadengine'),
                        'required' => array('tek-maintenance-countdown','equals', true),
                        'default' => 'Hours'
                    ),
                    array(
                        'id' => 'tek-maintenance-minutes-text',
                        'type' => 'text',
                        'title' => esc_html__('Minutes label', 'leadengine'),
                        'subtitle' => esc_html__('Enter minutes text label.', 'leadengine'),
                        'required' => array('tek-maintenance-countdown','equals', true),
                        'default' => 'Minutes'
                    ),
                    array(
                        'id' => 'tek-maintenance-seconds-text',
                        'type' => 'text',
                        'title' => esc_html__('Seconds label', 'leadengine'),
                        'subtitle' => esc_html__('Enter seconds text label.', 'leadengine'),
                        'required' => array('tek-maintenance-countdown','equals', true),
                        'default' => 'Seconds'
                    ),
                    array(
                        'id' => 'tek-maintenance-subscribe',
                        'type' => 'switch',
                        'title' => __('Enable contact form', 'leadengine'),
                        'subtitle' => esc_html__('Activate to enable contact form on page.', 'leadengine'),
                        'required' => array('tek-maintenance-mode','equals', true),
                        'default' => false
                    ),
                    array(
                        'id' => 'tek-maintenance-form-select',
                        'type' => 'select',
                        'title' => esc_html__('Contact form plugin', 'leadengine'),
                        'required' => array('tek-maintenance-subscribe','equals',true),
                        'options'  => array(
                            '1' => 'Contact Form 7',
                            '2' => 'Ninja Forms',
                            '3' => 'Gravity Forms',
                            '4' => 'WP Forms',
                        ),
                        'default' => '1'
                    ),
                    array(
                        'id' => 'tek-maintenance-contactf7-formid',
                        'type' => 'select',
                        'data' => 'posts',
                        'args' => array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1, ),
                        'title' => esc_html__('Contact Form 7 Title', 'leadengine'),
                        'required' => array('tek-maintenance-form-select','equals','1'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-maintenance-ninja-formid',
                        'type' => 'text',
                        'title' => esc_html__('Ninja Form ID', 'leadengine'),
                        'required' => array('tek-maintenance-form-select','equals','2'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-maintenance-gravity-formid',
                        'type' => 'text',
                        'title' => esc_html__('Gravity Form ID', 'leadengine'),
                        'required' => array('tek-maintenance-form-select','equals','3'),
                        'default' => ''
                    ),
                    array(
                        'id' => 'tek-maintenance-wp-formid',
                        'type' => 'text',
                        'title' => esc_html__('WP Form ID', 'leadengine'),
                        'required' => array('tek-maintenance-form-select','equals','4'),
                        'default' => ''
                    ),

                )
            );
            $this->sections[] = array(
                'icon' => 'el-icon-css',
                'title' => esc_html__('Custom CSS/JS', 'leadengine'),
                'fields' => array(
                    array(
                        'id' => 'tek-css',
                        'type' => 'ace_editor',
                        'title' => esc_html__('CSS', 'leadengine'),
                        'subtitle' => esc_html__('Enter your CSS code in the side field. Do not include any tags or HTML in the field. Custom CSS entered here will override the theme CSS.', 'leadengine'),
                        'mode' => 'css',
                        'theme' => 'chrome',
                    ),
                    array(
                  			'id' => 'tek-javascript',
                  			'type' => 'ace_editor',
                  			'title' => esc_html__( 'Javascript', 'leadengine' ),
                  			'subtitle' => esc_html__( 'Only accepts Javascript code.', 'leadengine' ),
                  			'mode' => 'html',
                  			'theme' => 'chrome',
                		),
                )
            );
        }
        /**
        * All the possible arguments for Redux.
        * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
        **/
        public function setArguments()
        {
            $theme                         = wp_get_theme(); // For use with some settings. Not necessary.
            $this->args                    = array(
                'opt_name' => 'redux_ThemeTek',
                'menu_type'            => 'submenu',

                'menu_title'           => esc_html__( 'Theme Options', 'leadengine' ),
                'page_title'           => esc_html__( 'Theme Options', 'leadengine' ),

                'async_typography'     => false,
                'admin_bar'            => false,
                'dev_mode'             => false,
                'show_options_object'  => false,
                'customizer'           => false,
                'show_import_export'   => true,

                'page_parent'          => 'leadengine-dashboard',
                'page_permissions'     => 'manage_options',
                'page_slug'            => 'theme-options',
                'hints' => array(
                    'icon' => 'el-icon-question-sign',
                    'icon_position' => 'right',
                    'icon_size' => 'normal',
                    'tip_style' => array(
                        'color' => 'light'
                    ),
                    'tip_position' => array(
                        'my' => 'top left',
                        'at' => 'bottom right'
                    ),
                    'tip_effect' => array(
                        'show' => array(
                            'duration' => '500',
                            'event' => 'mouseover'
                        ),
                        'hide' => array(
                            'duration' => '500',
                            'event' => 'mouseleave unfocus'
                        )
                    )
                ),
                'output' => '1',
                'output_tag' => '1',
                'compiler' => '0',
                'page_icon' => 'icon-themes',
                'page_permissions' => 'manage_options',
                'save_defaults' => '1',
                'show_import_export' => '1',
                'transient_time' => '3600',
                'network_sites' => '1'
            );
            $theme                         = wp_get_theme(); // For use with some settings. Not necessary.
            $this->args["display_name"]    = $theme->get("Name");
            $this->args["display_version"] = $theme->get("Version");

        }
    }
    global $reduxConfig;
    $reduxConfig = new keydesign_Redux_Framework_config();
}
