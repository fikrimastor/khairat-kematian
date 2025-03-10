# Payment Collection System - Models and Migrations

This document outlines the models and database structure for the Khairat Kematian payment collection system.

## Overview

The payment collection system is built around two primary models:
- **Payment**: Represents a payment made by a user
- **Receipt**: Represents a receipt generated for a successful payment

These models are supported by enums:
- **PaymentStatus**: Defines possible payment statuses
- **PaymentMethod**: Defines available payment methods

## Models

### Payment Model

The Payment model represents a payment made by a user for the Khairat Kematian program. It includes:

- Basic payment information (amount, method, reference)
- Status tracking (pending, verified, rejected)
- Verification metadata (who verified, when)
- Period information (month, year)
- Household count for calculating fees

#### Key Relationships:
- Belongs to a User
- Has one Receipt
- Belongs to a Verifier (admin user)

#### Key Methods:
- Status checking methods (isVerified, isPending)
- Period formatting
- Query scopes for filtering

### Receipt Model

The Receipt model represents a receipt generated after a payment is verified. It includes:

- Unique receipt number
- Path to the stored receipt file
- Generation timestamp

#### Key Relationships:
- Belongs to a Payment
- Has access to User through Payment

#### Key Methods:
- Receipt number generation
- Download URL generation
- Date formatting

## Enums

### PaymentStatus

Represents the possible states of a payment:
- Pending: Awaiting verification
- Processing: Being processed
- Verified: Successfully verified
- Rejected: Rejected due to issues
- Failed: Failed during processing

Includes methods for:
- Getting human-readable labels
- Color coding for UI
- State transition validation

### PaymentMethod

Represents the available payment methods:
- BankTransfer: Manual bank transfer
- ChipInAsia: Online payment via ChipIn Asia
- Cash: Cash payment

Includes methods for:
- Getting human-readable labels
- Determining if receipt upload is required
- Determining if online processing is needed
- Determining if manual verification is required

## Database Structure

### Payments Table

```sql
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
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id)
);
```

### Receipts Table

```sql
CREATE TABLE receipts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id BIGINT UNSIGNED NOT NULL,
    receipt_number VARCHAR(50) NOT NULL UNIQUE,
    receipt_path VARCHAR(255) NULL,
    generated_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE
);
```

## Usage Examples

### Creating a Payment

```php
// Create a payment
$payment = Payment::create([
    'user_id' => auth()->id(),
    'amount' => $amount,
    'payment_method' => PaymentMethod::BankTransfer,
    'reference_no' => $referenceNumber,
    'status' => PaymentStatus::Pending,
    'month' => $month,
    'year' => $year,
    'household_count' => $householdCount,
]);
```

### Generating a Receipt

```php
// Generate a receipt for a verified payment
$receipt = Receipt::create([
    'payment_id' => $payment->id,
    'receipt_number' => Receipt::generateReceiptNumber(),
    'receipt_path' => $receiptPath,
    'generated_at' => now(),
]);
```

### Verifying a Payment

```php
// Verify a payment
$payment->update([
    'status' => PaymentStatus::Verified,
    'verified_by' => auth()->id(),
    'verified_at' => now(),
]);

// Generate receipt
$receipt = Receipt::create([
    'payment_id' => $payment->id,
    'receipt_number' => Receipt::generateReceiptNumber(),
    'generated_at' => now(),
]);
```

## Next Steps

With the models and migrations defined, the next steps are:

1. Implement the Repository layer for data access
2. Create the Actions for payment processing
3. Develop the Controllers and Livewire components for the user interface
4. Set up the payment gateway integrations
