<?php

$get_all_items = get_custome_cat();

ob_start();

?>

      <ul class="rpress-category-lists">
      <?php
      foreach ( $get_all_items as $key => $get_all_item ) : ?>
        <li class="rpress-category-item ">
          <a href="#<?php echo $get_all_item->category_name; ?>" data-id="<?php echo $get_all_item->cat_id; ?>" class="rpress-category-link nav-scroller-item <?php echo $color; ?>"><?php echo $get_all_item->category_name; ?></a>
        </li>
      <?php endforeach; ?>
      </ul>
   
<?php
echo ob_get_clean();
