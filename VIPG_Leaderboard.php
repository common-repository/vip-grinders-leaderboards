<?php

class VIPG_Leaderboard
{
    private static $initiated = false;
    private static $options = null;

    public static function init() {
        if ( ! self::$initiated ) {
            self::init_hooks();
            self::add_default_options();
            self::$initiated = true;
        }
    }

    public static function init_hooks() {

        register_activation_hook(__FILE__, function () {});

        register_deactivation_hook( __FILE__, function() {});

        add_action( 'admin_menu', array('VIPG_Leaderboard', 'vglb_admin_menu'));

        add_action('admin_init', 'VIPG_Leaderboard::vglb_admin_init');

        add_action( 'admin_notices', 'VIPG_Leaderboard::vglb_notices_action' );

        //register admin scripts
        add_action('admin_init', 'VIPG_Leaderboard::vglb_admin_register_scripts');

        //enqueue scripts admin
        add_action('admin_enqueue_scripts', 'VIPG_Leaderboard::vglb_admin_enqueue_scripts');

        //add short code
        add_action('init', function(){
            add_shortcode('vglb_leaderboard', 'VIPG_Leaderboard::vglb_print_leaderboard');
        });

        //write css code to header
        add_action('wp_head', 'VIPG_Leaderboard::vglb_write_css');
    }

    public static function add_default_options() {

        $options = get_option('vglb_options');

        if($options === false){
            $options = array(
                'debug'                 => 'disabled',
                'display_title'         => 'enabled',
                'display_info'          => 'enabled',
                'color_scheme'          => 'Custom',
                'bg_color_header'       => '#0099cc',
                'font_color_header'     => '#fff',
                'bg_color1_body'        => '#f5f5f5',
                'bg_color2_body'        => '#e5e5e5',
                'font_color_body'       => '#333',
                'hover_color_body'      => '#f3f0a6',
                'cell_padding'          => '5px',
            );

            add_option('vglb_options', $options);
        }

        self::$options = $options;
    }

    public static function vglb_admin_menu() {

        add_menu_page(
            'VG Leaderboard Settings',   // page title
            'VG Leaderboards',         //menu title
            'manage_options',           // capability
            'vipg-leaderboard',              // menu slug
            array('VIPG_Leaderboard', 'vglb_create_main_menu_page')  // callback function
        );

        //create submenu items
        add_submenu_page(
            'vipg-leaderboard',
            'Get Short Code',
            'Get Short Code',
            'manage_options',
            'vipg-leaderboard-setup',
            array('VIPG_LB_Setup', 'vglb_create_setup_menu_page')
        );

        //create submenu items
        add_submenu_page(
            'vipg-leaderboard',
            'Help',
            'Help',
            'manage_options',
            'vipg-leaderboard-help',
            array('VIPG_LB_Help', 'vglb_create_help_menu_page')
        );
    }

    public static function vglb_create_main_menu_page() {
        ?>

        <input type="hidden" id="vglb_page_identifier" value="main">
        <div class="wrap">
            <h1>VIP-Grinders.com - Leaderboards</h1>
            <div id="vglb_help_info_wrap">
                Checkout the <a href="<?php echo esc_url(admin_url('admin.php?page=vipg-leaderboard-help')); ?>">help page</a> for instructions.
            </div>
            <form action="options.php" method="post">

                <?php
                wp_nonce_field( 'vglb_nonce_action', 'vglb_nonce_name' );
                settings_fields( 'vglb_options' );
                do_settings_sections( 'vipg-leaderboard' );
                submit_button( 'Save Changes', 'primary' );
                ?>
            </form>
        </div>
        <div class="wrap">
            <h2>Preview</h2>
            <style id="vblb_preview_styles"></style>
            <div id="vglb_preview_wrap">loading...</div>
        </div>
        <?php
    }

