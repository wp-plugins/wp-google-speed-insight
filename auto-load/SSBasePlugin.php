<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SSBasePlugin
 *
 * @author Kien
 */
abstract class SSBasePlugin {

    public $file;

    public function __construct($file) {
        $this->file = $file;
        $this->registerHooks();
        $this->run();
        add_action('init', array($this, 'init'));
        if (!is_admin()) {
            add_action('init', array($this, 'frontendInit'));
        } else {
            add_action('init', array($this, 'adminInit'));
        }
    }

    abstract function active();

    abstract function deactive();

    abstract function uninstall();

    abstract function run();

    function frontendInit() {
        
    }

    function adminInit() {
        
    }
    function init() {
        
    }

    public function registerHooks() {
        register_activation_hook($this->file, array($this, 'active'));
        register_deactivation_hook($this->file, array($this, 'deactive'));
        register_uninstall_hook($this->file, array($this, 'uninstall'));
    }

    public function registerScripts(array $scripts) {
        foreach ($scripts as $script) {
//            print_r($script);
            if (!isset($script['version']))
                $script['version'] = null;
            if (!isset($script['footer']))
                $script['footer'] = false;
            if (!isset($script['name']))
                $script['name'] = microtime(true);
            self::registerSingleScript($script['name'], $script['path'], $script['version'], $script['footer'], $this->file);
        }
    }

    static function registerSingleScript($name, $script, $version = null, $footer = false, $file) {
        if ($script[0] != '/')
            $script = plugin_dir_url($file) . $script;
        wp_enqueue_script($name, $script, array(), $version, false);
    }

    function registerCss($file) {
        if ($file[0] != '/')
            $file = plugin_dir_url($this->file) . $file;
        $handle = microtime(true);
        wp_enqueue_style($handle, $file);
    }

}
