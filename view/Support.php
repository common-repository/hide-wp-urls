<div class="card col-sm-12 p-0">
    <div class="card-body f-gray-dark text-center">
        <h3 class="card-title"><?php _e('Need Help?', _HMW_PLUGIN_NAME_); ?></h3>
        <form id="hmw_support">
            <div class="card-text my-2 text-muted">
                <?php echo __("We're happy to answer any question or suggestion you may have and we aim to respond within 24 hours.", _HMW_PLUGIN_NAME_) ?>
            </div>
            <h5 class="hmw_success text-success" style="display: none"><?php _e('Question sent.', _HMW_PLUGIN_NAME_); ?></h5>
            <h6 class="hmw_error text-warning" style="display: none; "><?php _e('Error sending the email. Make sure that you can send emails from your website.', _HMW_PLUGIN_NAME_); ?></h6>

            <div class="col-sm-12 my-3 p-0 text-left hmw_field">
                <label for="hmw_email"><?php _e('Your Email (for reply)', _HMW_PLUGIN_NAME_); ?></label>
                <div class="input-group mb-2">
                    <input type="text" class="form-control bg-input" id="hmw_email" placeholder="<?php _e('Email', _HMW_PLUGIN_NAME_); ?> ..." value="<?php
                    $current_user = wp_get_current_user();
                    echo $current_user->user_email;
                    ?>">
                </div>
                <label for="hmw_question"><?php _e('Question / Feedback', _HMW_PLUGIN_NAME_); ?></label>
                <div class="input-group">
                    <textarea class="form-control bg-input" id="hmw_question" rows="3" style="height: 80px; position: relative; line-height: 20px; font-size: 16px; "></textarea>
                </div>
            </div>
            <div class="col-sm-12 p-0 hmw_field">
                <button type="button" class="btn btn-default"><?php _e('Send Message', _HMW_PLUGIN_NAME_); ?></button>
            </div>
        </form>

    </div>
</div>