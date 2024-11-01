<?php

class VIPG_LB_Help
{
    public static function vglb_create_help_menu_page() {
        ?>

        <div class="wrap">
            <h1>Help</h1>
            <div class="vglb_help_sect">
                <h2>Quick Guide</h2>
                <ol>
                    <li><a href="#vglb_setup_styling">Setup styling</a></li>
                    <li><a href="#vglb_select_leaderboard">Select leaderboard</a></li>
                    <li><a href="#vglb_copy_short_code">Copy short code</a></li>
                </ol>
            </div>

            <div class="vglb_help_sect">
                <h3 id="vglb_setup_styling">Setup Styling</h3>
                <p>The styling settings can be set up on the <a href="<?php echo esc_url(admin_url('admin.php?page=vipg-leaderboard')); ?>">main page</a> of the plugin.</p>
                <p><b>Debug Info:</b> If enabled an error text will be displayed in the case that the leaderboard can't be loaded (wrong short code, network error, etc). Otherwise nothing will be displayed.</p>
                <p><b>Display Title:</b> Whether to display the promotion title or not.</p>
                <p><b>Display Info:</b> Whether to display the information tag or not.</p>
                <p><b>Color Scheme:</b><br>
                    There are predefined color schemes you can use. Chose scheme "custom" to apply your own settings. If like to add additional styling rules which are not covered by the options of the plugin, please check out the <a href="<?php echo esc_url(get_admin_url(null, "customize.php")); ?>">Customizer</a> of WordPress.<br>
                When you chose "no styles", no css code will be added by this plugin to your page. This is useful in the case you like to add your custom styles to your global css file.</p>
                <p>If the leaderboard is displayed different from the admin preview, then most likely CSS styles from your WordPress theme interferes with the leaderboard styles.</p>
                <img class="vglb_setup_img" src="<?php echo esc_url(plugin_dir_url(__FILE__)); ?>assets/setup_style_css.png" style="width: 800px">
            </div>

            <div class="vglb_help_sect">
                <h3 id="vglb_select_leaderboard">Select leaderboard</h3>

                <p>Go to the <a href="<?php echo esc_url(admin_url('admin.php?page=vipg-leaderboard-setup')); ?>">"Get Short Code Page"</a> and select a leaderboard from the drop down menu you like to include to your page. Most of our leaderboards are running on monthly basis. For this reason, there are three different "modes" to display the leaderboards:</p>
                <ul class="vglb_ul">
                    <li><b>static:</b> always the same (month) leaderboard will be displayed.</li>
                    <li><b>current:</b> always the most recent leaderboard of a series will be displayed.</li>
                    <li><b>previous:</b> always the next to last (previous month) leaderboard a series will be displayed.</li>
                </ul>
            </div>

            <div class="vglb_help_sect">
                <h3 id="vglb_copy_short_code">Copy & Paste Short Code</h3>
                <p>The short code tells WordPress where you want to display which leaderboard and can be generated on the <a href="<?php echo esc_url(admin_url('admin.php?page=vipg-leaderboard-setup')); ?>">"Get Short Code Page"</a>. You just need to copy & paste the short code text to your page where you like to include the leaderboard:</p>
                <img class="vglb_setup_img" src="<?php echo esc_url(plugin_dir_url(__FILE__)); ?>assets/setup_short_code_editor.png" style="height: 350px; margin-right: 20px;"><img class="vglb_setup_img" src="<?php echo esc_url(plugin_dir_url(__FILE__)); ?>assets/setup_short_code_page.png" style="height: 350px">
            </div>

            <div class="vglb_help_sect">
                <h3 id="vglb_copy_short_code">Need help?</h3>
                <p>Having questions or wishes regarding this plugin, please get in touch with Eddy:</p>
                <ul class="vglb_ul">
                    <li><b>Email:</b> <a href="mailto:admin@vip-grinders.com">admin@vip-grinders.com</a></li>
                    <li><b>Skype:</b> <a href="skype:tr_sofamann?chat">vip-grinders.tech</a></li>
                </ul>
            </div>

        </div>
        <?php
    }
}