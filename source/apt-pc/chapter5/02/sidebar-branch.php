    <div id="sidebar">
      <div class="side_nav">
        <h2>営業所</h2>
        <ul class="sub_navi">
<?php wp_list_pages(array('include' => get_page_by_path('office')->ID, 'title_li' => 0)); ?>
<?php
  wp_list_pages(array('post_type' => 'branch', 'title_li' => 0));
?>
        </ul>
      </div>
<?php get_template_part('sidebar-common'); ?>
    </div><!-- end #sidebar -->
