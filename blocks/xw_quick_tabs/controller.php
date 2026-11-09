<?php
namespace Concrete\Package\XwReplica\Block\XwQuickTabs;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Exception;

class Controller extends BlockController
{
    /**
     * @var string
     */
    const OPENCLOSE_OPEN = 'open';

    /**
     * @var string
     */
    const OPENCLOSE_CLOSE = 'close';

    public $openclose;

    public $tabTitle;

    public $semantic;

    public $tabHandle;

    protected $btTable = 'btXwQuickTabs';

    protected $btWrapperClass = 'ccm-ui';

    protected $btInterfaceHeight = 365;

    protected $btInterfaceWidth = 400;

    public function getBlockTypeDescription()
    {
        return t('Add Tabs to the Page');
    }

    public function getBlockTypeName()
    {
        return t('Quick Tabs');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::ignorePageThemeGridFrameworkContainer()
     */
    public function ignorePageThemeGridFrameworkContainer()
    {
        $c = $this->request->getCurrentPage();

        return !($c && !$c->isError() && $c->isEditMode());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::registerViewAssets()
     */
    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('javascript', 'jquery');
    }

    public function view()
    {
        $this->set('closeOption', static::OPENCLOSE_CLOSE);
    }

    public function add()
    {
        $this->set('openclose', '');
        $this->set('tabTitle', '');
        $this->set('semantic', '');
        $this->set('opencloseOptions', array('' => '') + $this->getOpencloseOptions());
        $this->set('tabHandle', '');
        $this->addOrEdit();
    }

    public function edit()
    {
        $this->set('opencloseOptions', $this->getOpencloseOptions());

        $this->addOrEdit();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::validate()
     */
    public function validate($args)
    {
        $e = parent::validate($args);

        $this->normalizeArgs($args, $e);

        return  $e;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::save()
     */
    public function save($args)
    {
        $e = $this->app->make('error');
        $result = $this->normalizeArgs($args, $e);
        if (!$result) {
            throw new Exception(implode("\n", $e->getList()));
        }

        parent::save($result);
    }

    protected function addOrEdit()
    {
        $this->set('closeOptionJSON', json_encode(static::OPENCLOSE_CLOSE));
    }

    /**
     * @param mixed $args
     * @param ErrorList $errors
     *
     * @return array|bool error object in case of errors
     */
    protected function normalizeArgs($args, ErrorList $errors)
    {
        if (!is_array($args)) {
            $args = array();
        }

        $args += array(
            'openclose' => '',
            'tabTitle' => '',
            'semantic' => '',
            'tabHandle' => '',
        );

        $result = array('openclose' => $args['openclose']);
        $opencloseOptions = $this->getOpencloseOptions();
        if ($result['openclose'] === '' || !isset($opencloseOptions[$result['openclose']])) {
            $errors->add(t('Is this the Opening or Closing Block?'));
        } elseif ($result['openclose'] === static::OPENCLOSE_OPEN) {
            $result['tabTitle'] = is_string($args['tabTitle']) ? $args['tabTitle'] : '';
            if ($result['tabTitle'] === '') {
                $errors->add(t('Please specify the Tag Title.'));
            }

            $result['semantic'] = is_string($args['semantic']) ? $args['semantic'] : '';
            if ($result['semantic'] === '') {
                $errors->add(t('Please specify the Semantic Tag for the Tab Title.'));
            }

            $result['tabHandle'] = is_string($args['tabHandle']) ? trim($args['tabHandle']) : '';
            $invalidChars = ':#|';
            if ($result['tabHandle'] !== '' && strpbrk($result['tabHandle'], $invalidChars) !== false) {
                $errors->add(
                    t(
                        "Tab Handle can't contain these characters: %s",
                        '"' . implode('", "', str_split($invalidChars, 1)) . '"'
                    )
                );
            }
        }

        return $errors->has() ? false : $result;
    }

    /**
     * Get the list of allowed values for the openclose field.
     *
     * @return array
     */
    protected function getOpencloseOptions()
    {
        return array(
            static::OPENCLOSE_OPEN => t('Open'),
            static::OPENCLOSE_CLOSE => t('Close'),
        );
    }
}
