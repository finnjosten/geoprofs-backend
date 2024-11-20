<?php


    // Format
    /**
     * Set meta data for a page
     */
    if (! function_exists('vlx_set_page_meta')) {
        function vlx_set_page_meta($custom = null) {
            if ($custom) {
                echo "<meta name='description' content='$custom'>";
            } else {
                echo "<meta name='description' content='". env("APP_DESCRIPTION") ."'>";
            }
            echo '
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
            ';
        }
    }
    /**
     * Set meta data for a page that is used by socials
     */
    if (! function_exists('vlx_set_social_meta')) {
        function vlx_set_social_meta() {
            echo '
                <meta property="og:title" content="' . env("APP_NAME") . '">
                <meta property="og:description" content="'. env("APP_DESCRIPTION") .'">
                <meta property="og:image" content="'. env("APP_URL") .'">
                <meta property="og:url" content="'. env("APP_URL") .'">

                <meta name="twitter:title" content="Add title here">
                <meta name="twitter:description" content="'. env("APP_DESCRIPTION") .'">
                <meta name="twitter:image" content="'. env("APP_URL") .'">
                <meta name="twitter:url" content="'. env("APP_URL") .'">
            ';
        }
    }




    // Format
    /**
     * Format a string (remove underscores and semicolons)
     */
    if (! function_exists('vlx_format')) {
        function vlx_format($string) {

            if(str_contains($string, '_')) { $string = str_replace('_', ' ', $string); }
            if(str_contains($string, ';')) { $string = str_replace(';', '', $string); }

            return $string;

        }
    }

    /**
     * Format a number
     */
    if (! function_exists('vlx_number_format')) {
        function vlx_number_format($input, $decimals){
            return number_format($input, $decimals, '.', ',');
        }
    }

    /**
     * Format a route name
     */
    if (! function_exists('vlx_format_route_name')) {
        function vlx_format_route_name($string) {

            $string = explode('.', $string)[0];
            $string = str_replace('-', ' ', $string);
            $string = ucwords($string);

            return $string;

        }
    }




    // Make something from something
    /**
     * Slugify a string
     */
    if (! function_exists('vlx_slugify')) {
        function vlx_slugify($string) {
            return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
        }
    }

    /**
     * Emailfy a name
     */
    if (! function_exists('vlx_emailfy')) {
        function vlx_emailfy($name) {

            $email = strtolower($name);
            $email = str_replace('.', '', $email);
            $email = str_replace(' ', '.', $email);
            $email = $email . '@' . vlx_get_app_domain();

            return $email;

        }
    }




    // Slashes
    /**
     * Add a slash at the start of a string
     */
    if (! function_exists('vlx_start_slash_it')) {
        function vlx_start_slash_it($string) {

            $string = trim($string, '/');
            $string = '/' . $string;

            return preg_replace('#/+#', '/', $string);

        }
    }

    /**
     * Add a slash at the end of a string
     */
    if (!function_exists('vlx_end_slash_it')) {
        function vlx_end_slash_it($string) {

            $string = rtrim($string, '/');
            $string .= '/';

            return preg_replace('#/+#', '/', $string);

        }
    }




    // Route urls
    /**
     * Get the account url
     */
    if (! function_exists('vlx_get_account_url')) {
        function vlx_get_account_url() {

            $url = !empty(env('SETTING_ACCOUNT_URL')) ? env('SETTING_ACCOUNT_URL') : 'account';
            return vlx_start_slash_it($url);

        }
    }

    /**
     * Get the admin url
     */
    if (! function_exists('vlx_get_admin_url')) {
        function vlx_get_admin_url() {

            $url = !empty(env('SETTING_ADMIN_URL')) ? env('SETTING_ADMIN_URL') : 'admin';
            return vlx_start_slash_it($url);

        }
    }

    /**
     * Get the auth url
     */
    if (! function_exists('vlx_get_auth_url')) {
        function vlx_get_auth_url() {

            $url = !empty(env('SETTING_AUTH_URL')) ? env('SETTING_AUTH_URL') : 'auth';
            return vlx_start_slash_it($url);

        }
    }




    // Get paths
    /**
     * Get the path to the ssh keys folder
     */
    if (! function_exists('vlx_get_ssh_key_path')) {
        function vlx_get_ssh_key_path() {

            $path = env('SSH_KEY_PATH');
            return $path;

        }
    }




    // App domain
    /**
     * Get the domain of the app
     */
    if (! function_exists('vlx_get_app_domain')) {
        function vlx_get_app_domain() {

            $domain = env('APP_DOMAIN');
            $domain = str_replace(['http:', 'https:', '/'], '', $domain);

            return $domain;

        }
    }




    // Get shit from env
    /**
     * Get a string from the ENV file (unless it contains a "KEY", "API", "USERNAME", "PASS")
     */
    if (! function_exists('vlx_get_env_string')) {
        function vlx_get_env_string($env_key) {

            //if(str_contains($env_key, 'KEY')) return null;
            if(str_contains($env_key, 'API')) return null;
            if(str_contains($env_key, 'USERNAME')) return null;
            if(str_contains($env_key, 'PASS')) return null;

            $string = env($env_key);
            $string = vlx_format($string);

            return $string;
        }
    }




    // Check if a string is an error
    /**
     * Check if string is error
     */
    if (! function_exists('vlx_error')) {
        function vlx_error($str) {
            if (is_string($str) && str_starts_with($str, 'error:')) {
                return true;
            }
            return false;
        }
    }




    // UUID (DEPRECATED)
    /**
     * Create a UUID (Universally Unique Identifier)
     * @deprecated
     */
    if (! function_exists('vlx_make_uuid')) {
        function vlx_make_uuid() {
            // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
            $data = random_bytes(16);
            assert(strlen($data) == 16);

            // Set version to 0100
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            // Set bits 6-7 to 10
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

            // Output the 36 character UUID.
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
    }

?>
