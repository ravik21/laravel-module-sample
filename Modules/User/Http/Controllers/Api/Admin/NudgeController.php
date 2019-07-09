<?php

namespace Modules\User\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Entities\User;
use Modules\User\Repositories\UserRepository;
use Modules\Notification\Services\Nudger;
use Modules\User\Http\Requests\Admin\UserNudgeRequest;
use Carbon\Carbon;

class NudgeController extends BaseApiController
{
    /**
     * @var Nudger
     */
    protected $nudger;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Constructor
     *
     * @param Nudger $nudger
     * @param UserRepository $userRepository
     */
    public function __construct(Nudger $nudger, UserRepository $userRepository)
    {
        parent::__construct();
        $this->nudger = $nudger;
        $this->userRepository = $userRepository;
    }

    /**
     * Send an instant nudge or schedule for later.
     *
     * @param  UserNudgeRequest $request
     * @return Response
     */
    public function store(UserNudgeRequest $request)
    {
        $action = 'user-nudge';

        try {
            $meta  = $request->only('search', 'group');
            $meta['status'] = 'Active';
            $users = $this->userRepository->filterUsing($meta, ['user'])->get();

            if ($request->scheduled_at) {
                /* If scheduled - store the nudge. see Modules\Notification\Console\Commands\ScheduledNudgeDispatcher */
                $this->nudger->scheduleNudgeMany($users, $request->message, $action, null, $this->user, $request->scheduled_at);
            } else {
                /* Send a nudge instantly */
                $this->nudger->nudgeMany($users, $request->message, $action, null, $this->user);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => true
            ]);
        }

        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'No Users that match the criteria.',
                'errors' => true
            ]);
        }

        $message = $request->scheduled_at 
            ? sprintf('%s Users scheduled to be nudged on %s', $users->count(), (new Carbon($request->scheduled_at))->format('D, dS M Y \a\t H:i')) 
            : sprintf('%s Users nudged', $users->count());

        return response()->json([
            'message' => $message
        ]);
    }
} 