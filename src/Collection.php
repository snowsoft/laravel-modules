<?php
namespace Llama\Modules;

use Illuminate\Contracts\Support\Arrayable;

class Collection extends \Illuminate\Support\Collection
{

    /**
     * Get items collections.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($module) {
            if ($module instanceof Module) {
                return $module->json()->getAttributes();
            }
            
            return $module instanceof Arrayable ? $module->toArray() : $module;
        }, $this->items);
    }
}
