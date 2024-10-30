<?php
defined('ABSPATH') || die('Cheatin\' uh?');

class HMW_Controllers_Menu extends HMW_Classes_FrontController {

    public $alert = '';

    /**
     * Hook the Admin load
     */
    public function hookInit() {
        /* add the plugin menu in admin */
        if (current_user_can('manage_options')) {
            //check if activated
            if (get_transient('hmw_activate') == 1) {
                // Delete the redirect transient
                delete_transient('hmw_activate');

                $redirect_to = HMW_Classes_Tools::getSettingsUrl();
                //wp_validate_redirect($redirect_to);
                wp_redirect($redirect_to);
                exit();
            }

            //Check if there are expected upgrades
            HMW_Classes_Tools::checkUpgrade();

            //Load notice class in admin
            HMW_Classes_ObjController::getClass('HMW_Controllers_Notice');

            //Show Admin Toolbar
            add_action('admin_bar_menu', array($this, 'hookTopmenu'), 999);

            //Set the alert if security wasn't check
            if(HMW_Classes_Tools::getOption('hmw_security_alert')) {
                if (!get_option('hmw_securitycheck')) {
                    $this->alert = " <span class='awaiting-mod count-errors_count' style='display: inline-block; vertical-align: middle; margin: -2px 0 0 2px; padding: 0 5px; min-width: 8px; height: 18px; border-radius: 11px; background-color: #ca4a1f; color: #fff; font-size: 9px; line-height: 18px; text-align: center;'> <span class='sq_count pending-count' style='line-height: 17px;font-size: 11px;'>1</span> </span>";
                } elseif ($securitycheck_time = get_option('hmw_securitycheck_time')) {
                    if (isset($securitycheck_time['timestamp']) && time() - $securitycheck_time['timestamp'] > (3600 * 24 * 7)) {
                        $this->alert = " <span class='awaiting-mod count-errors_count' style='display: inline-block; vertical-align: middle; margin: -2px 0 0 2px; padding: 0 5px; min-width: 8px; height: 18px; border-radius: 11px; background-color: #ca4a1f; color: #fff; font-size: 9px; line-height: 18px; text-align: center;'> <span class='sq_count pending-count' style='line-height: 17px;font-size: 11px;'>1</span> </span>";
                    }
                }
            }
        }

    }

    /**
     * Creates the Setting menu in WordPress
     */
    public function hookMenu() {
        if (current_user_can('manage_options')) {
            $this->model->addMenu(array(ucfirst(_HMW_PLUGIN_NAME_),
                'Hide My WP' . $this->alert,
                'manage_options',
                'hmw_settings',
                null,
                _HMW_THEME_URL_ . 'img/logo_16.png'
            ));

            /* add the plugin menu in admin */
            $this->model->addSubmenu(array('hmw_settings',
                __('Hide My WP Settings', _HMW_PLUGIN_NAME_),
                __('Hide My WP', _HMW_PLUGIN_NAME_),
                'manage_options',
                'hmw_settings',
                array(HMW_Classes_ObjController::getClass('HMW_Controllers_Settings'), 'init')
            ));

            /* add the plugin menu in admin */
            $this->model->addSubmenu(array('hmw_settings',
                __('Hide My WP Security Check', _HMW_PLUGIN_NAME_),
                __('Security Check', _HMW_PLUGIN_NAME_) . $this->alert,
                'manage_options',
                'hmw_securitycheck',
                array(HMW_Classes_ObjController::getClass('HMW_Controllers_SecurityCheck'), 'show')
            ));
        }

    }

    /**
     * Add a menu in Admin Bar
     *
     * @param WP_Admin_Bar $wp_admin_bar
     */
    public function hookTopmenu($wp_admin_bar) {
        if(HMW_Classes_Tools::getOption('hmw_security_alert')) {
            $wp_admin_bar->add_node(array(
                'id' => 'hmw_securitycheck',
                'title' => '<img src="' . _HMW_THEME_URL_ . 'img/logo_16.png' . '" style="height: 15px; vertical-align: text-bottom; display: inline-block; margin-right: 3px;" />' . __('Security Check', _HMW_PLUGIN_NAME_) . $this->alert,
                'href' => HMW_Classes_Tools::getSettingsUrl('hmw_securitycheck'),
                'parent' => false
            ));
        }
    }

    /**
     * Creates the Setting menu in Multisite WordPress
     */
    public function hookMultisiteMenu() {

        $this->model->addMenu(array(ucfirst(_HMW_PLUGIN_NAME_),
            'Hide My WP' . $this->alert,
            'manage_options',
            'hmw_settings',
            null,
            _HMW_THEME_URL_ . 'img/logo_16.png'
        ));

        /* add the plugin menu in admin */
        $this->model->addSubmenu(array('hmw_settings',
            __('Hide My WP Settings', _HMW_PLUGIN_NAME_),
            __('Hide My WP', _HMW_PLUGIN_NAME_),
            'manage_options',
            'hmw_settings',
            array(HMW_Classes_ObjController::getClass('HMW_Controllers_Settings'), 'init')
        ));

        /* add the plugin menu in admin */
        $this->model->addSubmenu(array('hmw_settings',
            __('Hide My WP Security Check', _HMW_PLUGIN_NAME_),
            __('Security Check', _HMW_PLUGIN_NAME_) . $this->alert,
            'manage_options',
            'hmw_securitycheck',
            array(HMW_Classes_ObjController::getClass('HMW_Controllers_SecurityCheck'), 'show')
        ));
    }
}