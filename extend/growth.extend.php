<?php
/**
 * Growth Plugin for YoungCart5
 * 결제수단 프로모션 플러그인 (말풍선, 뱃지, 결제수단 우선배치)
 */
if (!defined('_GNUBOARD_')) exit;

define('GROWTH_PLUGIN_PATH', G5_PLUGIN_PATH.'/growth');
define('GROWTH_PLUGIN_URL', G5_PLUGIN_URL.'/growth');
define('GROWTH_TABLE', G5_TABLE_PREFIX.'growth_config');

if (!is_dir(GROWTH_PLUGIN_PATH)) return;

// Auto-install DB table
$_growth_lock = GROWTH_PLUGIN_PATH.'/.installed';
if (!file_exists($_growth_lock)) {
    $res = @sql_query("SHOW TABLES LIKE '".GROWTH_TABLE."'");
    if (!$res || sql_num_rows($res) == 0) {
        @sql_query("CREATE TABLE IF NOT EXISTS `".GROWTH_TABLE."` (
            `gc_id` int(11) NOT NULL AUTO_INCREMENT,
            `gc_use` tinyint(4) NOT NULL DEFAULT '0',
            `gc_text` varchar(255) NOT NULL DEFAULT '계좌이체로 구매시 30% 소득공제',
            `gc_color` varchar(20) NOT NULL DEFAULT '#3a8afd',
            `gc_bounce` tinyint(4) NOT NULL DEFAULT '1',
            `gc_top_settle` varchar(50) NOT NULL DEFAULT '',
            `gc_badge_use` tinyint(4) NOT NULL DEFAULT '0',
            `gc_badge_text` varchar(100) NOT NULL DEFAULT '',
            `gc_badge_color` varchar(20) NOT NULL DEFAULT '#ff006c',
            `gc_layout_vertical` tinyint(4) NOT NULL DEFAULT '1',
            PRIMARY KEY (`gc_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        @sql_query("INSERT INTO `".GROWTH_TABLE."` SET gc_use='0'");
    }
    @file_put_contents($_growth_lock, date('Y-m-d H:i:s'));
}

function growth_get_config() {
    return @sql_fetch("SELECT * FROM `".GROWTH_TABLE."` WHERE gc_id=1");
}

// Frontend: inject JS/CSS via tail_sub hook
add_event('tail_sub', 'growth_frontend', G5_HOOK_DEFAULT_PRIORITY, 0);

function growth_frontend() {
    $cf = growth_get_config();
    if (!$cf || !$cf['gc_use']) return;

    $data = json_encode(array(
        'use'             => (int)$cf['gc_use'],
        'text'            => $cf['gc_text'] ?: '계좌이체로 구매시 30% 소득공제',
        'color'           => $cf['gc_color'] ?: '#3a8afd',
        'bounce'          => (int)$cf['gc_bounce'],
        'top_settle'      => $cf['gc_top_settle'] ?: '',
        'badge_use'       => (int)$cf['gc_badge_use'],
        'badge_text'      => $cf['gc_badge_text'] ?: '',
        'badge_color'     => $cf['gc_badge_color'] ?: '#ff006c',
        'layout_vertical' => (int)$cf['gc_layout_vertical']
    ), JSON_UNESCAPED_UNICODE);

    echo '<link rel="stylesheet" href="'.GROWTH_PLUGIN_URL.'/growth.css">';
    echo '<script>var GROWTH_CONFIG='.$data.';</script>';
    echo '<script src="'.GROWTH_PLUGIN_URL.'/growth.js"></script>';
}

// Admin: inject sidebar link
add_event('tail_sub', 'growth_admin_link', G5_HOOK_DEFAULT_PRIORITY, 0);

function growth_admin_link() {
    global $is_admin;
    if ($is_admin !== 'super' || strpos($_SERVER['SCRIPT_NAME'], '/adm/') === false) return;

    $url = GROWTH_PLUGIN_URL.'/admin.php';
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var s=document.getElementById("snb");if(!s)return;var u=s.querySelector("ul");if(!u)return;var l=document.createElement("li");l.innerHTML=\'<a href="'.$url.'" style="color:#3a8afd;font-weight:bold">Growth</a>\';u.appendChild(l)});</script>';
}
