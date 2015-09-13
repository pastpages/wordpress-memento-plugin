<?php # -*- coding: utf-8 -*-
/*
Plugin Name: Memento
Plugin URI:  http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A plugin for Wordpress web sites to enable the Memento framework for time-based access
Version:     0.0.1
Author:      Ben Welsh
Author URI:  http://palewi.re/who-is-ben-welsh/
License:     MIT
License URI: http://opensource.org/licenses/MIT

The MIT License (MIT)

Copyright (c) 2015 Ben Welsh

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

# defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
include "functions.php";


function wp_memento_add_rewrites()
{
    # The timemap list page
    add_rewrite_rule(
        '^timemap/(.*)',
        'index.php?timemap_url=$matches[1]',
        'top'
    );
    # The post revision detail page
    add_rewrite_endpoint('revision', EP_PERMALINK);
}
add_action( 'init', 'wp_memento_add_rewrites' );


function wp_memento_rewrite_add_vars( $vars )
{
    $vars[] = 'timemap_url';
    return $vars;
}
add_filter( 'query_vars', 'wp_memento_rewrite_add_vars' );


function wp_memento_catch_vars()
{
    # Handle a timemap list request
    if(get_query_var( 'timemap_url' ))
    {
        # Get the timemap URL and clean it up
        $timemap_url = get_query_var( 'timemap_url' );
        $timemap_url = str_replace("http:/", "http://", $timemap_url);
        $timemap_url .= "/";

        # Pull the original post from the database
        $post_id = url_to_postid($timemap_url);

        # If it doesn't exist, throw a 404 error
        if ($post_id == 0) {
           include(get_query_template('404'));
           exit;
        }

        # Render the timemap response
        $charset = get_option('blog_charset');
        header('Content-Type: application/link-format; charset=' . $charset);
        $post = get_post($post_id);
        $revision_list = get_post_revisions($post_id);
        array_unshift($revision_list, $post);
        include('timemap-list.php');

        # Finish
        exit;
    }
}
add_action( 'template_redirect', 'wp_memento_catch_vars' );


function wp_momento_content_filter($content) {
    if(is_singular() && get_query_var('revision'))
    {
        // If this a normal post and not a revision
        // then nothing special should happen
        if (is_single(get_query_var('revision'))) {
            return $content;
        // But if there is a revision id then we get to work.
        } else {
            # Remove the filer to avoid triggering an infinite loop
            remove_filter('the_content', 'wp_momento_content_filter');
            # Query this revision from the database
            # (Need a 404 when the id isn't found in the database)
            $revision_id = get_query_var('revision');
            $revision = wp_get_post_revision($revision_id);
            # Render the content using this older data
            $rev_content = apply_filters('the_content', $revision->post_content);
            # Put the filter override back on so we can use it again
            add_filter('the_content', 'wp_momento_content_filter');
            # Return the revision content
            return $rev_content;
        }
    } else {
        return $content;
    }
}
add_filter('the_content', 'wp_momento_content_filter');
#add_filter('single_post_title', 'prd_display_post_revisions');
#add_filter('the_title', 'prd_display_post_revisions');