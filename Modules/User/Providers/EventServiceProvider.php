<?php

namespace Modules\User\Providers;

use Modules\User\Events\RoleWasUpdated;
use Modules\User\Events\UserWasUpdated;
use Modules\User\Events\TraderIsInviting;
use Modules\User\Events\ExpertIsInviting;
use Modules\User\Events\UserHasRegistered;
use Modules\User\Events\AdminHasRegistered;
use Modules\User\Events\UserHasBegunResetProcess;
use Modules\User\Events\Handlers\SendResetCodeEmail;
use Modules\User\Events\UserApprovalHasBeenAccepted;
use Modules\User\Events\UserApprovalHasBeenRejected;
use Modules\User\Events\ChatMessageWasCreated;
use Modules\User\Events\NotifyExpertForMessage;
use Maatwebsite\Sidebar\Domain\Events\FlushesSidebarCache;

use Modules\User\Events\Handlers\SendChatMessageNotification;
use Modules\User\Events\Handlers\SendExpertInviteEmail;
use Modules\User\Events\Handlers\SendUserApprovalRequiredEmail;
use Modules\User\Events\Handlers\SendRegistrationConfirmationEmail;
use Modules\User\Events\Handlers\SendUserApprovalHasBeenAcceptedEmail;
use Modules\User\Events\Handlers\SendUserApprovalHasBeenRejectedEmail;
use Modules\User\Events\Handlers\SendChatMessageEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AdminHasRegistered::class => [
            SendRegistrationConfirmationEmail::class,
        ],
        UserHasRegistered::class => [
            SendRegistrationConfirmationEmail::class,
            SendUserApprovalRequiredEmail::class
        ],
        UserHasBegunResetProcess::class => [
            SendResetCodeEmail::class,
        ],
        UserWasUpdated::class => [
            FlushesSidebarCache::class,
        ],
        RoleWasUpdated::class => [
            FlushesSidebarCache::class,
        ],
        TraderIsInviting::class => [
            SendTraderInviteEmail::class,
        ],
        ExpertIsInviting::class => [
            SendExpertInviteEmail::class,
        ],
        UserApprovalHasBeenAccepted::class => [
            SendUserApprovalHasBeenAcceptedEmail::class
        ],
        UserApprovalHasBeenRejected::class => [
            SendUserApprovalHasBeenRejectedEmail::class
        ],
        ChatMessageWasCreated::class => [
            SendChatMessageNotification::class
        ],
        NotifyExpertForMessage::class => [
            SendChatMessageEmail::class
        ]
    ];
}