    public static function vglb_admin_init(){
        // Define the setting args
        $args = array(
            'type' 				=> 'string',
            'sanitize_callback' => 'VIPG_Leaderboard::vglb_validate_options',
            'default' 			=> NULL
        );


        // Register our settings
        register_setting( 'vglb_options', 'vglb_options', $args );

        // Add a settings section
        add_settings_section(
            'vglb_setting_section',  //html id
            'Display Settings',   //title
            'VIPG_Leaderboard::vglb_section_info',     //callback
            'vipg-leaderboard'          //page
        );

        // Create radio settings field
        add_settings_field(
            'vglb_debug',            //html id
            'Debug Info',                //title
            'VIPG_Leaderboard::vglb_setting_debug',    //callback
            'vipg-leaderboard',                     //page
            'vglb_setting_section'               //section
        );

        // Create radio settings field
        add_settings_field(
            'vglb_display_title',            //html id
            'Display Title',                //title
            'VIPG_Leaderboard::vglb_setting_display_title',    //callback
            'vipg-leaderboard',                     //page
            'vglb_setting_section'               //section
        );

        // Create radio settings field
        add_settings_field(
            'vglb_display_info',            //html id
            'Display Info',                //title
            'VIPG_Leaderboard::vglb_setting_display_info',    //callback
            'vipg-leaderboard',                     //page
            'vglb_setting_section'               //section
        );

        // Create dropdown settings field
        add_settings_field(
            'vglb_color_scheme',            //html id
            'Color Scheme',                //title
            'VIPG_Leaderboard::vglb_setting_color_scheme',    //callback
            'vipg-leaderboard',                     //page
            'vglb_setting_section'               //section
        );

        // Create settings field
        add_settings_field(
            'vglb_bg_color_header',          //html id
            'Background Color Table Header',      //title
            'VIPG_Leaderboard::vglb_setting_bg_color_header',  //callback
            'vipg-leaderboard',                 //page
            'vglb_setting_section'           //section
        );

        // Create settings field
        add_settings_field(
            'vglb_font_color_header',            //html id
            'Font Color Table Header',                //title
            'VIPG_Leaderboard::vglb_setting_font_color_header',    //callback
            'vipg-leaderboard',                     //page
            'vglb_setting_section'               //section
        );

        // Create settings field
        add_settings_field(
            'vglb_bg_color1_body',            //html id
            'Background Color 1 Table Body',                //title
            'VIPG_Leaderboard::vglb_setting_bg_color1_body',    //callback
            'vipg-leaderboard',                     //page
            'vglb_setting_section'               //section
        );

        // Create settings field
        add_settings_field(
            'vglb_bg_color2_body',            //html id
            'Background Color 2 Table Body',                //title
            'VIPG_Leaderboard::vglb_setting_bg_color2_body',    //callback
            'vipg-leaderboard',                     //page
            'vglb_setting_section'               //section
        );

        // Create settings field
        add_settings_field(
            'vglb_font_color_body',            //html id
            'Font Color Table Body',                //title
            'VIPG_Leaderboard::vglb_setting_font_color_body',    //callback
            'vipg-leaderboard',                     //page
            'vglb_setting_section'               //section
        );

        // Create settings field
        add_settings_field(
            'vglb_hover_color_body',            //html id
            'Hover Color Table Body',                //title
            'VIPG_Leaderboard::vglb_setting_hover_color_body',    //callback
            'vipg-leaderboard',                     //page
            'vglb_setting_section'               //section
        );

        // Create settings field
        add_settings_field(
            'vglb_cell_padding',            //html id
            'Table Cell Padding',                //title
            'VIPG_Leaderboard::vglb_setting_cell_padding',    //callback
            'vipg-leaderboard',                     //page
            'vglb_setting_section'               //section
        );
    }

    public static function vglb_section_info(){
        echo '<p>Below you can make configurations on how the leaderboards will be displayed on your page.</p>';
    }

    public static function vglb_setting_debug(){
        $debug = self::$options['debug'];
        $items = array( 'enabled', 'disabled' );

        foreach( $items as $item ) {
            echo "<label><input " . checked( $debug, $item, false ) . " value='" . esc_attr( $item ) . "' name='vglb_options[debug]' type='radio' />" . esc_html( $item ) . "</label><br />";
        }
    }

