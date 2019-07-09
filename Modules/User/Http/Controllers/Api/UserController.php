<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\User\Emails\VerifyEmail;
use Illuminate\Contracts\Mail\Mailer;
use Modules\User\Contracts\Authentication;
use Modules\User\Repositories\UserRepository;
use Modules\User\Services\AdminOrUserResetter;
use Modules\User\Services\User\UserRegistration;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Modules\User\Http\Requests\RegisterRequest;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Modules\User\Http\Requests\ResendVerificationRequest;
use Mpociot\VatCalculator\VatCalculator;

class UserController extends BaseApiController
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * Constructor
     *
     * @param UserRepository $userRepository
     * @param Mailer $mailer
     * @param Authentication $auth
     */
    public function __construct(UserRepository $userRepo, Mailer $mailer, Authentication $auth)
    {
        $this->userRepo                   = $userRepo;
        $this->mailer                     = $mailer;
        $this->auth                       = $auth;

        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/users/resend-verification",
     *     summary="Request verification email resend.",
     *     tags={"User"},
     *     description="Sends verfication email to valid email address provided.",
     *     operationId="userResendVerification",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="ApiKey",
     *         in="header",
     *         description="Valid Api Key for this API. ",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Valid ID and Email Address that exists in system.",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                   property="id",
     *                   type="integer",
     *             ),
     *             @SWG\Property(
     *                   property="email",
     *                   type="string",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Please specify an ApiKey.",
     *     ),
     *      @SWG\Response(
     *         response="401",
     *         description="Invalid ApiKey or Credentials supplied.",
     *     ),
     *      @SWG\Response(
     *         response="403",
     *         description="Account already activated.",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error.",
     *     )
     * )
     */
    public function resendVerificationEmail(ResendVerificationRequest $request)
    {
        $verificationDetails = $request->only('id', 'email');

        $user        = $this->userRepo->find($verificationDetails['id']);
        $user->email = $verificationDetails['email'];
        $user->save();

        $activation = $user->activations()->where('completed', false)->first();

        if ($activation && $activation->completed) {
            return $this->setStatusCode(403)->respondWithError(
                trans( $user->hasRoleSlug('user') ? 'user::messages.users.already-activated' : 'user::messages.admins.already-activated')
            );
        }

        $activationCode = $this->auth->createActivation($user);

        $this->mailer->to($user->email)->send(new VerifyEmail($user, $activationCode));

        return response()->json(["message" => "Successfully resent verification email"], 200);
    }
    public function validateVat(Request $request)
    {
      $vatCalculator = new VatCalculator();
      $vatCalculator->setBusinessCountryCode($request->country_code);
        try {
          $validVAT = $vatCalculator->isValidVATNumber($request->vat_number);
          return response()->json(['valid' => $validVAT ? true : false], 200);
        } catch( VATCheckUnavailableException $e ){
          return response()->json(['valid' => false], 500);
        }
    }
}
