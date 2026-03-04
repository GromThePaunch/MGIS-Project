<?php
$siteName = 'Inkforge Tees';
$taxRate = 0.085;

function shipping_cost(float $subtotal): float
{
    if ($subtotal <= 0) {
        return 0.00;
    }

    if ($subtotal < 50) {
        return 6.99;
    }

    if ($subtotal < 100) {
        return 10.99;
    }

    return 14.99;
}
