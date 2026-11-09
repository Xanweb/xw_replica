<?php

namespace Xanweb\RpHtmlHelper\Service\Form;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\View\View;
use Concrete\Core\Form\Service\Form;
use Xanweb\Replica\Module\Module;

class IosTogglerWidget
{
    /**
     * @var Form
     */
    protected Form $form;

    public function __construct(Form $form)
    {
        $this->form = $form;

        $al = AssetList::getInstance();
        $al->register(
            'css',
            'dashboard/ios/toggler',
            'vendor/xanweb/rp_html_helpers/css/dashboard/ios-toggle-button.css',
            ['minify' => false],
            Module::pkg()
        );
    }

    /**
     * Generates a Toggler.
     *
     * @param string $key The name/id of the element. It should end with '[]' if it's to return an array on submit.
     * @param string $value String value sent to server, if checkbox is checked, on submit
     * @param string $isChecked "Checked" value (subject to be overridden by $_REQUEST). Checkbox is checked if value is true (string). Note that 'false' (string) evaluates to true (boolean)!
     * @param array $miscFields additional fields appended to the element (a hash array of attributes name => value), possibly including 'class'
     */
    public function output($key, $value, $isChecked = false, $miscFields = []): void
    {
        $view = View::getInstance();
        $view->requireAsset('css', 'dashboard/ios/toggler');

        $miscFields['class'] = trim(($miscFields['class'] ?? '') . ' ios__toggler ios__toggler-round-flat');

        echo '<div>'
            . $this->form->checkbox($key, $value, $isChecked, $miscFields) . $this->form->label($key, '')
            . '</div>';
    }
}
