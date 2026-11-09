<?php defined('C5_EXECUTE') or die('Access Denied.');

$this->inc('elements/header_top.php');

$as = new GlobalArea('Header Search');
$blocks = $as->getTotalBlocksInArea();
$displayThirdColumn = $blocks > 0 || $c->isEditMode();
?>

<header>
    <div class="container">
        <div class="row">
            <div class="col-sm-3 col-6 header-site-title" id="logo-area">
                <?php
                $a = new GlobalArea('Header Site Title');
                $a->display();
                ?>
            </div>
            <div class="col-sm-7 col-6 responsive-navigation">
                <?php
                $a = new GlobalArea('Header Navigation');
                $a->display();
                ?>
            </div>
            <?php
            if ($displayThirdColumn) {
                ?>
                <div class="col-sm-2 col-12">
                    <?php $as->display(); ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</header>
