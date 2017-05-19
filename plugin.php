<?php
/**
 * Plugin Name: WP REST API - Polylang
 * Description: Polylang integration for the WP REST API
 * Author: Jorge R Garcia / Lucas Freitas
 * Author URI:
 * Version: 0.0.4
 * Plugin URI:
 * License: MIT
 */

/**
 * Init
 */
function polylang_json_api_init()
{

    global $polylang;

    $default = pll_default_language();
    $langs = pll_languages_list();

    if (isset($_GET['lang'])) {
        $cur_lang = $_GET['lang'];
    }
    if (!isset($cur_lang) || (isset($cur_lang) && !in_array($cur_lang, $langs))) {
        $cur_lang = $default;
    }

    $polylang->curlang = $polylang->model->get_language($cur_lang);
    $GLOBALS['text_direction'] = $polylang->curlang->is_rtl ? 'rtl' : 'ltr';
}

/**
 *  Get available languages
 *
 * @return array
 */
function polylang_json_api_languages()
{
    return pll_languages_list();
}

function polylang_json_api_post_translations($request)
{
    return pll_get_post_translations($request['id']);
}

add_action('rest_api_init', 'polylang_json_api_init');
add_action('rest_api_init', function () {
    register_rest_route('polylang/v2', '/languages', array(
        'methods' => 'GET',
        'callback' => 'polylang_json_api_languages',
    ));
    register_rest_route('polylang/v2', '/posts/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'polylang_json_api_post_translations',
        'args' => array(
            'id' => array(
                'validate_callback' => function($param, $request, $key) {
                    return true;
                    return is_numeric( $param );
                }
            )
        )
    ));
});
