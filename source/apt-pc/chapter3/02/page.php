<?php get_header(); ?>
  <div id="container">
    <div id="main" role="main">
      <div id="content">
<?php
  if (have_posts()) :
    while (have_posts()) :
      the_post();
?>
        <h1 class="page_ttl"><?php the_title(); ?></h1>
        <?php the_content(); ?>
<?php
    endwhile;
  endif;
?>
        <div class="clearfix" id="page_top_link"><a href="#wrap">このページの先頭へ</a></div>
      </div><!-- end #content -->
    </div><!-- end #main -->
<?php get_sidebar(); ?>
  </div><!-- end #container -->
<?php get_footer(); ?>
