<?php

class berqNotifications {
    private $transient_key = 'berqwp_user_notice'; // Define a unique key for the transient

    function __construct() {
        add_action('admin_notices', [$this, 'notification']);
        add_action('shutdown', [$this, 'maybe_clear_transient']); // Clear transient if empty after rendering notices
    }

    function notification() {
        // Get the notices from the transient
        $notices = get_transient($this->transient_key);

        if (empty($notices)) {
            return;
        }

        if (!empty($notices)) {
            foreach ($notices as $notice) {
                $msg = $notice[1];
                $class = $notice[0];

                $notice_html = '<div class="notice notice-'.$class.' is-dismissible">';
                $notice_html .= '<p>';
                $notice_html .= esc_html__($msg, 'searchpro');
                $notice_html .= '</p>';
                $notice_html .= '</div>';
                echo wp_kses_post($notice_html);
            }

            // Clear the notices after displaying them by removing the transient
            set_transient($this->transient_key, []);
        }
    }

    // Store a new notice in the transient
    function notice($text) {
        $this->add_notice('info', $text);
    }

    function error($text) {
        $this->add_notice('error', $text);
    }

    function warning($text) {
        $this->add_notice('warning', $text);
    }

    function success($text) {
        $this->add_notice('success', $text);
    }

    // Add a notice to the transient
    private function add_notice($type, $text) {
        // Get current notices
        $notices = get_transient($this->transient_key);

        if (!$notices) {
            $notices = [];
        }

        // Add the new notice
        $notices[] = [$type, $text];

        // Store the notices back in the transient (with no expiration, so it persists until explicitly cleared)
        set_transient($this->transient_key, $notices);
    }

    // Optionally clear transient if it's empty after rendering notices
    function maybe_clear_transient() {
        $notices = get_transient($this->transient_key);
        if (empty($notices)) {
            delete_transient($this->transient_key);
        }
    }
}

if (is_admin()) {
    global $berqNotifications;
    $berqNotifications = new berqNotifications();
}
