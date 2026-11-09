<?php

namespace Concrete\Package\XwReplica\Block\XwSimpleAccordion;

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Xanweb\Replica\Block\ItemListBlockController;
use Xanweb\Replica\Block\Traits\BlockControllerTrait;

class Controller extends ItemListBlockController
{
    use BlockControllerTrait;

    protected $btTable = 'btXwSimpleAccordion';

    protected $btInterfaceWidth = '700';

    protected $btWrapperClass = 'ccm-ui';

    protected $btInterfaceHeight = '465';

    public function getBlockTypeDescription(): string
    {
        return t('Add Collapsible Content to your Site');
    }

    public function getBlockTypeName(): string
    {
        return t('Simple Accordion');
    }

    public function add(): void
    {
        $this->addEditCommons();
        $this->set('rows', []);
    }


    public function edit(): void
    {
        $this->addEditCommons();
        $items = $this->getItems(['sortOrder' => 'ASC']);

        foreach ($items as &$row) {
            $row['description'] = LinkAbstractor::translateFromEditMode($row['description']);
        }

        $this->set('rows', $items);
    }

    public function view(): void
    {
        $items = $this->getItems(['sortOrder' => 'ASC']);

        foreach ($items as &$item) {
            $item['description'] = LinkAbstractor::translateFrom($item['description']);
        }

        $this->set('items', $items);
    }

    public function save($args): void
    {
        parent::save($args);

        $blockObject = $this->getBlockObject();
        if (is_object($blockObject)) {
            $blockObject->setCustomTemplate($args['framework']);
        }
    }

    protected function addEditCommons(): void
    {
        $this->set('uniqID', $this->getUniqueId());
        $this->set('uih', $this->app['helper/concrete/ui']);
        $this->app['editor']->requireEditorAssets();
        $this->requireAsset('rp/xw/v-item-list');
        $this->set('framework', $this->get('framework'));
        $this->set('semantic', $this->get('semantic'));
    }

    /**
     * {@inheritdoc}
     *
     * @see ItemListBlockController::getItemListTable()
     */
    protected function getItemListTable(): string
    {
        return 'btXwSimpleAccordionItem';
    }

    protected function _validate(?array $args, ErrorList $e): void
    {
        if (!isset($args['sortOrder'])) {
            $e->add(t('Please add at least one accordion entry.'));
        }
    }

    protected function validateItem(int $itemNbr, array $item, ErrorList $e): void
    {
        if (trim($item['title']) === '') {
            $e->add(t('Accordion') . " #{$itemNbr} " . t('The field %s is required.', t('Title')));
        }
    }
}
