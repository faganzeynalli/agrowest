<?php

declare(strict_types=1);

namespace App\Notification\Check;

use App\Container\EnvironmentAwareTrait;
use App\Entity\Api\Notification;
use App\Event\GetNotifications;
use App\Exception\Http\RateLimitExceededException;
use App\Session\FlashLevels;

final class DonateAdvisorCheck
{
    use EnvironmentAwareTrait;

    public function __invoke(GetNotifications $event): void
    {
        if (!$this->environment->isProduction()) {
            return;
        }

        $request = $event->getRequest();

        $rateLimit = $request->getRateLimit();
        try {
            $rateLimit->checkRequestRateLimit($request, 'notification:donate', 600, 1);
        } catch (RateLimitExceededException) {
            return;
        }

        $notification = new Notification();
        $notification->title = __('Aplus is free and open-source software.');
        $notification->body = __(
            'If you are enjoying Aplus, please consider donating to support our work. We depend ' .
            'on donations to build new features, fix bugs, and keep Aplus modern, accessible and free.',
        );
        $notification->type = FlashLevels::Info->value;

        $notification->actionLabel = __('Donate to Aplus');
        $notification->actionUrl = '';

        $event->addNotification($notification);
    }
}
