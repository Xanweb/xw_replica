<?php
namespace Concrete\Package\XwReplica;

use Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypesBaseRoutine as CoreImportPageTypesBaseRoutine;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Package\Package;
use Concrete\Package\XwReplica\Theme\XwReplica\PageTheme;
use Xanweb\Replica\Backup\ContentImporter\Importer\Routine\ImportPageTypesBaseRoutine;
use Xanweb\Replica\Module\Module;

class Controller extends Package implements ProviderInterface
{
    protected $pkgHandle = 'xw_replica';
    protected $appVersionRequired = '9.3.0';
    protected $pkgVersion = '2.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Xanweb/Replica' => 'Xanweb\Replica',
        'vendor/xanweb/rp_common' => 'Xanweb\RpCommon',
        'vendor/xanweb/rp_module' => 'Xanweb\RpModule',
        'vendor/xanweb/rp_helpers' => 'Xanweb\RpHelpers',
        'vendor/xanweb/rp_html_helpers/src' => 'Xanweb\RpHtmlHelper',
        'vendor/xanweb/rp_js_localization' => 'Xanweb\RpJsLocalization',
        'vendor/xanweb/rp_request' => 'Xanweb\RpRequest',
        'vendor/xanweb/rp_v_item_list/src' => 'Xanweb\RpVueItemList'
    ];
    protected $pkgAllowsFullContentSwap = true;

    public function getPackageName()
    {
        return t('Replica Theme');
    }

    public function getPackageDescription()
    {
        return t('Replica Theme, by Xanweb. Support: %s', 'https://github.com/Xanweb/xw_replica/issues');
    }

    public function install()
    {
        $pkg = parent::install();
        $this->app->bind(CoreImportPageTypesBaseRoutine::class, ImportPageTypesBaseRoutine::class);
        $theme = PageTheme::add('xw_replica', $pkg);
        $theme->applyToSite();

        $this->installXml();

        $this->installComponents($pkg);

        return $pkg;
    }

    public function on_start()
    {
        Module::boot();
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installComponents($this->getPackageEntity());
    }

    /**
     * Install or Update package components.
     *
     * @param \Concrete\Core\Entity\Package $pkg
     */
    private function installComponents($pkg)
    {
        $this->installBlockTypes(['xw_simple_accordion', 'xw_quick_tabs'], $pkg);
    }

    /**
     * Install/update data from install XML file.
     */
    private function installXml()
    {
        $this->installContentFile('install.xml');
    }

    private function installBlockTypes(array $blockTypes, $pkg)
    {
        foreach ($blockTypes as $handle) {
            $bt = BlockType::getByHandle($handle);
            if (!is_object($bt)) {
                $bt = BlockType::installBlockType($handle, $pkg);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDrivers()
    {
        return [];
    }
}
