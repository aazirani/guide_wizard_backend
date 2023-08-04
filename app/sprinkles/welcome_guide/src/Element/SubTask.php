<?php

/*
 * UserFrosting Form Generator
 */

namespace UserFrosting\Sprinkle\FormGenerator\Element;

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
