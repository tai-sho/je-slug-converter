<?php

class JE_Slug_Converter_Settings {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_notices', array($this, 'admin_notices'));
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

            <div class="je-slug-converter-intro">
                <h2>Welcome to JE Slug Converter</h2>
                <p>
                    JE Slug Converterは日本語で生成されるスラッグを英語変換するプラグインです。SEOフレンドリーなURLへ自動変換します。<br>
                    変換にはGoogleのGemini Pro APIを使用します。Gemini Pro APIは2024/10時点では無料で利用できますが、<a href="https://ai.google.dev/pricing?hl=ja#1_5pro" target="_blank">料金ページ</a>を必ず確認してください。</p>
            </div>

            <div class="je-slug-converter-usage">
                <h3>How to Use</h3>
                <ol>
                    <li><a href="https://aistudio.google.com/apikey" target="_blank">Google AI Studioの APIキー取得ページ</a>からAPIキーを取得してください。APIキーの取得にはGoogleアカウントが必要です。</li>
                    <li>Plugin SettingsからAPIキーを設定してください。</li>
                    <li>登録後、</li>
                    <li>The plugin will automatically generate an English slug for you!</li>
                </ol>
            </div>

            <form method="post" action="options.php">
                <?php
                settings_fields('je_slug_converter_option_group');
                do_settings_sections('je-slug-converter-admin');
                submit_button();
                ?>
            </form>

            <div class="je-slug-converter-author">
                <h3>About the Author</h3>
                <p>JE Slug Converter is developed by ShoheiTai. For support or inquiries, please contact:</p>
                <ul>
                    <li>X: <a href="https://x.com/ShoheiTai/" target="_blank">https://x.com/ShoheiTai/</a></li>
                    <li>GitHub: <a href="https://github.com/tai-sho" target="_blank">https://github.com/tai-sho</a></li>
                </ul>
            </div>
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
            'Plugin Settings',
            array($this, 'section_info'),
            'je-slug-converter-admin'
        );

        add_settings_field(
            'enable_conversion',
            'Enable Slug Conversion',
            array($this, 'enable_conversion_callback'),
            'je-slug-converter-admin',
            'je_slug_converter_setting_section'
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
        if(isset($input['enable_conversion']))
            $new_input['enable_conversion'] = sanitize_key($input['enable_conversion']);
        return $new_input;
    }

    public function section_info() {
        echo 'Configure your JE Slug Converter settings below:';
    }

    public function enable_conversion_callback() {
        $checked = isset($this->options['enable_conversion']) ? $this->options['enable_conversion'] : '0';
        printf(
            '<input type="checkbox" id="enable_conversion" name="je_slug_converter_options[enable_conversion]" value="1" %s />',
            checked($checked, '1', false)
        );
        echo '<label for="enable_conversion">Enable automatic slug conversion</label>';

        if ($checked == '1' && empty($this->options['gemini_api_key'])) {
            echo '<p class="description" style="color: #d63638;">Warning: Conversion is enabled but no API key is set. The conversion will not work without a valid API key.</p>';
        }
    }

    public function api_key_callback() {
        printf(
            '<input type="text" id="gemini_api_key" name="je_slug_converter_options[gemini_api_key]" value="%s" class="regular-text" />',
            isset($this->options['gemini_api_key']) ? esc_attr($this->options['gemini_api_key']) : ''
        );
        echo '<p class="description">Enter your Gemini API Key here. You can obtain this from the Google Cloud Console.</p>';
    }

    public function admin_notices() {
        $screen = get_current_screen();
        if ($screen->id != 'settings_page_je-slug-converter') {
            return;
        }

        if (isset($this->options['enable_conversion']) &&
            $this->options['enable_conversion'] == '1' &&
            empty($this->options['gemini_api_key'])) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><strong>JE Slug Converter:</strong> Slug conversion is enabled, but no API key is set. Please enter a valid Gemini API key for the conversion to work.</p>
            </div>
            <?php
        }
    }

    public function enqueue_admin_styles($hook) {
        // Only enqueue the style on the plugin's settings page
        if ($hook != 'settings_page_je-slug-converter') {
            return;
        }
        wp_enqueue_style('je-slug-converter-admin-styles', plugin_dir_url(__FILE__) . 'css/admin-style.css');
    }
}
