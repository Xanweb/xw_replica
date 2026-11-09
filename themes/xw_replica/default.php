<?php
defined('C5_EXECUTE') or die('Access Denied.');

$this->inc('elements/header.php');
$as = new Area('Page Header');
$c = Page::getCurrentPage();
$blocks = $as->getTotalBlocksInArea($c);
$addMargin = $blocks == 0 || $c->isEditMode();
?>

    <main>
        <?php
        $as->display($c);
        ?>
        <div class="container <?php echo ($addMargin) ? 'mtop' : ''; ?>">
            <div class="row">
                <?php
                $a = new Area('Main');
                $a->enableGridContainer();
                $a->display($c);
                ?>
            </div>
        </div>
        <?php
        $a = new Area('Page Footer');
        $a->enableGridContainer();
        $a->display($c);
        ?>
    </main>

<?php
$this->inc('elements/alternativ-footer.php');
