<?php
  $anc = apt_category_id();
?>
<div id="sidebar">
  <div class="side_nav">
    <h2>
      お知らせ
    </h2>
    <ul class="sub_navi">
      <?php
        wp_list_pages(
          array(
          'include' => get_page_by_path('news')->ID,
          'title_li' => 0
        ));
      ?>
      <?php
        wp_list_categories(
          array(
          'title_li' => false,
          'hide_empty' => true,
          'current_category' => $cat_id
        ));
      ?>
    </ul>
  </div>
  <?php
    get_template_part('sidebar-common');
  ?>
</div><!-- end #sidebar -->
