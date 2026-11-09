<?php

namespace Xanweb\RpCommon\Traits;

use Doctrine\Common\Collections\ArrayCollection;

trait JsonSerializableTrait
{
    public function jsonSerialize()
    {
        $dh = app('date');
        $jsonObj = new \stdClass();
        $array = get_object_vars($this);
        foreach ($array as $key => $v) {
            if ($v && ($v instanceof \DateTimeInterface)) {
                $jsonObj->{$key} = $dh->formatDate($v);
            } elseif (is_object($v)) {
                $this->jsonSerializeRelatedObj($key, $v, $jsonObj);
            } else {
                $jsonObj->{$key} = $v;
            }
        }

        return $jsonObj;
    }

    protected function jsonSerializeRelatedObj($key, $o, $jsonObj): void
    {
        if (!($o instanceof ArrayCollection) && method_exists($o, 'getID')) {
            $jsonObj->{$key . 'ID'} = $o->getID();
        }
    }
}
