<?php if (HMW_Classes_Tools::isPermalinkStructure()) { ?>
    <div id="hmw_wrap" class="d-flex flex-row my-3 bg-light">
        <?php echo $view->getAdminTabs(HMW_Classes_Tools::getValue('tab', 'hmw_permalinks')); ?>
        <div class="hmw_row d-flex flex-row bg-white px-3">
            <div class="hmw_col flex-grow-1 mr-3">
                <form method="POST">
                    <?php wp_nonce_field('hmw_advsettings', 'hmw_nonce') ?>
                    <input type="hidden" name="action" value="hmw_advsettings"/>

                    <div class="card p-0 col-sm-12 tab-panel">
                        <h3 class="card-title bg-brown text-white p-2"><?php _e('Redirect Settings', _HMW_PLUGIN_NAME_); ?>:</h3>
                        <div class="card-body">
                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-sm-4 p-1">
                                    <div class="font-weight-bold"><?php _e('Redirect hidden paths', _HMW_PLUGIN_NAME_); ?>:</div>
                                </div>
                                <div class="col-sm-8 p-0 input-group">
                                    <select name="hmw_url_redirect" class="form-control bg-input mb-1">
                                        <option value="." <?php selected('.', HMW_Classes_Tools::getOption('hmw_url_redirect'), true) ?>><?php _e("Front page", _HMW_PLUGIN_NAME_) ?></option>
                                        <option value="404" <?php selected('404', HMW_Classes_Tools::getOption('hmw_url_redirect'), true) ?> ><?php _e("404 page", _HMW_PLUGIN_NAME_) ?></option>
                                        <?php
                                        $pages = get_pages();
                                        foreach ($pages as $page) {
                                            $option = '<option value="' . $page->post_name . '" ' . selected($page->post_name, HMW_Classes_Tools::getOption('hmw_url_redirect'), true) . '>';
                                            $option .= $page->post_title;
                                            $option .= '</option>';
                                            echo $option;
                                        } ?>
                                    </select>
                                </div>
                                <div class="p-1">
                                    <div class="text-black-50"><?php echo __('Redirect the protected paths /wp-admin, /wp-login to Front Page or 404 page.', _HMW_PLUGIN_NAME_); ?></div>
                                    <div class="text-black-50"><?php echo __('You can create a new page and come back to choose to redirect to that page', _HMW_PLUGIN_NAME_); ?></div>
                                </div>
                            </div>

                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-sm-4 p-0 font-weight-bold">
                                    <?php _e('Custom Safe URL Param', _HMW_PLUGIN_NAME_); ?>:
                                    <div class="small text-black-50"><?php _e("eg. disable_url, safe_url", _HMW_PLUGIN_NAME_); ?></div>
                                </div>
                                <div class="col-sm-8 p-0 input-group">
                                    <input type="text" class="form-control bg-input" name="hmw_disable_name" value="<?php echo HMW_Classes_Tools::getOption('hmw_disable_name') ?>" placeholder="<?php echo HMW_Classes_Tools::getOption('hmw_disable_name') ?>"/>
                                </div>
                                <div class="col-sm-12 pt-4">
                                    <div class="small text-black-50 text-center"><?php _e("The Safe URL will set all the settings to default. Use it only if you're locked out", _HMW_PLUGIN_NAME_); ?></div>
                                    <div class="text-danger text-center"><?php echo '<strong>' . __("Safe URL:", _HMW_PLUGIN_NAME_) . '</strong>' . ' ' . site_url() . "/wp-login.php?" . HMW_Classes_Tools::getOption('hmw_disable_name') . "=" . HMW_Classes_Tools::getOption('hmw_disable') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card p-0 col-sm-12 tab-panel">
                        <h3 class="card-title bg-brown text-white p-2"><?php _e('Advanced Settings', _HMW_PLUGIN_NAME_); ?>:</h3>
                        <div class="card-body">


                            <div class="col-sm-12 row mb-1 ml-2">
                                <div class="checker col-sm-12 row my-2 py-1">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmw_fix_relative" value="0"/>
                                        <input type="checkbox" id="hmw_fix_relative" name="hmw_fix_relative" class="switch" <?php echo(HMW_Classes_Tools::getOption('hmw_fix_relative') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmw_fix_relative"><?php _e('Fix relative URLs', _HMW_PLUGIN_NAME_); ?></label>
                                        <div class="offset-1 text-black-50"><?php echo sprintf(__('Convert all the links  /wp-content/uploads.. into  %s/wp-content/uploads.. to be able to hide those links in frontend ', _HMW_PLUGIN_NAME_), site_url()); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-1 ml-2">
                                <div class="checker col-sm-12 row my-2 py-1">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmw_remove_third_hooks" value="0"/>
                                        <input type="checkbox" id="hmw_remove_third_hooks" name="hmw_remove_third_hooks" class="switch" <?php echo(HMW_Classes_Tools::getOption('hmw_remove_third_hooks') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmw_remove_third_hooks"><?php _e('Strict Login', _HMW_PLUGIN_NAME_); ?></label>
                                        <div class="offset-1 text-black-50"><?php _e('Cancel the login hooks from other plugins and themes to prevent them from changing the Hide My WordPress redirects.', _HMW_PLUGIN_NAME_); ?><?php _e('(not recommended)', _HMW_PLUGIN_NAME_); ?></div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-12 row mb-1 ml-2">
                                <div class="checker col-sm-12 row my-2 py-1">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmw_laterload" value="0"/>
                                        <input type="checkbox" id="hmw_laterload" name="hmw_laterload" class="switch" <?php echo(HMW_Classes_Tools::getOption('hmw_laterload') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmw_laterload"><?php _e('Late Loading', _HMW_PLUGIN_NAME_); ?></label>
                                        <div class="offset-1 text-black-50"><?php echo __('Load HMW after all plugins are loaded. Useful for CDN plugins (eg. CDN Enabler)', _HMW_PLUGIN_NAME_); ?></div>
                                        <div class="offset-1 text-black-50"><?php echo __('(only if other cache plugins request this)', _HMW_PLUGIN_NAME_); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-1 ml-1">
                                <div class="checker col-sm-12 row my-2 py-1">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmw_security_alert" value="0"/>
                                        <input type="checkbox" id="hmw_security_alert" name="hmw_security_alert" class="switch" <?php echo(HMW_Classes_Tools::getOption('hmw_security_alert') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmw_security_alert"><?php _e('Security Check Notification', _HMW_PLUGIN_NAME_); ?></label>
                                        <div class="offset-1 text-black-50"><?php echo __("Show Security Check notification when it's not checked every week.", _HMW_PLUGIN_NAME_); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-1 ml-1">
                                <div class="checker col-sm-12 row my-2 py-1">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmw_file_cache" value="0"/>
                                        <input type="checkbox" id="hmw_file_cache" name="hmw_file_cache" class="switch" <?php echo(HMW_Classes_Tools::getOption('hmw_file_cache') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmw_file_cache"><?php _e('Optimize CSS and JS files', _HMW_PLUGIN_NAME_); ?></label>
                                        <div class="offset-1 text-black-50"><?php echo __('Cache CSS, JS and Images to increase the frontend loading speed.', _HMW_PLUGIN_NAME_); ?></div>
                                        <div class="offset-1 text-black-50"><?php echo sprintf(__('Check the website loading speed with %sPingdom Tool%s', _HMW_PLUGIN_NAME_), '<a href="https://tools.pingdom.com/" target="_blank">', '</a>'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card p-0 col-sm-12 tab-panel">
                        <h3 class="card-title bg-brown text-white p-2"><?php _e('Pattern Settings', _HMW_PLUGIN_NAME_); ?>:</h3>
                        <div class="card-body">
                            <?php if (HMW_Classes_Tools::getOption('hmw_mode') == 'default') { ?>
                                <div class="col-sm-12 border-bottom border-light py-3 mx-0 my-3 text-black-50 text-center">
                                    <?php echo __('First, you need to switch Hide My Wp from Default mode to Safe Mode or Ghost Mode.', _HMW_PLUGIN_NAME_) ?>
                                </div>
                            <?php } else { ?>
                                <div class="col-sm-12 row border-bottom border-light py-3 mx-0 ">
                                    <div class="p-0">
                                        <h4><?php _e('Replace text by matching', _HMW_PLUGIN_NAME_); ?>:</h4>
                                        <div class="text-black-50"><?php _e("Note: Replace carefully. Your plugins and themes may use these and it will affect the design and functionality.", _HMW_PLUGIN_NAME_); ?></div>
                                    </div>

                                </div>


                                <div class="hmw_text_mapping_group py-3">
                                    <?php
                                    $hmw_text_mapping = json_decode(HMW_Classes_Tools::getOption('hmw_text_mapping'), true);
                                    if (isset($hmw_text_mapping['from']) && !empty($hmw_text_mapping['from'])) {
                                        foreach ($hmw_text_mapping['from'] as $index => $row) {
                                            ?>
                                            <div class="col-sm-12 hmw_text_mapping row border-bottom border-light py-1 px-0 mx-0 my-0">
                                                <div class="hmw_text_mapping_remove" onclick="jQuery(this).parent().remove()" title="<?php echo __('Remove Text Map', _HMW_PLUGIN_NAME_) ?>">x</div>
                                                <div class="col-sm-6 py-1 px-0 input-group input-group">
                                                    <input type="text" class="form-control bg-input" name="hmw_text_mapping_from[]" value="<?php echo $hmw_text_mapping['from'][$index] ?>" placeholder="Current Text ..."/>
                                                    <div class="col-sm-1 py-2 px-0 text-center text-black-50" style="max-width: 30px"><?php echo '=>' ?></div>
                                                </div>
                                                <div class="col-sm-6 py-1 px-0 input-group input-group">
                                                    <input type="text" class="form-control bg-input" name="hmw_text_mapping_to[]" value="<?php echo $hmw_text_mapping['to'][$index] ?>" placeholder="New Text ..."/>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } ?>
                                    <div class="col-sm-12 hmw_text_mapping row border-bottom border-light py-1 px-0 mx-0 my-0">
                                        <div class="hmw_text_mapping_remove" style="display: none" onclick="jQuery(this).parent().remove()" title="<?php echo __('Remove Text Map', _HMW_PLUGIN_NAME_) ?>">x</div>
                                        <div class="col-sm-6 py-1 px-0 input-group input-group">
                                            <input type="text" class="form-control bg-input" name="hmw_text_mapping_from[]" value="" placeholder="Current Text ..."/>
                                            <div class="col-sm-1 py-2 px-0 text-center text-black-50" style="max-width: 30px"><?php echo '=>' ?></div>
                                        </div>
                                        <div class="col-sm-6 py-1 px-0 input-group input-group">
                                            <input type="text" class="form-control bg-input" name="hmw_text_mapping_to[]" value="" placeholder="New Text ..."/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 row border-bottom border-light p-0 m-0">
                                    <div class="col-sm-2 p-0 offset-5">
                                        <button type="button" class="col-sm-12 btn btn-sm btn-warning text-white" onclick="jQuery('div.hmw_text_mapping:last').clone().appendTo('div.hmw_text_mapping_group'); jQuery('div.hmw_text_mapping_remove').show(); jQuery('div.hmw_text_mapping:last').find('div.hmw_text_mapping_remove').hide()"><?php echo __('Add new text', _HMW_PLUGIN_NAME_) ?></button>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>
                    <div class="card p-0 col-sm-12 tab-panel">
                        <h3 class="card-title bg-brown text-white p-2"><?php _e('URL Mapping', _HMW_PLUGIN_NAME_); ?>:</h3>
                        <div class="card-body">
                            <div class="box" data-toggle="popover" data-html="true" data-placement="top" data-content="<?php echo sprintf(__('This feature requires %sHide My WP Ghost%s.', _HMW_PLUGIN_NAME_), "<a href='http://hidemywp.co/' target='_blank'>", "</a>") ?>">
                                <div class="ribbon"><span><?php echo __('PRO', _HMW_PLUGIN_NAME_) ?></span></div>
                            </div>
                            <div style="opacity: 0.3">
                                <div class="text-black-50"><?php echo __("You can add a list of URLs you want to change into new ones. ", _HMW_PLUGIN_NAME_); ?></div>
                                <div class="text-black-50"><?php echo __("It's important to include only internal URLs from Frontend source code after you activate the plugin in Safe Mode or Ghost Mode.", _HMW_PLUGIN_NAME_); ?></div>
                                <div class="text-black-50 mt-4 font-weight-bold"><?php echo __("Example:", _HMW_PLUGIN_NAME_); ?></div>
                                <div class="text-black-50 row">
                                    <div class="col-sm-1 font-weight-bold mr-0 pr-0"><?php echo __('from', _HMW_PLUGIN_NAME_) ?>:</div>
                                    <div class="col-sm-10 m-0 p-0"><?php echo site_url() . '/' . HMW_Classes_Tools::getOption('hmw_themes_url') . '/' . substr(md5(str_replace('%2F', '/', rawurlencode(get_template()))), 0, 10) . '/' . HMW_Classes_Tools::getOption('hmw_themes_style'); ?></div>
                                </div>
                                <div class="text-black-50 row">
                                    <div class="col-sm-1 font-weight-bold mr-0 pr-0"><?php echo __('to', _HMW_PLUGIN_NAME_) ?>:</div>
                                    <div class="col-sm-10 m-0 p-0"><?php echo site_url('mystyle.css'); ?></div>
                                </div>
                                <div class="hmw_url_mapping_group py-3">
                                    <div class="col-sm-12 hmw_url_mapping row border-bottom border-light py-1 px-0 mx-0 my-0">
                                        <div class="col-sm-6 py-1 px-0 input-group input-group">
                                            <input type="text" class="form-control bg-input" name="hmw_url_mapping_from[]" value="" placeholder="Current URL ..."/>
                                            <div class="col-sm-1 py-2 px-0 text-center text-black-50" style="max-width: 30px"><?php echo '=>' ?></div>
                                        </div>
                                        <div class="col-sm-6 py-1 px-0 input-group input-group">
                                            <input type="text" class="form-control bg-input" name="hmw_url_mapping_to[]" value="" placeholder="New URL ..."/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card p-0 col-sm-12 tab-panel">
                        <h3 class="card-title bg-brown text-white p-2"><?php _e('Notification Settings', _HMW_PLUGIN_NAME_); ?>:</h3>
                        <div class="card-body">

                            <div class="col-sm-12 row mb-1 ml-2">
                                <div class="checker col-sm-12 row my-2 py-1">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmw_send_email" value="0"/>
                                        <input type="checkbox" id="hmw_send_email" name="hmw_send_email" class="switch" <?php echo(HMW_Classes_Tools::getOption('hmw_send_email') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmw_send_email"><?php _e('Email notification', _HMW_PLUGIN_NAME_); ?></label>
                                        <div class="offset-1 text-black-50"><?php _e('Send me an email with the changed admin and login URLs', _HMW_PLUGIN_NAME_); ?></div>
                                    </div>
                                </div>


                            </div>

                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-sm-4 p-1 font-weight-bold">
                                    <?php _e('Email Address', _HMW_PLUGIN_NAME_); ?>:
                                </div>
                                <div class="col-sm-8 p-0 input-group input-group">
                                    <?php
                                    $email = HMW_Classes_Tools::getOption('hmw_email_address');
                                    if ($email == '') {
                                        global $current_user;
                                        $email = $current_user->user_email;
                                    }
                                    ?>
                                    <input type="text" class="form-control bg-input" name="hmw_email_address" value="<?php echo $email ?>" placeholder="Email address ..."/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 my-3 p-0">
                        <button type="submit" class="btn rounded-0 btn-success btn-lg px-5 save"><?php _e('Save', _HMW_PLUGIN_NAME_); ?></button>
                        <a href="https://wordpress.org/support/plugin/hide-wp-urls/reviews/#new-post" target="_blank" class="btn rounded-0 btn-link btn-sm "><img src="<?php echo _HMW_THEME_URL_ ?>img/support_hide_my_wp.png" style="width: 100%; max-width: 550px"></a>
                    </div>
                </form>
            </div>
            <div class="hmw_col hmw_col_side">
                <div class="card col-sm-12 p-0">
                    <div class="card-body f-gray-dark text-center">
                        <h3 class="card-title"><?php echo __('Love Hide My WP?', _HMW_PLUGIN_NAME_); ?></h3>
                        <div class="card-text text-muted">
                            <h1>
                                <a href="https://wordpress.org/support/plugin/hide-wp-urls/reviews/#new-post" target="_blank" style="font-size: 80px"><i class="fa fa-heart text-danger"></i></a>
                            </h1>
                            <?php echo __('Please help us and support our plugin on WordPress.org', _HMW_PLUGIN_NAME_) ?>
                        </div>
                        <div class="card-text text-info m-3">
                            <a href="https://wordpress.org/support/plugin/hide-wp-urls/reviews/#new-post" target="_blank" class="btn rounded-0 btn-success btn-lg px-4"><?php echo __('Rate Hide My WP', _HMW_PLUGIN_NAME_); ?></a>
                        </div>
                        <div class="card-text text-muted">
                            <?php echo __('Contact us after you left the review cause we have a surprise for you.', _HMW_PLUGIN_NAME_) ?>
                            <h1>
                                <a href="https://hidemywp.co/contact/" target="_blank" style="font-size: 80px"><i class="fa fa-gift text-info"></i></a>
                            </h1>
                        </div>
                    </div>
                </div>

                <div class="hmw_col hmw_col_side">
                    <div class="card col-sm-12 p-0">
                        <div class="card-body f-gray-dark text-center">
                            <h3 class="card-title"><?php _e('Check Your Website', _HMW_PLUGIN_NAME_); ?></h3>
                            <div class="card-text text-muted">
                                <?php echo __('Check if your website is secured with the current settings.', _HMW_PLUGIN_NAME_) ?>
                            </div>
                            <div class="card-text text-info m-3">
                                <a href="<?php echo HMW_Classes_Tools::getSettingsUrl('hmw_securitycheck') ?>" class="btn rounded-0 btn-warning btn-lg text-white px-5 securitycheck"><?php _e('Security Check', _HMW_PLUGIN_NAME_); ?></a>
                            </div>
                            <div class="card-text text-muted small">
                                <?php echo __('Make sure you save the settings and empty the cache before checking your website with our tool.', _HMW_PLUGIN_NAME_) ?>
                            </div>

                            <div class="card-text m-3 ">
                                <a class="bigbutton text-center" href="http://hidemywp.co/knowledge-base/" target="_blank"><?php echo __("Learn more about Hide My WP", _HMW_PLUGIN_NAME_); ?></a>
                            </div>
                        </div>
                    </div>



                    <?php echo $view->getView('Support') ?>

                </div>
            </div>
        </div>
    </div>
<?php }