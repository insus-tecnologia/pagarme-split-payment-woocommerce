<?php

namespace PagarmeSplitPayment\Cpts;

class CustomPostTypePartner extends CustomPostType {
    public function __construct()
    {
        parent::__construct('Partners', 'Partner', 'partner');
    }
}