<?php
/*
  Plugin Name: Performance Check
  Plugin URI: http://blog-itlboy.rhcloud.com
  Description: Performance check for all wordpress site
  Version: 1.0
  Author: Itlboy
  Author URI: http://blog-itlboy.rhcloud.com
  License: GPL2
 */
/*
  Copyright 2012  Kien Nguyen Trung  (email : nkien.bk@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
 */

defined('ABSPATH') or die('No script kiddies please!');
//opcache_reset();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(-1);
/* * ********************************* Autoload ********************************* */
spl_autoload_register(function($className) {
    if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'auto-load' . DIRECTORY_SEPARATOR . $className . '.php'))
        include __DIR__ . DIRECTORY_SEPARATOR . 'auto-load' . DIRECTORY_SEPARATOR . $className . '.php';
});

class SpeedInsightPlugin extends SSBasePlugin {

    public $tableName = 'speed-insight';

    function active() {
        
    }

    function createDb() {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $table_name = $wpdb->prefix . $this->tableName;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `desktop_speed_score` int(2) NOT NULL,
            `mobile_speed_score` int(2) NOT NULL,
            `mobile_usability_score` int(2) NOT NULL,
            `numberResources` int(11) NOT NULL,
            `numberHosts` int(11) NOT NULL,
            `totalRequestBytes` int(11) NOT NULL,
            `numberStaticResources` int(11) NOT NULL,
            `htmlResponseBytes` int(11) NOT NULL,
            `cssResponseBytes` int(11) NOT NULL,
            `imageResponseBytes` int(11) NOT NULL,
            `javascriptResponseBytes` int(11) NOT NULL,
            `otherResponseBytes` int(11) NOT NULL,
            `numberJsResources` int(11) NOT NULL,
            `numberCssResources` int(11) NOT NULL,
            `ruleResults` text COLLATE utf8_unicode_ci NOT NULL
          ) $charset_collate;";
        $wpdb->get_results($sql);
        $indexSql = "ALTER TABLE `$table_name`
            ADD PRIMARY KEY (`path`),
            ADD KEY `desktop_speed_score` (`desktop_speed_score`),
            ADD KEY `mobile_speed_score` (`mobile_speed_score`),
            ADD KEY `mobile_usability_score` (`mobile_usability_score`),
            ADD KEY `numberResources` (`numberResources`),
            ADD KEY `totalRequestBytes` (`totalRequestBytes`),
            ADD KEY `numberHosts` (`numberHosts`),
            ADD KEY `numberStaticResources` (`numberStaticResources`),
            ADD KEY `htmlResponseBytes` (`htmlResponseBytes`),
            ADD KEY `cssResponseBytes` (`cssResponseBytes`),
            ADD KEY `imageResponseBytes` (`imageResponseBytes`),
            ADD KEY `javascriptResponseBytes` (`javascriptResponseBytes`),
            ADD KEY `otherResponseBytes` (`otherResponseBytes`),
            ADD KEY `numberJsResources` (`numberJsResources`),
            ADD KEY `numberCssResources` (`numberCssResources`);";
        $wpdb->get_results($indexSql);
    }

    function deactive() {
//        global $wpdb;
//        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//        $table_name = $wpdb->prefix . $this->tableName;
//        $wpdb->get_results($indexSql);
    }

    function uninstall() {
        
    }

    function run() {
        
    }

    function adminInit() {
        add_action('wp_ajax_si-get-stats', 'si_get_stats');

        function si_get_stats() {
            global $speedInsightPlugin;
            $url = $_REQUEST['url'];
            $speedInsight = new SpeedInsight($url);
            header('Content-Type: application/json');
            echo $speedInsight->returnJson();
            exit(0);
        }

        parent::adminInit();
    }

    function init() {
        if (current_user_can('manage_options')) {

            function SIScripts() {
                global $speedInsightPlugin;
                if (is_admin()) {
                    $urlToCheck = home_url();
//                    die($urlToCheck);
                } else {
                    $urlToCheck = $speedInsightPlugin->getCurrentURL();
                }
                wp_enqueue_script('get-stats', '//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js');
                wp_enqueue_script('get-stats-js', plugins_url('js/speed-insight.js', __FILE__));
                wp_localize_script('get-stats', 'MyAjax', array(
                    // URL to wp-admin/admin-ajax.php to process the request
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'urlToCheck' => $urlToCheck,
                ));
            }

            SIScripts();

//            add_action('wp_enqueue_scripts', 'SIScripts');

            $this->registerCss('css/speed-insight.css');

//            global $SIDesktopResult;
//            global $SIMobileResult;
            $url = $this->getCurrentURL();
//            $SIDesktopResult = new SpeedInsight($url);
//            var_dump($desktopResult->ruleGroups->SPEED->score);
//            die();
//            $SIMobileResult = new SpeedInsight($url, 'mobile');
            add_action('admin_bar_menu', 'addAdminBarNodes', 999);

            function addAdminBarNodes($wp_admin_bar) {
//                global $SIDesktopResult;
//                $score = $SIDesktopResult->getSpeedScore();
                $args = array(
                    'id' => 'si-desktop',
                    'title' => 'Desktop: ' . "<span id='si-desktop'>...</span>",
                    'onclick' => 'return false',
                    'meta' => array('class' => "si-stats", 'target' => '_blank','title'=>'Click for detail'),
                    'href' => SpeedInsight::getCurrentUrl('desktop'),
                );
                $wp_admin_bar->add_node($args);
//                global $SIMobileResult;
//                $score = $SIMobileResult->getSpeedScore();
                $wp_admin_bar->add_node(array(
                    'id' => 'si-mobile',
                    'title' => 'Mobile: ' . "<span id='si-mobile'>...</span>",
                    'onclick' => 'return false',
                    'meta' => array('class' => 'si-stats', 'target' => '_blank','title'=>'Click for detail'),
                    'href' => SpeedInsight::getCurrentUrl('mobile'),
                ));
//                $score = $SIMobileResult->result->ruleGroups->USABILITY->score;
                $wp_admin_bar->add_node(array(
                    'id' => 'si-mobile-usability',
                    'title' => 'Mobile usability: ' . "<span id='si-mobile-usability'>...</span>",
                    'onclick' => 'return false',
                    'meta' => array('class' => 'si-stats', 'target' => '_blank','title'=>'Click for detail'),
                    'href' => SpeedInsight::getCurrentUrl('mobile'),
                ));
            }

        }
//        parent::frontendInit();
    }

    function frontendInit() {
        
    }

    function getCurrentURL() {
        return 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
    }

}

global $speedInsightPlugin;
$speedInsightPlugin = new SpeedInsightPlugin(__FILE__);
