# Khairat Kematian Collection System Conversion Plan

## Overview

This document outlines the plan to convert the payment collection system from the existing khairat2u.com PHP application to a Laravel-based application using the TALL stack (Tailwind CSS, Alpine.js, Livewire 3, Laravel 12), focusing on a simplified architecture that leverages Eloquent directly with Action classes and Factory pattern.

## Analysis of Existing System

### Current Structure
The existing khairat2u.com application is a traditional PHP application with:
- Direct PHP-HTML mixed files (fee.php, receipt.php)
- Basic authentication system
- Simple database queries using mysqli
- JavaScript for client-side functionality
- No clear separation between presentation and business logic

### Collection System Components
From the existing code:
- **Fee Management**: Shows monthly fees with payment status
- **Receipt Generation**: Displays payment receipts with details
- **Payment Processing**: Basic payment records
- **Financial Reporting**: Simple financial statements

## Proposed Architecture

### Action-Based Architecture with Factory Pattern
We'll implement a Laravel application using:
- Action classes for single responsibility business logic
- Factory pattern for creating complex objects (payment gateways)
- Direct use of Eloquent ORM without repository abstraction
- TALL stack for frontend

### Folder Structure

```
app/
├── Actions/
│   └── Payments/
│       ├── CreatePaymentAction.php
│       ├── GenerateReceiptAction.php
│       ├── ProcessPaymentAction.php
│       ├── ValidatePaymentAction.php
│       ├── VerifyPaymentAction.php
│       └── UpdatePaymentStatusAction.php
│
├── Http/
│   ├── Controllers/
│   │   └── Payment/
│   │       ├── PaymentController.php
│   │       └── ReceiptController.php
│   │
│   └── Livewire/
│       └── Payment/
│           ├── PaymentForm.php
│           ├── PaymentList.php
│           ├── PaymentStatus.php
│           ├── PaymentVerification.php
│           └── ReceiptViewer.php
│
├── Models/
│   ├── Payment.php
│   ├── PaymentMethod.php
│   └── Receipt.php
│
├── Services/
│   └── Payment/
│       ├── Contracts/
│       │   └── PaymentGatewayInterface.php
│       ├── Factories/
│       │   └── PaymentGatewayFactory.php
│       └── Providers/
│           ├── ChipInAsiaGateway.php
│           └── BankTransferGateway.php
│
├── DTOs/
│   └── Payment/
│       ├── PaymentData.php
│       └── ReceiptData.php
│
├── Enums/
│   ├── PaymentStatus.php
│   └── PaymentMethod.php
│
└── Events/
    └── Payment/
        ├── PaymentCreated.php
        ├── PaymentProcessed.php
        └── PaymentVerified.php
```

### Database Schema

```sql
-- Payments Table
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    reference_no VARCHAR(100),
    status VARCHAR(50) NOT NULL,
    payment_date TIMESTAMP NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    month VARCHAR(20) NOT NULL,
    year SMALLINT NOT NULL,
    household_count TINYINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Receipts Table
CREATE TABLE receipts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id BIGINT UNSIGNED NOT NULL,
    receipt_number VARCHAR(50) NOT NULL,
    receipt_path VARCHAR(255) NULL,
    generated_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE
);
```

## Implementation Plan

### Phase 1: Foundation (1-2 weeks)

1. **Project Setup**
   - Initialize Laravel 12 project
   - Configure database connections
   - Set up Laravel Breeze authentication
   - Configure Livewire 3
   - Set up Tailwind CSS

2. **Models and Migrations**
   - Create Payment model/migration
   - Create Receipt model/migration
   - Define relationships
   - Implement enums for payment status and methods

3. **Core Services**
   - Create PaymentGatewayInterface
   - Implement PaymentGatewayFactory
   - Create concrete gateway implementations

### Phase 2: Payment Processing (2-3 weeks)

1. **Payment Actions**
   - Implement CreatePaymentAction
   - Implement ProcessPaymentAction
   - Implement ValidatePaymentAction
   - Implement UpdatePaymentStatusAction

2. **DTOs and Enums**
   - Create PaymentData DTO
   - Create ReceiptData DTO 
   - Implement PaymentStatus enum
   - Implement PaymentMethod enum

3. **Controllers & Livewire Components**
   - Implement PaymentController
   - Create PaymentForm Livewire component
   - Create PaymentList Livewire component
   - Create PaymentStatus Livewire component
   - Implement responsive layouts with Tailwind

### Phase 3: Receipt Management (1-2 weeks)

1. **Receipt Actions**
   - Implement GenerateReceiptAction
   - Implement ViewReceiptAction

2. **Receipt Components**
   - Create ReceiptViewer Livewire component
   - Implement receipt templates with Blade
   - Add PDF export functionality

3. **Payment Verification**
   - Implement VerifyPaymentAction
   - Create PaymentVerification Livewire component
   - Set up verification workflow

### Phase 4: Reporting & Integration (1-2 weeks)

1. **Reporting Features**
   - Implement payment summary reports
   - Create visualizations (charts)
   - Add export functionality

2. **Notifications**
   - Set up payment confirmation emails
   - Implement payment reminder notifications
   - Configure success/failure notifications

3. **Integration & Testing**
   - Connect with member management system
   - Implement unit and feature tests
   - Perform end-to-end testing

## Key Code Examples

### Payment Gateway Factory