    public static function vglb_setting_display_title(){
        $display_title = self::$options['display_title'];
        $items = array( 'enabled', 'disabled' );

        foreach( $items as $item ) {
            echo "<label><input class='vglb_display_title' id='vglb_display_title_" . esc_attr($item) . "' " . checked( $display_title, $item, false ) . " value='" . esc_attr( $item ) . "' name='vglb_options[display_title]' type='radio' />" . esc_html( $item ) . "</label><br />";
        }
    }

    public static function vglb_setting_display_info(){
        $display_info = self::$options['display_info'];
        $items = array( 'enabled', 'disabled' );

        foreach( $items as $item ) {
            echo "<label><input class='vglb_display_info' id='vglb_display_info_" . esc_attr($item) . "' " . checked( $display_info, $item, false ) . " value='" . esc_attr( $item ) . "' name='vglb_options[display_info]' type='radio' />" . esc_html( $item ) . "</label><br />";
        }
    }

    public static function vglb_setting_color_scheme(){
        $color_scheme = self::$options['color_scheme'];
        $items = array( 'Blue', 'Green', 'Red', 'Custom', 'No Styles');

        echo "<select id='vglb_color_scheme' name='vglb_options[color_scheme]'>";

        foreach( $items as $item ) {
            echo "<option value='" . esc_attr( $item ) . "' ".selected( $color_scheme, $item, false ).">" . esc_html( $item ) . "</option>";
        }

        echo "</select>";
    }

    public static function vglb_setting_bg_color_header(){
        $bg_color = self::$options['bg_color_header'];
        echo "<input class='vglb_style_field' id='vglb_bg_color_header' name='vglb_options[bg_color_header]' type='text' value='" . esc_attr( $bg_color ) . "' />";
    }

    public static function vglb_setting_font_color_header(){
        $font_color = self::$options['font_color_header'];
        echo "<input class='vglb_style_field' id='vglb_font_color_header' name='vglb_options[font_color_header]' type='text' value='" . esc_attr( $font_color ) . "' />";
    }

    public static function vglb_setting_bg_color1_body(){
        $bg_color = self::$options['bg_color1_body'];
        echo "<input class='vglb_style_field' id='vglb_bg_color1_body' name='vglb_options[bg_color1_body]' type='text' value='" . esc_attr( $bg_color ) . "' />";
    }

    public static function vglb_setting_bg_color2_body(){
        $bg_color = self::$options['bg_color2_body'];
        echo "<input class='vglb_style_field' id='vglb_bg_color2_body' name='vglb_options[bg_color2_body]' type='text' value='" . esc_attr( $bg_color ) . "' />";
    }

    public static function vglb_setting_font_color_body(){
        $font_color = self::$options['font_color_body'];
        echo "<input class='vglb_style_field' id='vglb_font_color_body' name='vglb_options[font_color_body]' type='text' value='" . esc_attr( $font_color ) . "' />";
    }

    public static function vglb_setting_hover_color_body(){
        $hover_color = self::$options['hover_color_body'];
        echo "<input class='vglb_style_field' id='vglb_hover_color_body' name='vglb_options[hover_color_body]' type='text' value='" . esc_attr( $hover_color ) . "' />";
    }

    public static function vglb_setting_cell_padding(){
        $cell_padding = self::$options['cell_padding'];
        echo "<input class='vglb_style_field' id='vglb_cell_padding' name='vglb_options[cell_padding]' type='text' value='" . esc_attr( $cell_padding ) . "' />";
    }

    public static function vglb_validate_options($input) {

        // Sanitize the data we are receiving
        $valid['debug'] = sanitize_text_field( $input['debug'] );
        $valid['display_title'] = sanitize_text_field( $input['display_title'] );
        $valid['display_info'] = sanitize_text_field( $input['display_info'] );
        $valid['color_scheme'] = sanitize_text_field( $input['color_scheme'] );
        $valid['bg_color_header'] = sanitize_text_field( $input['bg_color_header'] );
        $valid['font_color_header'] = sanitize_text_field( $input['font_color_header'] );

        $valid['bg_color1_body'] = sanitize_text_field( $input['bg_color1_body'] );
        $valid['bg_color2_body'] = sanitize_text_field( $input['bg_color2_body'] );
        $valid['font_color_body'] = sanitize_text_field( $input['font_color_body'] );
        $valid['hover_color_body'] = sanitize_text_field( $input['hover_color_body'] );
        $valid['cell_padding'] = sanitize_text_field( $input['cell_padding'] );

        return $valid;
    }

