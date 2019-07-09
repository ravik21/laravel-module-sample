<?php

namespace Modules\User\Repositories\Eloquent;

use Carbon\Carbon;
use Modules\User\Repositories\UserInviteRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentUserInviteRepository extends EloquentBaseRepository implements UserInviteRepository
{
    public function createOrUpdateForEmail($user, $email, $message = NULL)
    {
        $invite = $this->model->where('email', $email)->first();

        if (!$invite) {
            $invite = $this->model->create(['email' => $email]);
            $invite->inviter()->associate($user)->save();
        }

        if (!$invite->completed) {
            $invite->invite_count++;
            $invite->code = $this->generateCode();
            $invite->expires_at = Carbon::now()->addWeeks(2);

            if ($message and strlen( (string) $message ) > 0) {
                $invite->message = $message;
            }

            $invite->save();

            return $invite;
        }

        return null;
    }

    public function isValidCode($code)
    {
        return (bool) $this->model->where('code', $code)
                              ->where('completed', false)
                              ->unexpired()
                              ->first();
    }

    /**
     * Return a random string for an activation code.
     *
     * @return string
     */
    private function generateCode()
    {
        do { $code = str_random(); } while ( $this->model->where('code', $code)->first() );

        return $code;
    }

    public function isValidEmail($request)
    {
      return (bool) $this->model->where('code', $request['invite_code'])
                          ->where('email', $request['email'])
                          ->where('completed', false)
                          ->first();
    }
}
