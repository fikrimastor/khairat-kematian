<?php

namespace App\Services\Payment\Factories;

use App\Enums\PaymentMethod;
use App\Services\Payment\Providers\BankTransferGateway;
use App\Services\Payment\Providers\ChipInAsiaGateway;
use App\Services\Payment\Providers\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * Make a payment gateway instance based on the payment method.
     *
     * @param  PaymentMethod  $method  The payment method
     * @return PaymentGatewayInterface The payment gateway
     *
     * @throws InvalidArgumentException When an invalid payment method is provided
     */
    public function make(PaymentMethod $method): PaymentGatewayInterface
    {
        return match ($method) {
            PaymentMethod::BANK_TRANSFER => app(BankTransferGateway::class),
            PaymentMethod::CHIP_IN_ASIA => app(ChipInAsiaGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment method: {$method->value}")
        };
    }
}
