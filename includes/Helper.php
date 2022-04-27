<?php

namespace PagarmeSplitPayment;

class Helper
{
    public static function priceInCents(float $price)
    {
        return $price * 100;
    }
}
