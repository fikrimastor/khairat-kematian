<?php

namespace App\Helpers;

use App\Enums\NotificationType;
use App\Models\Dependent;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use App\Notifications\DependentAddedNotification;
use App\Notifications\MemberRegisteredNotification;
use App\Notifications\PaymentCreatedNotification;
use App\Notifications\PaymentRejectedNotification;
use App\Notifications\PaymentVerifiedNotification;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    /**
     * Send a notification based on the notification type
     *
     * @param User $user The user to notify
     * @param NotificationType $type The type of notification
     * @param array $data Additional data for the notification
     * @return bool Whether the notification was sent successfully
     */
    public static function notify(User $user, NotificationType $type, array $data = []): bool
    {
        try {
            switch ($type) {
                case NotificationType::PAYMENT_CREATED:
                    if (!isset($data['payment']) || !$data['payment'] instanceof Payment) {
                        throw new \InvalidArgumentException('Payment data is required for payment created notifications');
                    }
                    $user->notify(new PaymentCreatedNotification($data['payment']));
                    break;

                case NotificationType::PAYMENT_VERIFIED:
                    if (!isset($data['payment']) || !$data['payment'] instanceof Payment) {
                        throw new \InvalidArgumentException('Payment data is required for payment verified notifications');
                    }
                    if (!isset($data['receipt']) || !$data['receipt'] instanceof Receipt) {
                        throw new \InvalidArgumentException('Receipt data is required for payment verified notifications');
                    }
                    $user->notify(new PaymentVerifiedNotification($data['payment'], $data['receipt']));
                    break;

                case NotificationType::PAYMENT_REJECTED:
                    if (!isset($data['payment']) || !$data['payment'] instanceof Payment) {
                        throw new \InvalidArgumentException('Payment data is required for payment rejected notifications');
                    }
                    $user->notify(new PaymentRejectedNotification($data['payment']));
                    break;

                case NotificationType::MEMBER_REGISTERED:
                    $user->notify(new MemberRegisteredNotification());
                    break;

                case NotificationType::DEPENDENT_ADDED:
                    if (!isset($data['dependent']) || !$data['dependent'] instanceof Dependent) {
                        throw new \InvalidArgumentException('Dependent data is required for dependent added notifications');
                    }
                    $user->notify(new DependentAddedNotification($data['dependent']));
                    break;

                case NotificationType::SYSTEM_NOTIFICATION:
                    if (!isset($data['subject']) || !isset($data['message'])) {
                        throw new \InvalidArgumentException('Subject and message are required for system notifications');
                    }
                    $user->notify(new SystemNotification($data['subject'], $data['message'], $data));
                    break;

                default:
                    throw new \InvalidArgumentException("Unsupported notification type: {$type->value}");
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send notification: {$e->getMessage()}", [
                'user_id' => $user->id,
                'type' => $type->value,
                'exception' => $e,
            ]);

            return false;
        }
    }

    /**
     * Send a notification to multiple users
     *
     * @param array $users The users to notify
     * @param NotificationType $type The type of notification
     * @param array $data Additional data for the notification
     * @return array Results of sending to each user
     */
    public static function notifyMany(array $users, NotificationType $type, array $data = []): array
    {
        $results = [];

        foreach ($users as $user) {
            $results[$user->id] = self::notify($user, $type, $data);
        }

        return $results;
    }
} 