```php
<?php

namespace App\Services\Payment\Factories;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Providers\ChipInAsiaGateway;
use App\Services\Payment\Providers\BankTransferGateway;

class PaymentGatewayFactory
{
    public function make(string $gateway): PaymentGatewayInterface
    {
        return match ($gateway) {
            'chipinasia' => new ChipInAsiaGateway(
                config('services.chipinasia.key'),
                config('services.chipinasia.secret')
            ),
            'banktransfer' => new BankTransferGateway(),
            default => throw new \InvalidArgumentException("Unsupported payment gateway: {$gateway}")
        };
    }
}
```

### Process Payment Action

```php
<?php

namespace App\Actions\Payments;

use App\Models\Payment;
use App\Models\Receipt;
use App\Services\Payment\Factories\PaymentGatewayFactory;
use App\DTOs\Payment\PaymentData;
use App\Enums\PaymentStatus;

class ProcessPaymentAction
{
    protected $gatewayFactory;
    
    public function __construct(PaymentGatewayFactory $gatewayFactory)
    {
        $this->gatewayFactory = $gatewayFactory;
    }
    
    public function execute(PaymentData $data)
    {
        // Create the payment record directly with Eloquent
        $payment = Payment::create([
            'user_id' => $data->userId,
            'amount' => $data->amount,
            'payment_method' => $data->paymentMethod->value,
            'month' => $data->month,
            'year' => $data->year,
            'household_count' => $data->householdCount,
            'status' => PaymentStatus::PENDING->value
        ]);
        
        // Use factory to get the right payment gateway
        $gateway = $this->gatewayFactory->make($data->paymentMethod->value);
        
        // Process the payment through the gateway
        $result = $gateway->processPayment([
            'amount' => $data->amount,
            'reference' => $payment->id,
            // Other gateway-specific data
        ]);
        
        // Update payment with gateway response
        $payment->update([
            'reference_no' => $result['transaction_id'] ?? null,
            'status' => $result['status'],
        ]);
        
        // Generate receipt if payment was successful
        if ($result['status'] === PaymentStatus::COMPLETED->value) {
            Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => 'RCT-' . date('Y') . '-' . $payment->id,
                'generated_at' => now()
            ]);
        }
        
        return $payment;
    }
}
```

### Payment Model with Eloquent

```php
<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'reference_no',
        'status',
        'payment_date',
        'verified_by',
        'verified_at',
        'month',
        'year',
        'household_count',
    ];

    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes for common queries
    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', PaymentStatus::COMPLETED->value);
    }
}
```

### Payment Enums

```php
<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
}

enum PaymentMethod: string
{
    case BANK_TRANSFER = 'banktransfer';
    case CHIP_IN_ASIA = 'chipinasia';
}
```

## Key Features to Implement

1. **Fee Management**
   - Display of outstanding fees by month
   - Calculation of fees based on household members
   - Fee history and tracking

2. **Payment Processing**
   - Support for multiple payment methods
   - Integration with payment gateways
   - Receipt upload for manual payments
   - Payment status tracking

3. **Receipt Generation**
   - Automatic receipt generation
   - PDF exports
   - Receipt history viewing
   - Receipt sharing options

4. **Verification Workflow**
   - Admin verification of manual payments
   - Automatic verification for online payments
   - Verification status updates
   - Audit trail for verifications

5. **Reporting**
   - Monthly collection reports
   - Payment status summaries
   - User payment histories
   - Financial dashboards

## Technical Considerations

1. **Payment Security**
   - Encryption for payment data
   - Secure storage of receipts
   - Authentication for sensitive operations
   - Audit logs for all payment activities

2. **Performance**
   - Efficient Eloquent queries
   - Caching for frequent operations
   - Lazy loading of components
   - Database indexing for payment searches

3. **User Experience**
   - Mobile-responsive design
   - Real-time status updates with Livewire
   - Intuitive payment forms
   - Clear payment confirmation

4. **Error Handling**
   - Graceful failure handling
   - User-friendly error messages
   - Transaction rollbacks for failed payments
   - Logging for troubleshooting

## Migration Strategy

1. **Data Migration**
   - Create data migration scripts
   - Map old database schema to new schema
   - Validate imported data
   - Keep original data as backup

2. **Phased Rollout**
   - Deploy foundation components first
   - Implement basic payment processing
   - Add advanced features incrementally
   - Parallel run with old system initially

## References from Existing Code

1. From `fee.php`:
   - Monthly fee structure
   - Payment status tracking
   - Fee calculation based on household members

2. From `receipt.php`:
   - Receipt layout and design
   - Receipt content requirements
   - Payment details displayed

3. From `statistic.php`:
   - Financial reporting requirements
   - Visualization needs
   - Summary statistics

## Risk Assessment

| Risk | Impact | Mitigation |
|------|--------|------------|
| Data migration errors | High | Thorough testing, backup strategy, validation scripts |
| Payment gateway integration issues | High | Mock interfaces for testing, fallback to manual payments |
| User adoption resistance | Medium | Training sessions, intuitive UI, help documentation |
| Performance with large datasets | Medium | Database optimization, pagination, efficient queries |
| Security vulnerabilities | High | Regular security audits, follow Laravel best practices, input validation |

## Conclusion

This plan outlines the approach to convert the payment collection system from the existing khairat2u.com application to a Laravel-based application. By leveraging Laravel's Eloquent ORM directly with Action classes and the Factory pattern, we'll create a maintainable, secure, and user-friendly payment system without unnecessary abstraction layers. The implementation will follow a phased approach, starting with the foundation and gradually adding more advanced features.