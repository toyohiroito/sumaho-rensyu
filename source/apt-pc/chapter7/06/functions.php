<?php

// カスタムメニュー
register_nav_menus(
  array(
    'place_pc_global' => 'PCグローバル',
    'place_sp_global' => 'SPグローバル',
    'place_pc_utility' => 'PCユーティリティ',
    'place_sp_utility' => 'SPユーティリティ',
  )   
);

// wp_nav_menuにslugのクラス属性を追加する。
function apt_slug_nav($css, $item) {
  if ($item->object == 'page') {
    $page = get_post($item->object_id);
    $css[] = 'menu-item-slug-' . esc_attr($page->post_name);
  }
  return $css;
}

// 最上位の固定ページ情報を取得する。
function apt_page_ancestor() {
  global $post;
  $anc = array_pop(get_post_ancestors($post));
  $obj = new stdClass;
  if ($anc) {
    $obj->ID = $anc;
    $obj->post_title = get_post($anc)->post_title;
  } else {
    $obj->ID = $post->ID;
    $obj->post_title = $post->post_title;
  }
  return $obj;
}

// カテゴリIDを取得する。
function apt_category_id($tax='category') {
  global $post;
  $cat_id = 0;
  if (is_single()) {
    $cat_info = get_the_terms($post->ID, $tax);
    if ($cat_info) {
      $cat_id = array_shift($cat_info)->term_id;
    }
  }
  return $cat_id;  
}

// カテゴリ情報を取得する。
function apt_category_info($tax='category') {
  global $post;
  $cat = get_the_terms($post->ID, $tax);
  $obj = new stdClass;
  if ($cat) {
    $cat = array_shift($cat);
    $obj->name = $cat->name;
    $obj->slug = $cat->slug;
  } else {
    $obj->name = '';
    $obj->slug = '';
  }
  return $obj;
}

// カスタム投稿タイプ・カスタム分類
add_action('init', 'register_post_type_and_taxonomy');
function register_post_type_and_taxonomy() {
  register_post_type(
    'branch',
    array(
      'labels' => array(
        'name' => '営業所情報',
        'add_new_item' => '営業所を追加',
        'edit_item' => '営業所の編集',
      ),
      'public' => true,
      'hierarchical' => true,
      'supports' => array(
        'title',
        'editor',
        'excerpt',
        'custom-fields',
        'thumbnail',
        'page-attributes',
      ),
    )
  );
  register_post_type(
    'tour',
    array(
      'labels' => array(
        'name' => 'ツアー情報',
        'add_new_item' => '新規ツアーを追加',
        'edit_item' => 'ツアーの編集',
      ),
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'excerpt',
        'custom-fields',
        'thumbnail',
      ),
    )
  );
  register_taxonomy(
    'area',
    'tour',
    array(
      'labels' => array(
        'name' => '地域',
        'add_new_item' => '地域を追加',
        'edit_item' => '地域の編集',
      ),
      'hierarchical' => true,
      'show_admin_column' => true,
    )
  );
}

// wp_list_pagesのクラス属性を変更する。
function apt_add_current($output) {
  global $post;
  $oid = "page-item-{$post->ID}";
  $cid = "$oid current_page_item";
  $output = preg_replace("/$oid/", $cid, $output);
  return $output;
}

// アイキャッチ画像を利用できるようにします。
add_theme_support('post-thumbnails');
set_post_thumbnail_size(208, 138, true);

// メディアのサイズを追加します。
add_image_size('main_image', 370);
add_image_size('tour-archive', 280);
add_image_size('sub_image', 150);

// ツアー情報のパンくずナビを修正します。
function apt_bread_crumb($arr) {
  if (is_tax('area') && count($arr) == 2) {
    $arr[2] = $arr[1];
    $arr[1] = array('title' => 'ツアー情報', 'link' => get_permalink(get_page_by_path('tour-info')));
  }
  return $arr;
}

// 数字を円貨幣のフォーマットに整形します。
function apt_convert_yen($yen) {
  $yen = mb_convert_kana($yen, 'n', 'UTF-8');
  $yen = preg_replace('/[^0-9]/', '', $yen);
  if (is_numeric($yen)) {
    $yen = number_format($yen) . '円';
    return $yen;
  }
}

// タイトルタグのテキストを出力します。
function apt_simple_title() {
  if (!is_front_page()) {
    echo trim(wp_title('', false)) . " | ";
  } 
  bloginfo('name');
}

// サイトIDのタグをトップページとそれ以外で切り替えます。
function apt_site_id() {
  if (is_front_page()) {
    echo "h1";
  } else {
    echo "div";
  }
}

// 検索ワードが未入力または0の場合にsearch.phpをテンプレートとして使用する
function apt_search_redirect() {
  global $wp_query;
  $wp_query->is_search = true;
  $wp_query->is_home = false;
  if (file_exists(TEMPLATEPATH . '/search.php')) {
    include(TEMPLATEPATH . '/search.php');
  }
  exit;
}

if (isset($_GET['s']) && $_GET['s'] == false) {             
  add_action('template_redirect', 'apt_search_redirect');                                     
}

// wp_nav_menuにcurrentのクラス属性を追加します。
function apt_current_nav($css, $item) {
  if (is_search()) {
    return $css;
  }
  if ($item->title == 'ツアー情報') {
    if (get_post_type() == 'tour' || is_tax('area')) {
      $css[] = 'current-page-ancestor';
    }
  } elseif ($item->title == '営業所') {
    if (get_post_type() == 'branch') {
      $css[] = 'current-page-ancestor';
    }
  }
  return $css;
}

// カテゴリーイメージで使用するファイル名を出力します。
function apt_category_image() {
  global $post;
  $cat_img = 'def';
  if (is_page()) {
    if (in_array($post->post_name, array('about', 'csr', 'tour-info', 'office' ))) {
      $cat_img = $post->post_name;
    } else {
      $anc = array_pop(get_post_ancestors($post));
      if ($anc) {
        $anc = get_page($anc);
        if (in_array($anc->post_name, array('about', 'csr'))) {
          $cat_img = $anc->post_name;
        }
      }
    }
  }
  if (get_post_type() == 'branch') {
    $cat_img = 'office';
  }
  if (get_post_type() == 'tour' || is_tax('area')) {
    $cat_img = 'tour-info';
  }
  if (is_search()) {
    $cat_img = 'def';
  }
  $cat_img = 'img_cat_' . $cat_img . '.png';
  return $cat_img;
}
