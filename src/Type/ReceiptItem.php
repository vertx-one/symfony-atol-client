<?php


namespace VertxOne\Symfony\Atol\Type;


class ReceiptItem
{
    /** @var string */
    public $name;

    /** @var float */
    public $price;

    /** @var float */
    public $quantity;

    /** @var float */
    public $sum;

    /** @var string|null */
    public $measurement_unit;

    /** @var string|null */
    public $payment_method;

    /** @var Vat */
    public $vat;
}