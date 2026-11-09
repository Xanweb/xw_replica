<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Package\XwReplica\Block\XwSimpleAccordion\Controller $controller
 * @var string $semantic
 * @var array $items
 * @var string $uniqID
 * @var int $bID
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
<div class="rp-simple-accordion rp-simple-accordion--bootstrap">
    <div class="accordion <?php echo $additionalClasses ?? '' ?>" role="tablist">
        <?php foreach ($items as $i => $item) {
            ?>
            <div class="accordion-item rounded-0 mb-2">
                <div class="accordion-header">
                    <a href="#" id="<?php echo "heading{$bID}-{$i}" ?>" class="accordion-button <?php echo $item['state'] === 'open' ? '' : 'collapsed' ?>"
                       data-bs-toggle="collapse" data-bs-target="#<?php echo "collapse{$bID}-{$i}" ?>" role="tab"
                       aria-expanded="<?php echo $item['state'] === 'open' ? 'true' : 'false' ?>"
                       aria-controls="<?php echo "collapse{$bID}-{$i}" ?>">
                        <?php echo \HtmlObject\Element::create($semantic, $item['title'], ['class' => 'rp-simple-accordion__title rp-simple-accordion__title--bootstrap my-0']) ?>
                    </a>
                </div>
                <div id="<?php echo "collapse{$bID}-{$i}" ?>" class="accordion-collapse collapse <?php echo $item['state'] === 'open' ? 'show' : '' ?>"
                     role="tabpanel" aria-labelledby="<?php echo "heading{$bID}-{$i}" ?>">
                    <div class="accordion-body">
                        <?php echo $item['description'] ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
