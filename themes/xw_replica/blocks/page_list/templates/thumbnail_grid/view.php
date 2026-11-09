<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Utility\Service\Text $th
 * @var \Concrete\Core\Localization\Service\Date $dh
 * @var bool $displayThumbnail
 * @var bool $includeEntryText
 * @var bool $useButtonForLink
 * @var string $buttonLinkText
 * @var string $noResultsMessage
 * @var bool $showPagination
 */
$app = Concrete\Core\Support\Facade\Facade::getFacadeApplication();
$th = $app->make('helper/text');
$dh = $app->make('helper/date');
$c = Page::getCurrentPage();
?>
    <div class="ccm-block-page-list-thumbnail-grid-wrapper">
        <?php if (isset($pageListTitle) && $pageListTitle): ?>
            <div class="ccm-block-page-list-header">
                <h5><?php echo h($pageListTitle); ?></h5>
            </div>
        <?php endif; ?>
        <?php if (isset($rssUrl) && $rssUrl) {
    ?>
            <a href="<?php echo $rssUrl; ?>" target="_blank" class="ccm-block-page-list-rss-feed">
                <i class="fa fa-rss"></i>
            </a>
            <?php
} ?>
        <div class="ccm-block-page-list-pages">
            <?php foreach ($pages as $page):
                $title = $th->entities($page->getCollectionName());
                $url = $nh->getLinkToCollection($page);
                $target = ($page->getCollectionPointerExternalLink() != '' && $page->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $page->getAttribute('nav_target');
                $target = empty($target) ? '_self' : $target;
                $hoverLinkText = $title;
                $description = $page->getCollectionDescription();
                $description = $controller->truncateSummaries ? $th->wordSafeShortText($description, $controller->truncateChars) : $description;
                $description = $th->entities($description);
                $thumbnail = false;
                if ($displayThumbnail) {
                    $thumbnail = $page->getAttribute('thumbnail');
                }
                if (isset($includeEntryText) && is_object($thumbnail) && $includeEntryText) {
                    $entryClasses = 'ccm-block-page-list-page-entry-horizontal';
                }
                $date = $dh->formatDateTime($page->getCollectionDatePublic(), true);
                if ($useButtonForLink) {
                    $hoverLinkText = $buttonLinkText;
                }
                ?>
                <div class="ccm-block-page-list-page-entry-grid-item">
                    <div class="ccm-block-page-list-page-entry-grid-thumbnail">
                        <?php if (is_object($thumbnail)): ?>
                            <a href="<?php echo $url; ?>" target="<?php echo $target; ?>"><?php
                                $img = $app->make('html/image', ['f' => $thumbnail]);
                                $tag = $img->getTag();
                                $tag->addClass('img-responsive');
                                echo $tag;
                                ?>
                                <div class="ccm-block-page-list-page-entry-grid-thumbnail-hover">
                                    <div class="ccm-block-page-list-page-entry-grid-thumbnail-title-wrapper">
                                        <div class="ccm-block-page-list-page-entry-grid-thumbnail-title">
                                            <i class="ccm-block-page-list-page-entry-grid-thumbnail-icon"></i>
                                            <?php echo $hoverLinkText; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endif; ?>
                        <?php if (isset($includeName) && $includeName) {
                                    ?>
                            <div class="ccm-block-page-list-title">
                                <?php if (isset($useButtonForLink) && $useButtonForLink) {
                                        ?>
                                    <?php echo h($title); ?>
                                    <?php
                                    } else {
                                        ?>
                                    <a href="<?php echo h($url); ?>"
                                       target="<?php echo h($target); ?>"><?php echo h($title); ?></a>
                                    <?php
                                    } ?>
                            </div>
                            <?php
                                } ?>
                        <?php if (isset($includeDate) && $includeDate) {
                                    ?>
                            <div class="ccm-block-page-list-date"><?php echo h($date); ?></div>
                            <?php
                                } ?>
                        <?php if (isset($includeDescription) && $includeDescription) {
                                    ?>
                            <div class="ccm-block-page-list-description"><?php echo h($description); ?></div>
                            <?php
                                } ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($pages) == 0): ?>
            <div class="ccm-block-page-list-no-pages"><?php echo h($noResultsMessage); ?></div>
        <?php endif; ?>
    </div>
<?php if ($showPagination): ?>
    <?php echo $pagination; ?>
<?php endif; ?>

<?php if ($c->isEditMode() && $controller->isBlockEmpty()): ?>
    <div class="ccm-edit-mode-disabled-item"><?php echo t('Empty Page List Block.'); ?></div>
<?php endif; ?>