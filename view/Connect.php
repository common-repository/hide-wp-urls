<div id="hmw_wrap" class="d-flex flex-row my-3 bg-light">
    <?php echo $view->getAdminTabs(HMW_Classes_Tools::getValue('tab', 'hmw_permalinks')); ?>
    <div class="hmw_row d-flex flex-row bg-white px-3">
        <?php do_action('hmw_notices'); ?>
        <div class="hmw_col flex-grow-1 mr-3">
            <form method="POST">
                <?php wp_nonce_field('hmw_connect', 'hmw_nonce') ?>
                <input type="hidden" name="action" value="hmw_connect"/>

                <?php do_action('hmw_form_notices'); ?>
                <div class="card p-0 col-sm-12 tab-panel">
                    <h3 class="card-title bg-brown text-white p-2"><?php _e('Activate the Free version', _HMW_PLUGIN_NAME_); ?></h3>
                    <div class="card-body">

                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-sm-4 p-1 font-weight-bold">
                                <?php _e('Email Address', _HMW_PLUGIN_NAME_); ?>:
                                <div class="small text-black-50"><?php echo __('Enter your email address, connect to WPPlugins and get a free Token', _HMW_PLUGIN_NAME_); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group ">
                                <?php
                                $email = HMW_Classes_Tools::getOption('hmw_email_address');
                                if ($email == '') {
                                    global $current_user;
                                    $email = $current_user->user_email;
                                }
                                ?>
                                <input type="text" class="form-control" name="hmw_email" value="<?php echo $email ?>" placeholder="<?php echo $email ?>"/>
                            </div>
                        </div>

                        <div class="col-sm-12 row mb-1 ml-1 hmw_howtolessons_div">
                            <div class="checker col-sm-12 row my-2 py-1">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmw_howtolessons" value="0"/>
                                    <input type="checkbox" id="hmw_howtolessons" name="hmw_howtolessons" class="switch" checked="checked" value="1"/>
                                    <label for="hmw_howtolessons"><?php _e('Setup help by email', _HMW_PLUGIN_NAME_); ?></label>
                                    <div class="offset-1 text-black-50"><?php _e('I agree to receive How To email lessons to help me setup the plugin for my website.', _HMW_PLUGIN_NAME_); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 border-bottom border-light py-3 mx-0 my-3 text-right">
                            <button type="button" class="btn btn-link text-info" onclick="jQuery('#hmw_token_div').toggle();"><?php _e("I have a token already", _HMW_PLUGIN_NAME_); ?></button>
                        </div>
                        <div id="hmw_token_div" class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3" style="display: none">
                            <div class="col-sm-4 p-1 font-weight-bold">
                                <?php _e('Enter the Token', _HMW_PLUGIN_NAME_); ?>:
                                <div class="small text-black-50"><?php echo sprintf(__('Enter the Token from your %sWPPlugins account%s ', _HMW_PLUGIN_NAME_), '<a href="' . _HMW_ACCOUNT_SITE_ . '/user/auth/licenses" target="_blank">', '</a>'); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group">
                                <input type="text" class="form-control" name="hmw_token" value=""/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 my-3 p-0">
                    <button type="submit" class="btn rounded-0 btn-success btn-lg px-5 save"><?php _e('Activate', _HMW_PLUGIN_NAME_); ?></button>
                </div>
            </form>

            <div class="card col-sm-12 p-3 tab-panel_tutorial embed-responsive embed-responsive-16by9 text-center">
                <iframe width="853" height="480" style="max-width: 100%" src="https://www.youtube.com/embed/zhvRGHMjKic" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        </div>
        <div class="hmw_col hmw_col_side">
            <div class="card col-sm-12 p-0">
                <div class="card-body f-gray-dark text-left border-bottom">
                    <h3 class="card-title"><?php _e('Activate Hide My WP', _HMW_PLUGIN_NAME_); ?></h3>
                    <div class="text-info">
                        <?php echo sprintf(__("By activating the Free version of Hide My WP you agree with our %sTerms of Use%s and %sPrivacy Policy%s", _HMW_PLUGIN_NAME_), '<a href="https://wpplugins.tips/terms-of-use/" target="_blank">', '</a>', '<a href="https://wpplugins.tips/privacy-policy/" target="_blank">', '</a>'); ?>
                    </div>
                    <div class="text-info mt-3">
                        <?php echo __('Note! If you add your email you will receive a free token which will activate the plugin.', _HMW_PLUGIN_NAME_); ?>
                    </div>

                    <div class="text-danger mt-2">
                        <?php echo sprintf(__("If you bought Hide My WP Ghost please remove this plugin and install the one from %sYour Account%s", _HMW_PLUGIN_NAME_), '<a href="https://account.wpplugins.tips/user/" target="_blank">', '</a>'); ?>
                    </div>
                </div>
            </div>

            <?php echo $view->getView('Support') ?>

        </div>
    </div>

</div>