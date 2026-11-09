<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Package\XwReplica\Block\XwSimpleAccordion\Controller $controller
 * @var string $semantic
 * @var array $items
 * @var string $uniqID
 */
if ($items === []) {
    if ($controller->isInEditMode()) {
        $loc = Localization::getInstance();
        $loc->pushActiveContext(Localization::CONTEXT_UI); ?>
        <div class="col-sm-12">
            <div class="alert alert-info"><?php echo t('You did not add any items to the accordion.') ?></div>
        </div>
        <?php
        $loc->popActiveContext();
    }

    return;
}
?>

<div class="rp-simple-accordion">
    <?php  foreach ($items as $item) {?>
        <div class="rp-simple-accordion__item rp-simple-accordion--<?php echo $item['state'] !== '' ? $item['state'] : 'closed' ?>">
            <div class="rp-simple-accordion__button js-rp-simple-accordion">
                <?php echo \HtmlObject\Element::create(
                    $semantic,
                    $item['title'],
                    ['class' => 'rp-simple-accordion__title my-0']
                ) ?>
            </div>
            <div class="rp-simple-accordion__description">
                <?php echo $item['description']; ?>
            </div>
        </div>
    <?php  } ?>
</div>
