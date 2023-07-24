<?php

/*
 * UserFrosting Form Generator
 *
 * @link      https://github.com/lcharette/UF_FormGenerator
 * @copyright Copyright (c) 2020 Louis Charette
 * @license   https://github.com/lcharette/UF_FormGenerator/blob/master/LICENSE (MIT License)
 */

namespace UserFrosting\Sprinkle\FormGenerator\Element;

use UserFrosting\Sprinkle\FormGenerator\Element\Input;

/**
 * Select input type class.
 * Manage the default attributes required to display a select input type.
 */
class SubTask extends Input
{
    /**
     * {@inheritdoc}
     */
    protected function applyTransformations(): void
    {
        $this->element = array_merge([
            'autocomplete' => 'off',
            'class' => 'form-control',
            'name'  => $this->name,
            'id'    => 'field_' . $this->name,
        ], $this->element);
    }
}
