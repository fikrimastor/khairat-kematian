<?php

namespace App\Services\Payment\Factories;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Providers\BankTransferGateway;
use App\Services\Payment\Providers\ChipInAsiaGateway;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * Create a payment gateway instance based on the specified type
     *
     * @param  string  $gateway  The type of gateway to create
     * @return PaymentGatewayInterface The gateway instance
     *
     * @throws InvalidArgumentException If the gateway type is not supported
     */
    public function make(string $gateway): PaymentGatewayInterface
    {
        return match ($gateway) {
            'chipinasia' => new ChipInAsiaGateway(
                config('services.chipinasia.key'),
                config('services.chipinasia.secret')
            ),
            'banktransfer' => new BankTransferGateway,
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gateway}")
        };
    }
}
