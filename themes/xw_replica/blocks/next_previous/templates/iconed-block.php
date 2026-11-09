<?php defined('C5_EXECUTE') or die('Access Denied.');

if (!$previousLinkURL && !$nextLinkURL && !$parentLabel) {
    return false;
}
?>
<div class="block-sidebar-wrapped">
    <div class="ccm-block-next-previous-wrapper row icons">
        <?php
        if ($previousLinkURL && $previousLabel) {
            ?>
            <div class="ccm-block-next-previous-header col-sm-5">
                <a href="<?php echo $previousLinkURL; ?>"><h5><i
                                class="fa fa-long-arrow-left"></i> <?php echo $previousLabel; ?></h5></a>
            </div>
            <?php
        } ?>
        <?php
        if ($nextLinkURL && $nextLabel) {
            ?>
            <div class="ccm-block-next-previous-header col-sm-5 col-sm-offset-2 text-center">
                <a href="<?php echo $nextLinkURL; ?>"><h5><?php echo $nextLabel; ?> <i
                                class="fa fa-long-arrow-right"></i></h5></a>
            </div>
            <?php
        }
        ?>
    </div>
</div>
