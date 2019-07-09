<?php

namespace Modules\User\Services;

use Modules\User\Repositories\UserDeviceTokenRepository;

class DeviceTokenRegistration
{
    /**
     * @var UserDeviceTokenRepository
     */
    private $userDeviceToken;

    /**
     * AWS SNS Client instance
     *
     * @var AWS SNS Client
     */
    private $sns;

    /**
     * Constructor
     *
     * @param UserDeviceTokenRepository $userDeviceToken
     */
    public function __construct(UserDeviceTokenRepository $userDeviceToken)
    {
        $this->userDeviceToken = $userDeviceToken;
        $this->sns             = \AWS::createClient('sns');
    }

    /**
     * Handles the creating/updating of Device Token ARN endpoints via AWS SNS.
     * @param  array  $credentials
     * @return object
     */
    public function register($tokenDetails)
    {
        $deviceToken = $this->deviceTokenExists($tokenDetails);

        if ($deviceToken && $this->validEndpointForDeviceToken($deviceToken)) {
            return $this->update($deviceToken, $tokenDetails);
        } else {
            // if ($deviceToken) $this->userDeviceToken->destroy($deviceToken); // Allow multiple valid tokens
            $this->userDeviceToken->deleteAllForUser($tokenDetails['user_id']); // Allow one valid token
            return $this->create($tokenDetails);
        }
    }

    /**
     * Creates endpoint using device token.
     * @param array $credentials
     * @return object
     */
    private function create($tokenDetails)
    {
        $endpointArn = $this->sns->createPlatformEndpoint(array(
            'PlatformApplicationArn' => $tokenDetails['platform'] == 'android' ? env('AWS_ANDROID_ARN_URL') : env('AWS_IOS_ARN_URL'),
            'Token' => $tokenDetails['device_token'],
        ));

        $tokenDetails['arn_endpoint'] = isset($endpointArn['EndpointArn']) ? $endpointArn['EndpointArn'] : '';

        return $this->userDeviceToken->create($tokenDetails);
    }

    /**
     * Updates existing endpoint for device token.
     * @param array $credentials
     * @return object
     */
    private function update($deviceToken, $tokenDetails)
    {
        $this->sns->setEndpointAttributes([
            'Attributes' => [
                'Token' => $deviceToken->device_token,
                'Enabled' => "true"
            ],
            'EndpointArn' => $deviceToken->arn_endpoint
        ]);

        return $this->userDeviceToken->update($deviceToken, $tokenDetails);
    }

    /**
     * Check device token exists.
     * @param array $tokenDetails
     * @return object
     */
    private function deviceTokenExists($tokenDetails)
    {
        return $this->userDeviceToken->findByAttributes([
            'platform' => $tokenDetails['platform'],
            'device_token' => $tokenDetails['device_token'],
        ]);
    }

    /**
     * Get Endpoint attributes for ARN, check still valid and it is marked as enabled.
     * @param  object $deviceToken
     * @return bool
     */
    private function validEndpointForDeviceToken($deviceToken)
    {
        $attributes = $this->sns->getEndpointAttributes(["EndpointArn" => $deviceToken->arn_endpoint]);

        return ( ($attributes !="failed" || $attributes != -1) || $attributes['Attributes']['Enabled'] != 'false' );
    }
}
