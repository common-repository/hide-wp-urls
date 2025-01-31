<?php
defined('ABSPATH') || die('Cheatin\' uh?');

/**
 * Handles the parameters and url
 *
 * @author StarBox
 */
class HMW_Classes_Tools extends HMW_Classes_FrontController {

    /** @var array Saved options in database */
    public static $init = array(), $default = array(), $lite = array(), $ninja = array();
    public static $options = array();
    public static $debug = array();
    public static $is_multisite;
    public static $active_plugins;

    /** @var integer Count the errors in site */
    static $errors_count = 0;

    public function __construct() {
        //Check the max memory usage
        $maxmemory = self::getMaxMemory();
        if ($maxmemory && $maxmemory < 60) {
            if (defined('WP_MAX_MEMORY_LIMIT') && (int)WP_MAX_MEMORY_LIMIT > 60) {
                @ini_set('memory_limit', apply_filters('admin_memory_limit', WP_MAX_MEMORY_LIMIT));
                $maxmemory = self::getMaxMemory();
                if ($maxmemory && $maxmemory < 60) {
                    define('HMW_DISABLE', true);
                    HMW_Classes_Error::setError(sprintf(__('Your memory limit is %sM. You need at least %sM to prevent loading errors in frontend. See: %sIncreasing memory allocated to PHP%s', _HMW_PLUGIN_NAME_), $maxmemory, 64, '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">', '</a>'));
                }
            } else {
                define('HMW_DISABLE', true);
                HMW_Classes_Error::setError(sprintf(__('Your memory limit is %sM. You need at least %sM to prevent loading errors in frontend. See: %sIncreasing memory allocated to PHP%s', _HMW_PLUGIN_NAME_), $maxmemory, 64, '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">', '</a>'));
            }
        }
        //Get the plugin options from database
        self::$options = self::getOptions();

        //Load multilanguage
        add_filter("init", array($this, 'loadMultilanguage'));

        //add review link in plugin list
        add_filter("plugin_row_meta", array($this, 'hookExtraLinks'), 10, 4);

        //add setting link in plugin
        add_filter('plugin_action_links', array($this, 'hookActionlink'), 5, 2);
    }

    /**
     * Check the memory and make sure it's enough
     * @return bool|string
     */
    public static function getMaxMemory() {
        try {
            $memory_limit = @ini_get('memory_limit');
            if ((int)$memory_limit > 0) {
                if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
                    if ($matches[2] == 'G') {
                        $memory_limit = $matches[1] * 1024 * 1024 * 1024; // nnnM -> nnn MB
                    } elseif ($matches[2] == 'M') {
                        $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
                    } elseif ($matches[2] == 'K') {
                        $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
                    }
                }

                if ((int)$memory_limit > 0) {
                    return number_format((int)$memory_limit / 1024 / 1024, 0, '', '');
                }
            }
        } catch (Exception $e) {
        }

