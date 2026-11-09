<?php

namespace Xanweb\RpCommon\Traits;

trait ObjectTrait
{
    public function setPropertiesFromArray($arr)
    {
        foreach ($arr as $key => $prop) {
            $setter = 'set' . ucfirst($key);
            // we prefer passing by setter method
            if (method_exists($this, $setter)) {
                $this->$setter($prop);
            } else {
                $this->{$key} = $prop;
            }
        }
    }
}
