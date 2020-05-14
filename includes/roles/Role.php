<?php

namespace PagarmeSplitPayment\Roles;

use \Carbon_Fields\Container\Container;

class Role {
    public $role, $displayName, $capabilities, $fields;

    public function __construct($role, $displayName, $capabilities = [], $fields = [], $external = false)
    {
        $this->role = $role;
        $this->displayName = $displayName;
        $this->capabilities = $capabilities;
        $this->fields = $fields;
        $this->external = $external;
    }

    public function register()
    {
        $currentRole = get_role($this->role);

        if (
            $currentRole && // Role exists
            array_diff_assoc($currentRole->capabilities, $this->capabilities) // Capabilities outdated
        ) {
            remove_role($this->role);
        }
        
        add_role($this->role, $this->displayName, $this->capabilities);
    }

    public function addFields()
    {
        Container::make( 
            'user_meta', 
            __(PLUGIN_NAME . " - {$this->displayName} Data")
        )
        ->add_fields( $this->fields );
    }

    public function create()
    {
        if (!$this->external) {
            add_action( 'init', array($this, 'register') );
        }
        add_action( 'carbon_fields_register_fields', array($this, 'addFields') );
    }
}
