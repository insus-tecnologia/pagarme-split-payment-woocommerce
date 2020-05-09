<?php

namespace PagarmeSplitPayment\Admin;

use Carbon_Fields\Container;
use PagarmeSplitPayment\Fields\RecipientFieldGroup;

class PluginOptions {

    public function create()
    {
        Container::make('theme_options', __('Pagar.me Split Payment'))
            ->add_fields(RecipientFieldGroup::get());
    }
}
