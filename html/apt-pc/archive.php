<?php get_header(); ?>
  <div id="container">
    <div id="main" role="main">
      <div id="content">
        <h1 class="page_ttl">
          <?php
            single_cat_title();
          ?>
        </h1>
        <?php
          if(have_posts()) :
            while(have_posts()) :
              the_post();
        ?>
        <h1 class="page_ttl">
          <?php the_title(); ?>
        </h1>
          <div class="clearfix">
            <span class="date">
                <?php the_date('Y年m月d日'); ?>
            </span>
          </div>
        <?php the_content(); ?>
        <?php
            endwhile;
          endif;
          if(class_exists('WP_SiteManager_page_navi')) :
            WP_SiteManager_page_navi::page_navi();
          endif;
        ?>
        <?php
          get_template_part('page_top_link');
        ?>
      </div><!-- end #content -->
    </div><!-- end #main -->
<?php get_sidebar('post'); ?>
  </div><!-- end #container -->
<?php get_footer(); ?>