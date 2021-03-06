<?php
/**
 * Plugin Name: WP REST API - Polylang
 * Description: Polylang integration for the WP REST API
 * Author: Jorge R Garcia / Lucas Freitas
 * Author URI:
 * Version: 0.0.5
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


/**
 * Get the related posts translations (only the permalinks)
 */
function polylang_json_api_post_translations($request)
{
    $r = [];
    foreach (pll_get_post_translations($request['id']) as $lang => $post_id) {
        $r[$lang] = get_permalink($post_id);
    }
    return $r;
}

/**
 * Get the post data in a specific language.
 */
function polylang_json_api_other_post($request)
{
    return pll_get_post($request['id'], $request['lang']);
}

function polylang_json_api_term_translations($request) {
    $r = [];
    foreach (pll_get_term_translations($request['id']) as $lang => $term_id) {
        $r[$lang] = get_permalink($term_id);
    }
    return $r;
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
    register_rest_route('polylang/v2', '/term/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'polylang_json_api_term_translations',
        'args' => array(
            'id' => array(
                'validate_callback' => 'is_numeric'
            ),
        )
    ));
});
