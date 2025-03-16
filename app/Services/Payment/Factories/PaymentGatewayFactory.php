<?php

namespace App\Services\Payment\Factories;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Providers\BankTransferGateway;
use App\Services\Payment\Providers\BillplzGateway;
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
        return match (strtolower($gateway)) {
            'chipinasia', 'chipin' => new ChipInAsiaGateway(
                config('services.chipinasia.key') ?? config('services.chipin.api_key'),
                config('services.chipinasia.secret') ?? config('services.chipin.api_secret')
            ),
            'billplz' => new BillplzGateway(
                config('services.billplz.api_key'),
                config('services.billplz.x_signature_key'),
                config('services.billplz.collection_id')
            ),
            'banktransfer', 'bank_transfer' => new BankTransferGateway,
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gateway}")
        };
    }
}
