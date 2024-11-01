<?php

class VIPG_LB_Setup
{
    private static $data = null;

    public static function vglb_create_setup_menu_page() {
        $json = VIPG_LB_Setup::vglb_get_leaderboards();
        self::$data = json_decode($json, true);
        $cnt = count(self::$data["id"]);

        for ($i = 0; $i < $cnt; $i++) {
            self::$data["start"][$i] = date('Y-m', strtotime(self::$data["start"][$i])) . '-01';
        }

        echo '<input type="hidden" id="vglb_page_identifier" value="setup">';
        echo '<div class="wrap">';
        echo '<div class="vglb_opt_wrap">';
        echo '<div class="vglb_opt_lable">Select Leaderboard</div>';
        echo '<select name="promotion_id" id="vglb_promotion_id" size="1">';
        
        $start_pre = "";
        for ($i = 0; $i < $cnt; $i++) {

            if ($start_pre != self::$data["start"][$i]) {
                echo '<optgroup label="' . esc_attr(self::$data["start"][$i]) . '">';
            }

            echo '<option value="' . esc_attr(self::$data["id"][$i]) . '" sid="' . esc_attr(self::$data["sid"][$i]) . '"';
            echo ($i == 0) ? ' selected' : '';
            echo '>' . esc_attr(self::$data["name"][$i]) . '</option>';

            if ($i + 1 == $cnt) {
                echo '</optgroup>';
            } else {
                if (self::$data["start"][$i] != self::$data["start"][$i + 1]) {
                    echo '</optgroup>';
                }
            }
            $start_pre = self::$data["start"][$i];
        }

        echo '</select>';
        echo '</div>';  //close vglb_opt_wrap


        echo '<div class="vglb_opt_wrap">';
        echo '<div class="vglb_opt_lable">Max rankings to display (0 = all)</div>';
        echo '<input type="text" name="vglb_max_ranks" id="vglb_max_ranks" value="0" placeholder="0 = unlimited ranks">';
        echo '<div id="vglb_max_ranks_error"></div>';
        echo '</div>';

        echo '<div class="vglb_opt_wrap">';
        echo '<div class="vglb_opt_lable">Short Code</div>';
        echo '<textarea id="vglb_short_code"></textarea>';
        echo '</div>';  //close vglb_opt_wrap

        echo '<div class="vglb_opt_wrap">';
        echo '<div class="vglb_opt_lable">Mode</div>';
        echo '<input type="radio" class="vglb_mode" id="vglb_mode_static" name="vglb_mode" value="static" checked><span class="vglb_radio_label">Static Leaderboard</span>';
        echo '<input type="radio" class="vglb_mode" id="vglb_mode_current" name="vglb_mode" value="current"><span class="vglb_radio_label">Current Leaderboard</span>';
        echo '<input type="radio" class="vglb_mode" id="vglb_mode_current" name="vglb_mode" value="previous"><span class="vglb_radio_label">Previous Leaderboard</span>';
        echo '</div>';  //close vglb_opt_wrap

        echo '<div class="vglb_opt_lable">Preview:</div>';
        echo '<div id="vglb_preview_wrap">loading...</div>';
    }

    public static function vglb_get_leaderboard_preview($params){

        VIPG_Leaderboard::vglb_write_css();

        $opt = array(
            'id' => $params['promotion_id'],
            'max_ranks' => $params['max_ranks'],
            'sid' => $params['sid'],
            'lb' => $params['lb']
        );

        echo VIPG_Leaderboard::vglb_print_leaderboard($opt);

        wp_die();
    }

    public static function vglb_get_leaderboards() {
        $url = 'https://www.vip-grinders.com/member/promotions_dir/leaderboards/get_leaderboards.php';
        $result = wp_remote_get($url, array('method' => 'GET'));
        return $result['body'];
    }
}