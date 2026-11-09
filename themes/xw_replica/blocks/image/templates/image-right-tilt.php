<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\File\File $f
 * @var Concrete\Core\File\File $foS
 * @var int $maxWidth
 * @var int $maxHeight
 * @var int $cropImage
 * @var string $altText
 * @var string $linkURL
 * @var bool $openLinkInNewWindow
 * @var array $imgPaths
 */
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
if (is_object($f) && $f->getFileID()) {
    echo '<div class="image-right-tilt">';
    if ($f->getTypeObject()->isSVG()) {
        $tag = new \HtmlObject\Image();
        $tag->src($f->getRelativePath());
        if ($maxWidth > 0) {
            $tag->width($maxWidth);
        }
        if ($maxHeight > 0) {
            $tag->height($maxHeight);
        }
        $tag->addClass('ccm-svg');
    } elseif ($maxWidth > 0 || $maxHeight > 0) {
        $im = $app->make('helper/image');
        $thumb = $im->getThumbnail($f, $maxWidth, $maxHeight, $cropImage);

        $tag = new \HtmlObject\Image();
        $tag->src($thumb->src)->width($thumb->width)->height($thumb->height);
    } else {
        $image = $app->make('html/image', ['f' => $f]);
        $tag = $image->getTag();
    }

    $tag->addClass('ccm-image-block img-responsive bID-' . $bID);

    if ($altText) {
        $tag->alt(h($altText));
    } else {
        $tag->alt('');
    }

    if ($title) {
        $tag->title(h($title));
    }

    if ($linkURL) {
        echo '<a href="' . $linkURL . '" ' . ($openLinkInNewWindow ? 'target="_blank" rel="noopener noreferrer"' : '') . '>';
    }

    // add data attributes for hover effect
    if (is_object($f) && is_object($foS)) {
        if (($maxWidth > 0 || $maxHeight > 0) && !$f->getTypeObject()->isSVG() && !$foS->getTypeObject()->isSVG()) {
            $tag->addClass('ccm-image-block-hover');
            $tag->setAttribute('data-default-src', $imgPaths['default']);
            $tag->setAttribute('data-hover-src', $imgPaths['hover']);
        }
    }

    echo $tag;
    if ($linkURL) {
        echo '</a>';
    }
    echo '</div>';
} elseif ($c->isEditMode()) { ?>
    <div class="ccm-edit-mode-disabled-item"><?php echo t('Empty Image Block.'); ?></div>
    <?php
}
