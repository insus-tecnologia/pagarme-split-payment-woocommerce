<?php

namespace PagarmeSplitPayment\Roles;

use \Carbon_Fields\Container\Container;

class Role {
    public $role, $displayName, $capabilities, $fields, $external, $allow_admin;

    public function __construct(
        $role, 
        $displayName, 
        $capabilities = [], 
        $fields = [], 
        $external = false,
        $allow_admin = false
    ) {
        $this->role = $role;
        $this->displayName = $displayName;
        $this->capabilities = $capabilities;
        $this->fields = $fields;
        $this->external = $external;
        $this->allow_admin = $allow_admin;
    }

    public function register()
    {
        $currentRole = get_role($this->role);

        $intersect = array_intersect_assoc($currentRole->capabilities, $this->capabilities);

        if (
            $currentRole && // Role exists
            (
                count($intersect) !== count($currentRole->capabilities) || 
                count($intersect) !== count($this->capabilities)
            ) // Capabilities outdated
        ) {
            remove_role($this->role);
        }

        // If admin doesnt have some custom caps, add it to him
        $admin_role = get_role('administrator');
        $admin_missing_capabilities = array_diff_assoc(
            $this->capabilities, 
            $admin_role->capabilities
        );

        foreach ($admin_missing_capabilities as $capability => $value) {
            $admin_role->add_cap($capability);
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

        add_filter(
            'woocommerce_prevent_admin_access',
            array($this, 'prevent_admin_access')
        );
        add_filter(
            'woocommerce_disable_admin_bar',
            array($this, 'prevent_admin_access')
        );
    }

    public function prevent_admin_access()
    {
        if( !is_user_logged_in() ) {
            return true;
        }

        $user = wp_get_current_user();
        if (
            current_user_can('administrator') || 
            ($this->allow_admin && in_array($this->role, $user->roles))
        ) {
            return false;
        }

        return true;
    }
}
