<?php

namespace PagarmeSplitPayment\Admin;

use Carbon_Fields\Container;
use PagarmeSplitPayment\Fields\RecipientFieldGroup;

class PluginOptions {

    public function create()
    {
        Container::make('theme_options', __('Pagar.me Split Payment'))
            ->set_icon('dashicons-cart')
            ->add_fields(RecipientFieldGroup::get());
    }
}
