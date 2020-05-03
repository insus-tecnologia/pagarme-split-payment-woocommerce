<?php

namespace PagarmeSplitPayment\Cpts;

class CustomPostType {
    public $name, $singularName, $slug;

    public function __construct($name, $singularName, $slug)
    {
        $this->name = $name;
        $this->singularName = $singularName;
        $this->slug = $slug;
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

    public function run()
    {
        add_action( 'init', array($this, 'create') );
    }
}