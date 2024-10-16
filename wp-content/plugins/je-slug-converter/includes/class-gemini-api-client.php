<?php

class Gemini_API_Client {
    private $api_key;
    private $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function translate_to_english($text) {
        $prompt = "Please convert the following Japanese text into SEO-friendly English URLs. 
        The result should be in lowercase, spaces and special characters should be replaced with hyphens, 
        and the conversion should be concise and meaningful in English. Only output the final converted string. 
        Do not include any additional information or explanations. Use only hyphens for special characters. 
        INPUT: {$text}";

        $response = wp_remote_post($this->api_url . '?key=' . $this->api_key, array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode(array(
                'contents' => array(
                    array('parts' => array(array('text' => $prompt)))
                )
            ))
        ));

        error_log($response['body']);
        if (is_wp_error($response)) {
            return $text;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $this->create_slug($data['candidates'][0]['content']['parts'][0]['text']);
        }

        return $text; // Return original text if translation fails
    }

    private function create_slug($text) {
        return sanitize_title($text);
    }
}
