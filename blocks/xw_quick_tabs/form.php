<?php   defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Package\XwReplica\Block\XwQuickTabs\Controller $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $openclose
 * @var array $opencloseOptions
 * @var string $semantic
 * @var array $semanticOptions
 * @var string $tabTitle
 * @var string $closeOptionJSON
 * @var string $tabHandle
 */

?>

<div class="form-group">
    <?php echo $form->label('openclose', t('Is this the Opening or Closing Block?')); ?>
    <?php echo $form->select('openclose', $opencloseOptions, $openclose, array('required' => 'required')); ?>
</div>

<div class="form-group<?php echo $openclose === 'close' ? ' hide' : '' ?>">
    <?php echo $form->label('tabTitle', t('Tab Title')); ?>
    <?php echo $form->text('tabTitle', $tabTitle); ?>
</div>

<div class="form-group<?php echo $openclose === 'close' ? ' hide' : '' ?>">
    <?php echo $form->label('semantic', t('Semantic Tag for the Tab Title')); ?>
    <?php echo $form->select('semantic', ['h2' => t('Heading %d', 2), 'h3' => t('Heading %d', 3), 'h4' => t('Heading %d', 4), 'p' => t('Paragraph'), 'span' => tc('HTML Element', 'Span')], $semantic); ?>
</div>

<div class="form-group<?php echo $openclose === 'close' ? ' hide' : '' ?>">
    <?php echo $form->label('tabHandle', t('Tab Handle')); ?>
    <?php echo $form->text('tabHandle', $tabHandle, array('maxlength' => 255)); ?>
</div>

<script>
    $(document).ready(function() {
        $('#openclose')
            .on('change', function() {
                $('#tabTitle,#semantic,#tabHandle').closest('.form-group').toggleClass('hide', this.value === <?php echo $closeOptionJSON ?>);
            })
            .trigger('change')
        ;
    });
</script>
