<?php


namespace VertxOne\Symfony\Atol;

trait HostTrait
{
    /** @var bool */
    private $isTestMode;

    public function getHost(): string
    {
        return $this->isTestMode ? 'https://testonline.atol.ru/possystem/v4' : 'https://online.atol.ru/possystem/v4';
    }
}