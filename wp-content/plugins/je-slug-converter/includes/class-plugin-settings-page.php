<?php

class JE_Slug_Converter_Settings {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    public function add_plugin_page() {
        add_options_page(
            'JE Slug Converter Settings',
            'JE Slug Converter',
            'manage_options',
            'je-slug-converter',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $this->options = get_option('je_slug_converter_options');
        ?>
        <div class="wrap">
            <h1>JE Slug Converter Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('je_slug_converter_option_group');
                do_settings_sections('je-slug-converter-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'je_slug_converter_option_group',
            'je_slug_converter_options',
            array($this, 'sanitize')
        );

        add_settings_section(
            'je_slug_converter_setting_section',
            'Settings',
            array($this, 'section_info'),
            'je-slug-converter-admin'
        );

        add_settings_field(
            'gemini_api_key',
            'Gemini API Key',
            array($this, 'api_key_callback'),
            'je-slug-converter-admin',
            'je_slug_converter_setting_section'
        );
    }

    public function sanitize($input) {
        $new_input = array();
        if(isset($input['gemini_api_key']))
            $new_input['gemini_api_key'] = sanitize_text_field($input['gemini_api_key']);

        return $new_input;
    }

    public function section_info() {
        print 'Enter your settings below:';
    }

    public function api_key_callback() {
        printf(
            '<input type="password" id="gemini_api_key" name="je_slug_converter_options[gemini_api_key]" value="%s" />',
            isset($this->options['gemini_api_key']) ? esc_attr($this->options['gemini_api_key']) : ''
        );
    }
}