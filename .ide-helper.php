<?php
/**
 * IDE Helper file for MedCare
 * This file helps IDEs recognize global helper functions
 */

namespace {
    if (!function_exists('url')) {
        /**
         * Generate a URL for the application
         *
         * @param string $path
         * @return string
         */
        function url(string $path = ''): string
        {
            return \App\Helpers\ViewHelper::url($path);
        }
    }

    if (!function_exists('old')) {
        /**
         * Get an old input value from the session
         *
         * @param string $key
         * @param mixed $default
         * @return mixed
         */
        function old(string $key, mixed $default = ''): mixed
        {
            return \App\Helpers\ViewHelper::old($key, $default);
        }
    }
}