    public static function vglb_notices_action() {
        settings_errors( 'bg_color_header' );
        settings_errors( 'font_color_header' );
    }

    public static function vglb_nonce_verification(){

        // Bail if no nonce field.
        if ( ! isset( $_POST['vglb_nonce_name'] ) ) {
            return;
        }

        // Display error and die if not verified.
        if ( ! wp_verify_nonce( $_POST['vglb_nonce_name'], 'vglb_nonce_action' ) ) {
            wp_die( 'Your nonce could not be verified.' );
        }
    }

    public static function vglb_admin_register_scripts(){

        wp_register_script(
            'vglb_admin_script',  //script id
            plugin_dir_url(__FILE__) . 'vglb-admin.js',  //url to script
            array(),    //dependencies
            '1.4',    //version
            true        //output in footer
        );

        wp_register_style(
            'vglb_admin_style',
            plugin_dir_url(__FILE__) . 'vglb-admin.css',
            array(),
            '1.3'
        );
    }

    public static function vglb_register_scripts(){

        wp_register_script(
            'vglb_user_script',  //script id
            plugin_dir_url(__FILE__).'vglb-user.js',  //url to script
            array(),    //dependencies
            '1.0.0',    //version
            true        //output in footer
        );
    }

    public static function vglb_admin_enqueue_scripts(){
        wp_enqueue_script('vglb_admin_script');
        wp_enqueue_style( 'vglb_admin_style');
    }


