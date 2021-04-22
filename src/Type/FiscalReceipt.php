<?php


namespace VertxOne\Symfony\Atol\Type;


class FiscalReceipt
{
    /** @var Client */
    public $client;

    /** @var Company */
    public $company;

    /** @var ReceiptItem[] */
    public $items;

    /** @var Payment[] */
    public $payments;

    /** @var Vat[] */
    public $vats;

    /** @var float */
    public $total;

    /** @var AdditionalUserProp|null */
    public $additional_user_props;
}