<?php

namespace Statamic\Addons\Tags;

use Statamic\Extend\Fieldtype;

class TagsFieldtype extends Fieldtype
{
    public $category = ['structured', 'text'];

    public function preProcess($data)
    {
        return ($data) ? $data : [];
    }
}