    public static function vglb_print_leaderboard($attr){

        $attr = shortcode_atts(
            array(
                'id' => 0,
                'sid' => 0,
                'lb' => 'current',
                'max_ranks' => 0
            ),
            $attr,
            'vglb_leaderboard'
        );

        $attr['id'] = intval($attr['id']);
        $attr['sid'] = intval($attr['sid']);
        $attr['max_ranks'] = intval($attr['max_ranks']);

        if(!in_array($attr['lb'], array('current', 'previous', 'prev', 'past'))){
            $attr['lb'] = 'current';
        }

        $lb = VIPG_Leaderboard::vglb_get_leaderboard_data($attr);


        if($lb === false || !property_exists($lb, 'cols')){
            if(self::$options['debug'] === 'enabled'){
                echo 'Fetching leaderboard data failed. Please check the short code or get in touch with the <a href="' . esc_url(admin_url('admin.php?page=vipg-leaderboard-help#support')) . 'support</a>.';
            }
            return;
        }

        $cnt = count($lb->userRanking->rank);

        if($attr['max_ranks'] > 0 && $attr['max_ranks'] < $cnt){
            $cnt = $attr['max_ranks'];
        }

        ob_start();

        if(self::$options['display_title'] == 'enabled'){
            echo '<div class="vglb_title_wrap"><h2>' . esc_html($lb->name) . '</h2></div>';
        }

        if(self::$options['display_info'] == 'enabled'){
            echo '<div class="vglb_info_wrap">';
            echo 'Last updated on: ' . esc_html(date_format(new DateTime($lb->lastUpdate), 'H:i')) . ' CET, ' . esc_html(date_format(new DateTime($lb->lastUpdate),'d.m.Y'));
            echo ', Status: ';
            echo ($lb->is_final == 1) ? 'Final' : 'Running';
            echo '</div>';
        }

        echo '<div class="vglb_table_wrap"><table class="vglb_table" id="vglb_table_' . esc_attr($attr['id']) .'_' . esc_attr($attr['sid']) . '"><thead><tr>';

        for($i=0;$i<count($lb->cols->col);$i++){
            echo '<th>' . esc_html($lb->cols->col[$i]['title']) . '</th>';
        }

        echo '</tr></thead><tbody>';

        if($cnt == 0){
            echo '<tr>';

            for($i=0;$i<count($lb->cols->col);$i++){
                echo '<td>-</td>';
            }

            echo '</tr>';
        }

        for($i=0;$i<$cnt;$i++){
            echo '<tr>';

            for($j=0;$j<count($lb->cols->col);$j++){
                echo '<td>' . esc_html($lb->userRanking->rank[$i][$lb->cols->col[$j]['key']]) . '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody></table></div>';

        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public static function vglb_get_leaderboard_data($attr){
        $url = 'https://www.vip-grinders.com/member/promotions_dir/leaderboards/';

        if($attr['sid'] == 0 && $attr['id'] == 0){
            return false;
        }

        if($attr['sid'] != 0){
            $url .= 'xml.php?sid=' . $attr['sid'] . '&lb=' . $attr['lb'];
        }else{
            $url .= 'xml/' . $attr['id'] . '.xml';
        }

        $result = wp_remote_get($url, array('method' => 'GET'));

        $xml = simplexml_load_string($result['body']);

        return $xml;
    }

    public static function vglb_write_css(){

        if(self::$options['color_scheme'] == 'No Styles'){
            return;
        }

        $item = array(
            'bg_color_header'       => '',
            'font_color_header'     => '',
            'bg_color1_body'        => '',
            'bg_color2_body'        => '',
            'font_color_body'       => '',
            'hover_color_body'      => '',
            'cell_padding'          => '',
        );

        $schemes = array(
            'Blue' => array(
                'bg_color_header'       => '#0099cc',
                'font_color_header'     => '#fff',
                'bg_color1_body'        => '#f5f5f5',
                'bg_color2_body'        => '#e5e5e5',
                'font_color_body'       => '#333',
                'hover_color_body'      => '#f3f0a6',
                'cell_padding'          => '5px',
            ),
            'Green' => array(
                'bg_color_header'       => '#00cc00',
                'font_color_header'     => '#fff',
                'bg_color1_body'        => '#f5f5f5',
                'bg_color2_body'        => '#e5e5e5',
                'font_color_body'       => '#333',
                'hover_color_body'      => '#f3f0a6',
                'cell_padding'          => '5px',
            ),
            'Red' => array(
                'bg_color_header'       => '#cc0000',
                'font_color_header'     => '#fff',
                'bg_color1_body'        => '#f5f5f5',
                'bg_color2_body'        => '#e5e5e5',
                'font_color_body'       => '#333',
                'hover_color_body'      => '#f3f0a6',
                'cell_padding'          => '5px',
            )
        );

        if(self::$options['color_scheme'] == 'Custom'){
            $item['bg_color_header'] = self::$options['bg_color_header'];
            $item['font_color_header'] = self::$options['font_color_header'];
            $item['bg_color1_body'] = self::$options['bg_color1_body'];
            $item['bg_color2_body'] = self::$options['bg_color2_body'];
            $item['font_color_body'] = self::$options['font_color_body'];
            $item['hover_color_body'] = self::$options['hover_color_body'];
            $item['cell_padding'] = self::$options['cell_padding'];
        }else{
            foreach ($item as $key => $val){
                $item[$key] = $schemes[self::$options['color_scheme']][$key];
            }
        }

        echo '<style>
            table.vglb_table {
                border-collapse: collapse;
                width: 100%;
            }
            table.vglb_table td {
                padding: ' . esc_attr($item['cell_padding']) . ';
            }
            table.vglb_table>thead>tr>th{
                background-color: ' . esc_attr($item['bg_color_header']) . ';
                color: ' . esc_attr($item['font_color_header']) . ';
                text-align: left;
                padding: ' . esc_attr($item['cell_padding']) . ';
            }
            table.vglb_table>tbody {
                color: ' . esc_attr($item['font_color_body']) . ';
            }
            table.vglb_table>tbody>tr:nth-child(odd) {
                background-color: ' . esc_attr($item['bg_color1_body']) . ';
            }
            table.vglb_table>tbody>tr:nth-child(even) {
                background-color: ' . esc_attr($item['bg_color2_body']) . ';
            }
            table.vglb_table>tbody>tr:hover {
                background-color: ' . esc_attr($item['hover_color_body']) . ';
            }
            div.vglb_table_wrap{
                overflow-x: auto;
            }
            ';

        echo '</style>';
    }
}