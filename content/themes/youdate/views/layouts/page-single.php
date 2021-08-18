<?php

/* @var $content string */

?>
<?php $this->beginContent('@theme/views/layouts/base.php'); ?>
<div class="page">
    <div class="page-content">
        <div class="container">
            <?php echo $content ?>
        </div>
    </div>
</div>
<?php $this->endContent(); ?>
