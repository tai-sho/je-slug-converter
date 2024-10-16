<?php

class JE_Slug_Converter {
    private $options;
    private $gemini_client;

    public function __construct() {
        $this->load_dependencies();
        $this->define_hooks();
        $this->options = get_option('je_slug_converter_options');
        $this->gemini_client = new Gemini_API_Client($this->options['gemini_api_key']);
    }

    private function load_dependencies() {
        require_once JE_SLUG_CONVERTER_PLUGIN_DIR . 'includes/class-gemini-api-client.php';
    }

    private function define_hooks() {
//        add_filter('name_save_pre', array($this, 'convert_slug_to_english'), 10, 1);
//        add_filter('wp_insert_post_data', array($this, 'convert_post_slug_to_english'), 10, 2);
        add_filter('sanitize_title', array($this, 'convert_slug_to_english'), 10, 3);
    }

    public function run() {
        // Run the plugin
    }

    public function convert_slug_to_english($slug) {
        $slug = urldecode($slug);
        if ($this->is_japanese($slug)) {
            return $this->gemini_client->translate_to_english($slug);
        }
        return $slug;
    }

    public function convert_post_slug_to_english($data, $postarr) {
        if (empty($data['post_name']) && !empty($data['post_title'])) {
            $data['post_name'] = $this->convert_slug_to_english($data['post_title']);
        }
        return $data;
    }

    private function is_japanese($text) {
        return preg_match('/[\p{Han}\p{Hiragana}\p{Katakana}]/u', $text);
    }
}