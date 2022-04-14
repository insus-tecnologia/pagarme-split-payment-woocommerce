<?php

namespace PagarmeSplitPayment;

class Helper
{
    public static function partnerAmount(float $percentage, float $orderTotal)
    {
        return self::priceInCents(round($percentage * $orderTotal, 2));
    }

    public static function priceInCents(float $price)
    {
        return $price * 100;
    }
}
