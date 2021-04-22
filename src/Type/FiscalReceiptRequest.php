<?php

namespace VertxOne\Symfony\Atol\Type;

class FiscalReceiptRequest
{
    /** @var string */
    public $external_id;

    /** @var FiscalReceipt */
    public $receipt;

    /** @var Service|null */
    public $service;

    /** @var string */
    public $timestamp;
}