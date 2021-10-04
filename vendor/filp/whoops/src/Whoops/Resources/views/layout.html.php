<?php
/**
* Layout template file for Whoops's pretty error output.
*/
?>
<!DOCTYPE html><?php echo $preface; ?>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title><?php echo $tpl->escape($page_title) ?></title>

    <style><?php echo $stylesheet ?></style>
<<<<<<< HEAD
<<<<<<< HEAD
    <style><?php echo $prismCss ?></style>
=======
>>>>>>> 22c0e54 (table changes)
=======
    <style><?php echo $prismCss ?></style>
>>>>>>> f330c64 (optimization in progress)
  </head>
  <body>

    <div class="Whoops container">
      <div class="stack-container">

        <?php $tpl->render($panel_left_outer) ?>

        <?php $tpl->render($panel_details_outer) ?>

      </div>
    </div>

<<<<<<< HEAD
<<<<<<< HEAD
    <script data-manual><?php echo $prismJs ?></script>
=======
    <script><?php echo $prettify ?></script>
>>>>>>> 22c0e54 (table changes)
=======
    <script data-manual><?php echo $prismJs ?></script>
>>>>>>> f330c64 (optimization in progress)
    <script><?php echo $zepto ?></script>
    <script><?php echo $clipboard ?></script>
    <script><?php echo $javascript ?></script>
  </body>
</html>
