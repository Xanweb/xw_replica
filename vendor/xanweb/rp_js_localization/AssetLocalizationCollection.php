<?php

namespace Xanweb\RpJsLocalization;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Xanweb\RpHelpers\Bootstrap;

class AssetLocalizationCollection extends Collection
{
    public function __construct()
    {
        parent::__construct(['i18n' => []]);
    }

    /**
     * Merge Given Array of items with class items.
     *
     * @param array $items
     *
     * @return $this
     */
    public function mergeWith(array $items): self
    {
        $this->items = array_merge($this->items, $items);

        return $this;
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $array = $this->jsonSerialize();

        $placeholders = [];
        array_walk_recursive($array, static function(&$value) use (&$placeholders) {
            // We don't want to encode passed js functions
            // So we will set placeholders before encoding to restore them after that.
            if (\str_starts_with(Bootstrap::strip_spaces($value), 'function')) {
                $placeholders['"' . ($placeholder = '__PLACEHOLDER__' . Str::random(8)) . '"'] = $value;

                $value = $placeholder;
            }
        });

        $encoded = json_encode($array, $options);

        if (empty($placeholders)) {
            return $encoded;
        }

        // Restore js functions as they are
        return str_replace(array_keys($placeholders), array_values($placeholders), $encoded);
    }
}
