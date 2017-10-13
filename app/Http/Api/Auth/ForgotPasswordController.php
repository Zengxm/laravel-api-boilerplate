<?php

namespace App\Http\Api\Auth;

use App\Http\Api\ApiController;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Password;

/**
 * Class ForgotPasswordController
 * @package App\Http\Api\Auth
 */
class ForgotPasswordController extends ApiController
{
    private $user;

    /**
     * ForgotPasswordController constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * @param ForgotPasswordRequest $request
     * @return mixed
     */
    public function sendResetEmail(ForgotPasswordRequest $request)
    {
        $user = $this->user->where('email', '=', $request->get('email'))->first();

        if(!$user) {
            return $this->respondNotFound('Email has not been sent');
        }

        $broker = $this->getPasswordBroker();
        $sendingResponse = $broker->sendResetLink($request->only('email'));

        if($sendingResponse !== Password::RESET_LINK_SENT) {
            return $this->respondInternalError('An error has occurred');
        }

        return $this->respondNoContent();
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    private function getPasswordBroker()
    {
        return Password::broker();
    }
}
