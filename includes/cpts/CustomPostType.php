<?php

namespace PagarmeSplitPayment\Cpts;

use \Carbon_Fields\Container\Container;

class CustomPostType {
    public $name, $singularName, $slug, $fields;

    public function __construct($name, $singularName, $slug, $fields = [])
    {
        $this->name = $name;
        $this->singularName = $singularName;
        $this->slug = $slug;
        $this->fields = $fields;
    }

    public function create()
    {
        register_post_type( 
            $this->slug,
            array(
                'labels' => array(
                    'name' => __( $this->name ),
                    'singular_name' => __( $this->singularName )
                ),
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => $this->slug),
                'show_in_rest' => true,
            )
        );
    }

    public function addFields()
    {
        Container::make( 
            'post_meta', 
            __(PLUGIN_NAME . " - {$this->singularName} Data")
        )
        ->where( 'post_type', '=', $this->slug )
        ->add_fields( $this->fields );
    }

    public function run()
    {
        add_action( 'init', array($this, 'create') );
        add_action( 'carbon_fields_register_fields', array($this, 'addFields') );
    }
}
