<?php

namespace Xanweb\Replica\Block\Traits;

use Concrete\Core\Block\Block;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker as Permissions;
use Concrete\Core\Session\SessionValidator;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Illuminate\Support\Str;
use Xanweb\RpRequest\Page as RequestPage;
use Xanweb\RpRequest\User as RequestUser;

/**
 * Trait BlockControllerTrait.
 *
 * @property \Concrete\Core\Application\Application $app
 * @property \Concrete\Core\Http\Request $request
 * @property Block $block
 * @property int $bID
 */
trait BlockControllerTrait
{
    /**
     * @var Page
     */
    private $blockPage;

    /**
     * @var Permissions
     */
    private $permissions;

    /**
     * @var string
     */
    private $realIdentifier;

    /**
     * @var string
     */
    private $uniqID;

    /**
     * @var string
     */
    private $locale;

    /**
     * Get Uniq Identifier for Block.
     */
    public function getUniqueId(): string
    {
        if (!$this->uniqID) {
            $prefix = strtolower($this->getRealIdentifier());
            $this->uniqID = $prefix . '_' . Str::random(3);
        }

        return $this->uniqID;
    }

    /**
     * Get Uniq Identifier for Block.
     */
    public function getRealIdentifier(): string
    {
        if (!$this->realIdentifier) {
            $b = $this->getBlockObject(); // @var Block $b
            if (is_object($b) && $proxyBlock = $b->getProxyBlock()) {
                $this->realIdentifier = (string) $proxyBlock->getController()->getIdentifier();
            } else {
                $this->realIdentifier = (string) $this->getIdentifier();
            }
        }

        return $this->realIdentifier;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::isValidControllerTask()
     */
    public function isValidControllerTask($method, $parameters = [])
    {
        if (parent::isValidControllerTask($method, $parameters)) {
            $bID = array_pop($parameters);
            if ((new Numbers())->integer($bID, 1, PHP_INT_MAX) && (int) $this->bID === (int) $bID) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether we are in Edit Mode.
     *
     * @return bool
     */
    public function isInEditMode(): bool
    {
        $c = $this->getPageObject();

        return ($c !== null) ? $c->isEditMode() : RequestPage::isEditMode();
    }

    /**
     * Check if active user can edit block.
     */
    public function userCanEditBlock(): bool
    {
        $bp = $this->getPermissionObject();

        return ($bp !== null) ? $bp->canWrite() : false;
    }

    public function getPermissionObject(): ?Permissions
    {
        if (!is_object($this->block)) {
            return null;
        }

        return $this->permissions ?? $this->permissions = new Permissions($this->block);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getSets()
     */
    public function getSets()
    {
        $sets = parent::getSets();

        $validator = $this->app->make(SessionValidator::class);
        if ($validator->hasActiveSession()) {
            $blockIdentifier = $this->getRealIdentifier();
            $sessionBag = $this->app->make('session')->getFlashBag();
            if ($sessionBag->has('block_message_' . $blockIdentifier)) {
                $messages = $sessionBag->get('block_message_' . $blockIdentifier);
                foreach ($messages as [$key, $value, $isHTML]) {
                    $sets[$key] = $value;
                    $sets[$key . 'IsHTML'] = $isHTML;
                }
            }
        }

        return $sets;
    }

    public function flash(string $key, string $value, bool $isHTML = false): void
    {
        $session = $this->app->make('session');
        $session->getFlashBag()->add('block_message_' . $this->getRealIdentifier(), [$key, $value, $isHTML]);
    }

    /**
     * Get Block Related Page Language.
     *
     * @return string
     */
    public function getPageLanguage(): string
    {
        return \current(\explode('_', $this->getPageLocale()));
    }

    /**
     * Get Block Related Page Locale.
     *
     * @return string
     */
    public function getPageLocale(): string
    {
        if (!$this->locale && ($page = $this->getPageObject()) !== null) {
            $section = Section::getBySectionOfSite($page);
            if (is_object($section)) {
                $this->locale = $section->getLocale();
            }
        }

        return $this->locale ?? $this->locale = RequestPage::getLocale();
    }

    /**
     * Get Block Related Page Object.
     *
     * @return Page
     */
    public function getPageObject(): ?Page
    {
        if (!$this->blockPage) {
            if ($this->isEditedWithinStack()) {
                $this->blockPage = $this->getCollectionObject() ?: null;
            } else {
                $c = $this->request->getCurrentPage();
                $this->blockPage = $c ?? ($this->getCollectionObject() ?: null);
            }
        }

        return $this->blockPage;
    }

    /**
     * Check if the block is edited in Stack.
     *
     * @return bool
     */
    public function isEditedWithinStack(): bool
    {
        if (RequestUser::canAccessDashboard() && !empty($path = $this->request->getPath())
            && $this->request->matches('*' . STACKS_LISTING_PAGE_PATH . '*')) {
            $cID = (string) last(explode('/', $path));
            if (strpos($cID, '@') !== false) {
                list($cID, $locale) = explode('@', $cID, 2);
            }

            if ($cID > 0) {
                $s = Stack::getByID($cID);

                return is_object($s);
            }
        }

        return false;
    }

    protected function getCurrentAreaName(): ?string
    {
        $areaName = ($this->block instanceof Block) ? $this->block->getAreaHandle() : null;

        return $areaName ?? $this->request->get('arHandle');
    }
}
