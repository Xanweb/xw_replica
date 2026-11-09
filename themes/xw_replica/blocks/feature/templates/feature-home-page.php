<?php defined('C5_EXECUTE') or die('Access Denied.');
$title = h($title);
if ($linkURL) {
    $title = '<a href="' . $linkURL . '">' . $title . '</a>';
}
?>
<div class="feature-home-page">
    <div class="ccm-block-feature-item">
        <?php if ($title) {
    ?>
            <h4><i class="fa fa-<?php echo $icon; ?>"></i> <?php echo $title; ?></h4>
            <?php
} ?>
        <?php
        if ($paragraph) {
            echo $paragraph;
        }
        ?>
    </div>
</div>