        return false;

    }

    /**
     * Load the Options from user option table in DB
     *
     * @param bool|false $safe
     * @return array|mixed|object
     */
    public static function getOptions($safe = false) {
        $keymeta = HMW_OPTION;
        $homepath = ltrim(parse_url(site_url(), PHP_URL_PATH), '/');
        $pluginurl = ltrim(parse_url(plugins_url(), PHP_URL_PATH), '/');
        $contenturl = ltrim(parse_url(content_url(), PHP_URL_PATH), '/');

        if ($safe) {
            $keymeta = HMW_OPTION_SAFE;
        }

        self::$init = array(
            'hmw_ver' => 0,
            'api_token' => false,
            'hmw_token' => 0,
            'hmw_disable' => mt_rand(111111, 999999),
            'hmw_disable_name' => 'hmw_disable',
            'logout' => false,
            'error' => false,
            'configure_error' => false,
            'changes' => false,
            'admin_notice' => array(),
            //--
            'hmw_laterload' => 0,
            'hmw_fix_relative' => 1,
            'hmw_url_redirect' => '.',
            'hmw_remove_third_hooks' => 0,
            'hmw_send_email' => 1,
            'hmw_activity_log' => 1,
            'hmw_activity_log_roles' => array(),
            'hmw_email_address' => '',

            //-- Brute Force
            'hmw_bruteforce' => 0,
            'hmw_bruteforce_log' => 1,
            'hmw_brute_message' => __('Your IP has been flagged for potential security violations. Please try again in a little while...', _HMW_PLUGIN_NAME_),
            'whitelist_ip' => array(),
            'banlist_ip' => array(),
            'hmw_hide_classes' => json_encode(array()),
            'trusted_ip_header' => '',
            //
            'brute_use_math' => 0,
            'brute_max_attempts' => 5,
            'brute_max_timeout' => 3600,
            //captcha
            'brute_use_captcha' => 1,
            'brute_captcha_site_key' => '',
            'brute_captcha_secret_key' => '',
            'brute_captcha_theme' => 'light',
            'brute_captcha_language' => '',
            //
            'hmw_new_plugins' => array(),
            'hmw_new_themes' => array(),
            //
            'hmw_in_dashboard' => 0,
            'hmw_hide_version' => 0,
            'hmw_hide_header' => 0,
            'hmw_hide_comments' => 0,
            'hmw_disable_emojicons' => 0,
            'hmw_disable_xmlrpc' => 0,
            'hmw_disable_manifest' => 0,
            'hmw_disable_embeds' => 0,
            'hmw_disable_debug' => 0,
            'hmw_file_cache' => 1,
            'hmw_security_alert' => 1,
            'html_cdn_urls' => array(),
            'hmw_text_mapping' => json_encode(
                array(
                    'from' => array('wp-caption', 'wp-custom'),
                    'to' => array('caption', 'custom'),
                )
            ),
        );
        self::$default = array(
            'hmw_mode' => 'default',
            'hmw_admin_url' => 'wp-admin',
            'hmw_login_url' => 'wp-login.php',
            'hmw_activate_url' => 'wp-activate.php',
            'hmw_lostpassword_url' => '',
            'hmw_register_url' => '',
            'hmw_logout_url' => '',
            'hmw_plugin_url' => trim(preg_replace('/' . str_replace('/', '\/', $homepath) . '/', '', $pluginurl, 1), '/'),
            'hmw_plugins' => array(),
            'hmw_themes_url' => 'themes',
            'hmw_themes' => array(),
            'hmw_upload_url' => 'uploads',
            'hmw_admin-ajax_url' => 'admin-ajax.php',
            'hmw_hideajax_admin' => 0,
            'hmw_tags_url' => 'tag',
            'hmw_wp-content_url' => trim(preg_replace('/' . str_replace('/', '\/', $homepath) . '/', '', $contenturl, 1), '/'),
            'hmw_wp-includes_url' => 'wp-includes',
            'hmw_author_url' => 'author',
            'hmw_hide_authors' => 0,
            'hmw_wp-comments-post' => 'wp-comments-post.php',
            'hmw_themes_style' => 'style.css',
            'hmw_hide_img_classes' => 0,
            'hmw_hide_styleids' => 0,
            'hmw_wp-json' => 'wp-json',
            'hmw_disable_rest_api' => 0,
            'hmw_hide_admin' => 0,
            'hmw_hide_newadmin' => 0,
            'hmw_hide_login' => 0,
            'hmw_hide_plugins' => 0,
            'hmw_hide_themes' => 0,

            //
            'hmw_sqlinjection' => 0,
            'hmw_hide_commonfiles' => 0,
            'hmw_hide_oldpaths' => 0,
            'hmw_disable_browsing' => 0,

            'hmw_category_base' => '',
            'hmw_tag_base' => '',
        );
        self::$lite = array(
            'hmw_mode' => 'lite',
            'hmw_login_url' => 'newlogin',
            'hmw_activate_url' => 'activate',
            'hmw_lostpassword_url' => 'lostpass',
            'hmw_register_url' => 'signup',
            'hmw_logout_url' => '',
            'hmw_admin-ajax_url' => 'admin-ajax.php',
            'hmw_hideajax_admin' => 1,
            'hmw_plugin_url' => 'core/modules',
            'hmw_themes_url' => 'core/assets',
            'hmw_upload_url' => 'storage',
            'hmw_wp-content_url' => 'core',
            'hmw_wp-includes_url' => 'lib',
            'hmw_author_url' => 'writer',
            'hmw_hide_authors' => 0,
            'hmw_wp-comments-post' => 'comments',
            'hmw_themes_style' => 'style.css',
            'hmw_hide_admin' => 1,
            'hmw_hide_newadmin' => 0,
            'hmw_hide_login' => 1,
            'hmw_hide_plugins' => 0,
            'hmw_hide_themes' => 0,
            'hmw_disable_rest_api' => 0,
            'hmw_hide_styleids' => 0,
            //
            'hmw_sqlinjection' => 0,
            'hmw_hide_commonfiles' => 0,
            'hmw_hide_oldpaths' => 0,
            'hmw_disable_browsing' => 0
        );
        self::$ninja = array();

        if (is_multisite() && defined('BLOG_ID_CURRENT_SITE')) {
            $options = json_decode(get_blog_option(BLOG_ID_CURRENT_SITE, $keymeta), true);
        } else {
            $options = json_decode(get_option($keymeta), true);
        }

        if (is_array($options)) {
            $options = @array_merge(self::$init, self::$default, $options);
        } else {
            $options = @array_merge(self::$init, self::$default);
        }

        $category_base = get_option('category_base');
        $tag_base = get_option('tag_base');

        if (is_multisite() && !is_subdomain_install() && is_main_site() && 0 === strpos(get_option('permalink_structure'), '/blog/')) {
            $category_base = preg_replace('|^/?blog|', '', $category_base);
            $tag_base = preg_replace('|^/?blog|', '', $tag_base);
        }

        $options['hmw_category_base'] = $category_base;
        $options['hmw_tag_base'] = $tag_base;


        return $options;
    }

    /**
     * Get the option from database
     * @param $key
     * @return mixed
     */
    public static function getOption($key) {
        if (!isset(self::$options[$key])) {
            self::$options = self::getOptions();

            if (!isset(self::$options[$key])) {
                self::$options[$key] = 0;
            }
        }

        return self::$options[$key];
    }

    /**
     * Save the Options in user option table in DB
     *
     * @param string $key
     * @param string $value
     * @param bool|false $safe
     *
     */
    public static function saveOptions($key = null, $value = '', $safe = false) {
        $keymeta = HMW_OPTION;

        if ($safe) {
            $keymeta = HMW_OPTION_SAFE;
        }

        if (isset($key)) {
            self::$options[$key] = $value;
        }

        if (is_multisite() && defined('BLOG_ID_CURRENT_SITE')) {
            update_blog_option(BLOG_ID_CURRENT_SITE, $keymeta, json_encode(self::$options));
        } else {
            update_option($keymeta, json_encode(self::$options));
        }
    }


    /**
     * Adds extra links to plugin  page
     *
     * @param $meta
     * @param $file
     * @param $data
     * @param $status
     * @return array
     */
    public function hookExtraLinks($meta, $file, $data = null, $status = null) {
        if ($file == _HMW_PLUGIN_NAME_ . '/index.php') {
            echo '<style>
                .ml-stars{display:inline-block;color:#ffb900;position:relative;top:3px}
                .ml-stars svg{fill:#ffb900}
                .ml-stars svg:hover{fill:#ffb900}
                .ml-stars svg:hover ~ svg{fill:none}
            </style>';

            $meta[] = "<a href='https://hidemywp.co/knowledge-base/' target='_blank'>" . __('Documentation', _HMW_PLUGIN_NAME_) . "</a>";
            $meta[] = "<a href='https://wordpress.org/support/plugin/hide-my-wp/reviews/#new-post' target='_blank' title='" . __('Leave a review', _HMW_PLUGIN_NAME_) . "'><i class='ml-stars'><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg><svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg></i></a>";
        }
        return $meta;
    }


    /**
     * Add a link to settings in the plugin list
     *
     * @param array $links
     * @param string $file
     * @return array
     */
    public function hookActionlink($links, $file) {
        if ($file == _HMW_PLUGIN_NAME_ . '/index.php') {
            $link = '<a href="https://hidemywp.co/wordpress_update" title="Hide My WP Ghost" target="_blank" style="color:#11967A; font-weight: bold">' . __('Upgrade to Premium', _HMW_PLUGIN_NAME_) . '</a>';
            $link .= ' | ';
            $link .= '<a href="' . admin_url('admin.php?page=hmw_settings') . '" title="Hide My Wp Settings">' . __('Settings', _HMW_PLUGIN_NAME_) . '</a>';
            array_unshift($links, $link);
        }

        return $links;
    }

    /**
     * Load the multilanguage support from .mo
     */
    public static function loadMultilanguage() {
        if (!defined('WP_PLUGIN_DIR')) {
            load_plugin_textdomain(_HMW_PLUGIN_NAME_, _HMW_PLUGIN_NAME_ . '/languages/');
        } else {
            load_plugin_textdomain(_HMW_PLUGIN_NAME_, null, _HMW_PLUGIN_NAME_ . '/languages/');
        }
    }

    /**
     * Check if it's Ajax call
     * @return bool
     */
    public static function isAjax() {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return true;
        }
        return false;
    }

    /**
     * Get the plugin settings URL
     * @param string $page
     * @return string
     */
    public static function getSettingsUrl($page = 'hmw_settings') {
        if (!is_multisite()) {
            return admin_url('admin.php?page=' . $page);
        } else {
            return network_admin_url('admin.php?page=' . $page);
        }
    }

    /**
     * Set the header type
     * @param string $type
     */
    public static function setHeader($type) {
        switch ($type) {
            case 'json':
                header('Content-Type: application/json');
                break;
            case 'text':
                header("Content-type: text/plain");
                break;
        }
    }

    /**
     * Get a value from $_POST / $_GET
     * if unavailable, take a default value
     *
     * @param string $key Value key
     * @param boolean $keep_newlines Keep the new lines in variable in case of texareas
     * @param mixed $defaultValue (optional)
     * @return mixed Value
     */
    public static function getValue($key = null, $defaultValue = false, $keep_newlines = false) {
        if (!isset($key) || $key == '') {
            return false;
        }

        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));

        if (is_string($ret) === true) {
            if ($keep_newlines === false) {
                if (in_array($key, array('hmw_email_address', 'hmw_email'))) { //validate email address
                    $ret = preg_replace('/[^A-Za-z0-9-_\.\#\/\*\@]/', '', $ret);
                } elseif (in_array($key, array('hmw_disable_name'))) { //validate url parameter
                    $ret = preg_replace('/[^A-Za-z0-9-_]/', '', $ret);
                } else {
                    $ret = preg_replace('/[^A-Za-z0-9-_\/\.]/', '', $ret); //validate fields
                }
                $ret = sanitize_text_field($ret);
            } else {
                $ret = preg_replace('/[^A-Za-z0-9-_.\#\n\r\s\/\* ]\@/', '', $ret);
                if (function_exists('sanitize_textarea_field')) {
                    $ret = sanitize_textarea_field($ret);
                }
            }
        }

        return wp_unslash($ret);
    }

    /**
     * Check if the parameter is set
     *
     * @param string $key
     * @return boolean
     */
    public static function getIsset($key = null) {
        if (!isset($key) || $key == '') {
            return false;
        }

        return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
    }

    /**
     * Show the notices to WP
     *
     * @param $message
     * @param string $type
     * @return string
     */
    public static function showNotices($message, $type = '') {
        if (file_exists(_HMW_THEME_DIR_ . 'Notices.php')) {
            ob_start();
            include(_HMW_THEME_DIR_ . 'Notices.php');
            $message = ob_get_contents();
            ob_end_clean();
        }

        return $message;
    }

    /**
     * Connect remote with wp_remote_get
     *
     * @param $url
     * @param array $params
     * @param array $options
     * @return bool|string
     */
    public static function hmw_remote_get($url, $params = array(), $options = array()) {
        $options['method'] = 'GET';

        $parameters = '';
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if ($key <> '') $parameters .= ($parameters == "" ? "" : "&") . $key . "=" . $value;
            }

            if ($parameters <> '') {
                $url .= ((strpos($url, "?") === false) ? "?" : "&") . $parameters;
            }
        }
        //echo $url; exit();
        if (!$response = self::hmw_wpcall($url, $params, $options)) {
            return false;
        }

        return $response;
    }

    /**
     * Connect remote with wp_remote_get
     *
     * @param $url
     * @param array $params
     * @param array $options
     * @return bool|string
     */
    public static function hmw_remote_post($url, $params = array(), $options = array()) {
        $options['method'] = 'POST';
        if (!$response = self::hmw_wpcall($url, $params, $options)) {
            return false;
        }

        return $response;
    }

    /**
     * Use the WP remote call
     *
     * @param string $url
     * @param array $params
     * @param array $options
     * @return string
     */
    private static function hmw_wpcall($url, $params, $options) {
        $options['timeout'] = (isset($options['timeout'])) ? $options['timeout'] : 30;
        $options['sslverify'] = false;
        $options['httpversion'] = '1.0';

        if ($options['method'] == 'POST') {
            $options['body'] = $params;
            unset($options['method']);
            $response = wp_remote_post($url, $options);
        } else {
            unset($options['method']);
            $response = wp_remote_get($url, $options);
        }
        if (is_wp_error($response)) {
            HMW_Debug::dump($response);
            return false;
        }

        $response = self::cleanResponce(wp_remote_retrieve_body($response)); //clear and get the body
        HMW_Debug::dump('hmw_wpcall', $url, $options, $response); //output debug
        return $response;
    }

    /**
     * Get the Json from responce if any
     * @param string $response
     * @return string
     */
    private static function cleanResponce($response) {
        $response = trim($response, '()');
        return $response;
    }

    /**
     * Returns true if permalink structure
     *
     * @return boolean
     */
    public static function isPermalinkStructure() {
        return get_option('permalink_structure');
    }


    /**
     * Check if HTML Headers to prevent chenging the code for other file extension
     * @return bool
     */
    public static function isHtmlHeader() {
        $headers = headers_list();

        foreach ($headers as $index => $value) {
            if (strpos($value, ':') !== false) {
                $exploded = @explode(': ', $value);
                if (count($exploded) > 1) {
                    $headers[$exploded[0]] = $exploded[1];
                }
            }
        }

        if (isset($headers['Content-Type'])) {
            if (strpos($headers['Content-Type'], 'text/html') !== false ||
                strpos($headers['Content-Type'], 'text/xml') !== false) {
                return true;
            }
        } else {
            return false;
        }

        return false;
    }


    /**
     * Returns true if server is Apache
     *
     * @return boolean
     */
    public static function isApache() {
        global $is_apache;
        return $is_apache;
    }

    /**
     * Check if mode rewrite is on
     * @return bool
     */
    public static function isModeRewrite() {
        if (function_exists('apache_get_modules')) {
            $modules = apache_get_modules();
            if (!empty($modules))
                return in_array('mod_rewrite', $modules);
        }
        return true;
    }

    /**
     * Check whether server is LiteSpeed
     *
     * @return bool
     */
    public static function isLitespeed() {
        return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false);
    }

    /**
     * Check whether server is Lighthttp
     *
     * @return bool
     */
    public static function isLighthttp() {
        return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'lighttpd') !== false);
    }

    /**
     * Check if multisites with path
     *
     * @return bool
     */
    public static function isMultisites() {
        if (!isset(self::$is_multisite)) {
            self::$is_multisite = (is_multisite() && ((defined('SUBDOMAIN_INSTALL') && !SUBDOMAIN_INSTALL) || (defined('VHOST') && VHOST == 'no')));
        }
        return self::$is_multisite;
    }

    /**
     * Returns true if server is nginx
     *
     * @return boolean
     */
    public static function isNginx() {
        global $is_nginx;
        return ($is_nginx || (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false));
    }

    /**
     * Returns true if server is nginx
     *
     * @return boolean
     */
    public static function isWpengine() {
        return (isset($_SERVER['WPENGINE_PHPSESSIONS']));
    }

    public static function isInmotion() {
        return (isset($_SERVER['SERVER_ADDR']) && strpos(@gethostbyaddr($_SERVER['SERVER_ADDR']), 'inmotionhosting.com') !== false);
    }

    /**
     * Returns true if server is IIS
     *
     * @return boolean
     */
    public static function isIIS() {
        global $is_IIS, $is_iis7;
        return ($is_iis7 || $is_IIS || (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'microsoft-iis') !== false));
    }

    /**
     * Returns true if windows
     * @return bool
     */
    public static function isWindows() {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    }

    /**
     * Check if IIS has rewrite 2 structure enabled
     * @return bool
     */
    public static function isPHPPermalink() {
        if (get_option('permalink_structure')) {
            if (strpos(get_option('permalink_structure'), 'index.php') !== false || strpos(get_option('permalink_structure'), 'index.html') !== false || strpos(get_option('permalink_structure'), 'index.htm') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether the plugin is active by checking the active_plugins list.
     *
     * @source wp-admin/includes/plugin.php
     *
     * @param string $plugin Plugin folder/main file.
     *
     * @return boolean
     */
    public static function isPluginActive($plugin) {
        if (empty(self::$active_plugins)) {
            self::$active_plugins = (array)get_option('active_plugins', array());

            if (is_multisite()) {
                self::$active_plugins = array_merge(array_values(self::$active_plugins), array_keys(get_site_option('active_sitewide_plugins')));
            }

            HMW_Debug::dump(self::$active_plugins);

        }
        return in_array($plugin, self::$active_plugins, true);
    }

    /**
     * Check whether the theme is active.
     *
     * @param string $theme Theme folder/main file.
     *
     * @return boolean
     */
    public static function isThemeActive($theme) {
        if (function_exists('wp_get_theme')) {
            $themes = wp_get_theme();
            if (isset($themes->name) && (strtolower($themes->name) == strtolower($theme) || strtolower($themes->name) == strtolower($theme) . ' child')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all the plugin names
     *
     * @return array
     */
    public static function getAllPlugins() {
        $all_plugins = (array)get_option('active_plugins', array());;

        if (is_multisite()) {
            $all_plugins = array_merge(array_values($all_plugins), array_keys(get_site_option('active_sitewide_plugins')));
        }

        return $all_plugins;
    }

    /**
     * Get all the themes names
     *
     * @return array
     */
    public static function getAllThemes() {
        return search_theme_directories();
    }

    /**
     * Get the absolute filesystem path to the root of the WordPress installation
     *
     * @return string Full filesystem path to the root of the WordPress installation
     */
    public static function getRootPath() {
        return ABSPATH;
    }

    /**
     * Get Relative path for the current blog in case of WP Multisite
     * @param $url
     * @return mixed|string
     */
    public static function getRelativePath($url) {
        $url = wp_make_link_relative($url);

        if ($url <> '') {
            $url = str_replace(wp_make_link_relative(get_bloginfo('url')), '', $url);

            if (HMW_Classes_Tools::isMultisites() && defined('PATH_CURRENT_SITE')) {
                $url = str_replace(rtrim(PATH_CURRENT_SITE, '/'), '', $url);
                $url = trim($url, '/');
                $url = $url . '/';
            } else {
                $url = trim($url, '/');
            }
        }

        HMW_Debug::dump($url);
        return $url;
    }

    /**
     * Empty the cache from other cache plugins when save the settings
     */
    public static function emptyCache() {
        if (function_exists('w3tc_pgcache_flush')) {
            w3tc_pgcache_flush();
        }

        if (function_exists('w3tc_minify_flush')) {
            w3tc_minify_flush();
        }
        if (function_exists('w3tc_dbcache_flush')) {
            w3tc_dbcache_flush();
        }
        if (function_exists('w3tc_objectcache_flush')) {
            w3tc_objectcache_flush();
        }

        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }

        if (function_exists('rocket_clean_domain')) {
            // Remove all cache files
            rocket_clean_domain();
        }

        if (function_exists('opcache_reset')) {
            // Remove all opcache if enabled
            opcache_reset();
        }

        if (function_exists('apc_clear_cache')) {
            // Remove all apc if enabled
            apc_clear_cache();
        }

        if (class_exists('Cache_Enabler_Disk') && method_exists('Cache_Enabler_Disk', 'clear_cache')) {
            // clear disk cache
            Cache_Enabler_Disk::clear_cache();
        }

        //Clear the fastest cache
        global $wp_fastest_cache;
        if (isset($wp_fastest_cache) && method_exists($wp_fastest_cache, 'deleteCache')) {
            $wp_fastest_cache->deleteCache();
        }
    }

    /**
     * Flush the WordPress rewrites
     */
    public static function flushWPRewrites() {
        if (HMW_Classes_Tools::isPluginActive('woocommerce/woocommerce.php')) {
            update_option('woocommerce_queue_flush_rewrite_rules', 'yes');
        }

        flush_rewrite_rules();
    }

    /**
     * Called on plugin activation
     */
    public function hmw_activate() {
        set_transient('hmw_activate', true);

        $lastsafeoptions = self::getOptions(true);
        if (isset($lastsafeoptions['hmw_mode']) && ($lastsafeoptions['hmw_mode'] == 'ninja' || $lastsafeoptions['hmw_mode'] == 'lite')) {
            set_transient('hmw_restore', true);
        }

        self::$options = @array_merge(self::$init, self::$default);
        self::$options['hmw_ver'] = HMW_VERSION_ID;
        self::saveOptions();
    }

    /**
     * Called on plugin deactivation
     */
    public function hmw_deactivate() {
        $options = self::$default;
        //Prevent duplicates
        foreach ($options as $key => $value) {
            //set the default params from tools
            HMW_Classes_Tools::saveOptions($key, $value);
        }

        //clear the locked ips
        HMW_Classes_ObjController::getClass('HMW_Controllers_Brute')->clearBlockedIPs();
    }

    /**
     * Check for updates
     * Called on activation
     */
    public static function checkUpgrade() {
        self::$options = self::getOptions();

        //get the options from the free version
        if (get_option('hmu_options')) {
            $options = json_decode(get_option('hmu_options'));
            self::saveOptions('hmw_disable', $options->hmu_disable);
            self::saveOptions('hmw_mode', 'lite');
            self::saveOptions('hmw_admin_url', $options->hmu_admin_url);
            self::saveOptions('hmw_login_url', $options->hmu_login_url);
            self::saveOptions('hmw_hide_admin', $options->hmu_hide_admin);
            self::saveOptions('hmw_hide_login', $options->hmu_hide_login);


            $homepath = ltrim(parse_url(site_url(), PHP_URL_PATH), '/');
            $pluginurl = ltrim(parse_url(plugins_url(), PHP_URL_PATH), '/');
            $contenturl = ltrim(parse_url(content_url(), PHP_URL_PATH), '/');
            self::$options['hmw_plugin_url'] = trim(str_replace($homepath, '', $pluginurl), '/');
            self::$options['hmw_wp-content_url'] = trim(str_replace($homepath, '', $contenturl), '/');
            self::$options['hmw_themes_url'] = 'themes';

            HMW_Classes_ObjController::getClass('HMW_Models_Rules')->removeConfigCookie();
            delete_option('hmu_options');
        }
    }

    /**
     * Check if new themes or plugins are added
     */
    public function checkWpUpdates() {
        $all_plugins = HMW_Classes_Tools::getAllPlugins();
        $dbplugins = HMW_Classes_Tools::getOption('hmw_plugins');
        foreach ($all_plugins as $plugin) {
            if (is_plugin_active($plugin) && isset($dbplugins['from']) && !empty($dbplugins['from'])) {
                if (!in_array(plugin_dir_path($plugin), $dbplugins['from'])) {
                    self::saveOptions('changes', true);
                }
            }
        }

        $all_themes = HMW_Classes_Tools::getAllThemes();
        $dbthemes = HMW_Classes_Tools::getOption('hmw_themes');
        foreach ($all_themes as $theme => $value) {
            if (is_dir($value['theme_root']) && isset($dbthemes['from']) && !empty($dbthemes['from'])) {
                if (!in_array($theme . '/', $dbthemes['from'])) {
                    self::saveOptions('changes', true);
                }
            }
        }

    }

    /**
     * Call API Server
     * @param null $email
     * @param string $redirect_to
     * @return array|bool|mixed|object
     */
    public static function checkApi($email = null, $redirect_to = '') {
        $check = array();
        $howtolessons = HMW_Classes_Tools::getValue('hmw_howtolessons', 0);
        if (isset($email) && $email <> '') {
            $args = array('email' => $email, 'url' => home_url(), 'howtolessons' => (int)$howtolessons, 'source' => _HMW_PLUGIN_NAME_);
            $response = self::hmw_remote_get(_HMW_API_SITE_ . '/api/free/token', $args, array('timeout' => 10));
        } elseif (self::getOption('hmw_token')) {
            $args = array('token' => self::getOption('hmw_token'), 'url' => home_url(), 'howtolessons' => (int)$howtolessons, 'source' => _HMW_PLUGIN_NAME_);
            $response = self::hmw_remote_get(_HMW_API_SITE_ . '/api/free/token', $args, array('timeout' => 10));
        } else {
            return $check;
        }
        if ($response && json_decode($response)) {
            $check = json_decode($response, true);

            HMW_Classes_Tools::saveOptions('hmw_token', (isset($check['token']) ? $check['token'] : 0));
            HMW_Classes_Tools::saveOptions('api_token', (isset($check['api_token']) ? $check['api_token'] : false));
            HMW_Classes_Tools::saveOptions('error', isset($check['error']));

            if (!isset($check['error'])) {
                if ($redirect_to <> '') {
                    wp_redirect($redirect_to);
                    exit();
                }
            } elseif (isset($check['message'])) {
                HMW_Classes_Error::setError($check['message']);
            }
        } else {
            //HMW_Classes_Tools::saveOptions('error', true);
            HMW_Classes_Error::setError(sprintf(__('CONNECTION ERROR! Make sure your website can access: %s', _HMW_PLUGIN_NAME_), '<a href="' . _HMW_SUPPORT_SITE_ . '" target="_blank">' . _HMW_SUPPORT_SITE_ . '</a>') . " <br /> ");
        }

        return $check;
    }

    /**
     * Send the email is case there are major changes
     * @return bool
     */
    public static function sendEmail() {
        $email = self::getOption('hmw_email_address');
        if ($email == '') {
            global $current_user;
            $email = $current_user->user_email;
        }

        $line = "\n" . "________________________________________" . "\n";
        $to = $email;
        $from = 'support@wpplugins.tips';
        $subject = __('Hide My WP - New Login Data', _HMW_PLUGIN_NAME_);
        $message = "Thank you for using Hide My WordPress!" . "\n";
        $message .= $line;
        $message .= "Your new site URLs are:" . "\n";
        $message .= "Admin URL: " . admin_url() . "\n";
        $message .= "Login URL: " . site_url(self::$options['hmw_login_url']) . "\n";
        $message .= $line;
        $message .= "Note: If you can't login to your site, just access this URL: \n";
        $message .= site_url() . "/wp-login.php?" . self::getOption('hmw_disable_name') . "=" . self::$options['hmw_disable'] . "\n\n";
        $message .= $line;
        $message .= "SPECIAL OFFER: 65% Discount! Use coupon 5HIDEMYWP65 for Hide My WP Ghost 5 Websites License" . "\n";
        $message .= "https://wpplugins.tips/wordpress" . "\n\n\n";
        $message .= "Best regards," . "\n";
        $message .= "Wpplugins.tips Team" . "\n";

        $headers = array();
        $headers[] = 'From: Hide My WP <' . $from . '>';
        $headers[] = 'Content-type: text/plain';

        add_filter('wp_mail_content_type', array('HMW_Classes_Tools', 'setContentType'));

        if (@wp_mail($to, $subject, $message, $headers)) {
            return true;
        }

        return false;
    }

    /**
     * Set the content type to text/plain
     * @return string
     */
    public static function setContentType() {
        return "text/plain";
    }

    /**
     * Return false on hooks
     * @param string $param
     * @return bool
     */
    public static function returnFalse($param = null) {
        return false;
    }

    /**
     * Return true on hooks
     * @param string $param
     * @return bool
     */
    public static function returnTrue($param = null) {
        return true;
    }

}