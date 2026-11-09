<?php

namespace Xanweb\Replica\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPageTypesBaseRoutine as CoreBaseRoutine;
use Concrete\Core\Page\Type\Type;

class ImportPageTypesBaseRoutine extends CoreBaseRoutine
{
    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetypes)) {
            foreach ($sx->pagetypes->pagetype as $p) {
                $ptHandle = (string) $p['handle'];
                $pt = Type::getByHandle($ptHandle);
                if (!is_object($pt)) {
                    Type::import($p);
                }
            }
        }
    }
}
