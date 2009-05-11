<?php
  /* 
     Copyright 2009 Ranveer Kunal  (email : ranveerkunal@gmail.com)

     This program is free software; you can redistribute it and/or modify
     it under the terms of the GNU General Public License as published by
     the Free Software Foundation; either version 2 of the License, or
     (at your option) any later version.

     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.

     You should have received a copy of the GNU General Public License
     along with this program; if not, write to the Free Software
     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  */
  /*
    Plugin Name: Show Author
    Plugin URI: http://www.randomgyan.org
    Description: This plugin attaches author information at the end of the posts.
    Version: 1.0
    Author: Ranveer Kunal
    Author URI: http://www.ranveerkunal.com
  */

function install_ShowAuthor() {
  if (get_option('sa_add_to_content') == '') {
    update_option('sa_add_to_content', 'yes');
  }
  if (get_option('sa_add_to_author_page') == '') {
    update_option('sa_add_to_author_page', 'yes');
  }
}

// Adds the author info to the content.
function sa_add_author_info($content) {
  ob_start();
  the_author_posts_link();
  $author_post_link = ob_get_contents();
  ob_end_clean();

  // If its the main page or single post page.
  if (get_option('sa_add_to_content') == 'yes'
      && (is_home() || is_single())) {
    return $content.'</br><p>Author: '.$author_post_link.'</p>';
  }

  return $content;
}

function sa_author_info_card($curauth) {
  $user = $curauth->ID;
  ob_start();
  if(userphoto_exists($user)) {
    userphoto($user);
  } else {
    echo get_avatar($user, 96);
  }
  $author_photo = ob_get_contents();
  ob_end_clean();
  return ""
    ."<div>$author_photo</div>"
    ."<div>"
    ."     <ul>"
    ."         <li>Homepage: <a href=$curauth->user_url>$curauth->user_url</a></li>"
    ."         <li>$curauth->user_description</li>"
    ."     </ul>"
    ."</div>"
    ."</br>";
}

function widget_author_info_card($args) {
  if(get_query_var('author_name')) :
    $curauth = get_userdatabylogin(get_query_var('author_name'));
  else :
    $curauth = get_userdata(get_query_var('author'));
  endif;

  extract($args);
  echo $before_widget.$before_title;

  // If its the main page or single post page.
  if (get_option('sa_add_to_author_page') == 'yes' && is_author()) {
    $title = $curauth->display_name;
    echo $title.$after_title;
    echo sa_author_info_card($curauth);
  } else {
    $title = "List of Authors";
    echo $title.$after_title;
    ob_start();
    wp_list_authors();
    $author_list = ob_get_contents();
    ob_end_clean();
    echo "<ul>$author_list</ul>";
  }

  echo $after_widget;
}

function widget_author_info_card_init() {
  if(function_exists('register_sidebar_widget')) {
    wp_register_sidebar_widget('AI', 'Author Info', 'widget_author_info_card');
  }
}

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
  install_ShowAuthor();
}

if (get_option('sa_add_to_content') == 'yes') {
  add_filter('the_content', 'sa_add_author_info', 0);
}

if (get_option('sa_add_to_author_page') == 'yes') {
  add_action('plugins_loaded', 'widget_author_info_card_init');
}

?>
