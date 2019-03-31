<?php

namespace Piano;

use Piano\Service\PianoApi\Publisher\PianoApiPublisherUserService;

class UserRepository {
    protected $service;

    public function __construct($service = null) {
        $this->service = $service ?: new PianoApiPublisherUserService();
    }

    public function findByEmail($email)
    {
        $result = $this->service->search(['email' => $email]);

        if (!isset($result) || $result['code'] !== 0) {
            throw new \Exception("Error while API request");
        }

        if ($result['total'] > 0) {
            return $result['users'][0];
        }

        return null;
    }

    public function updateUid(&$local_user)
    {
        $remote_user = $this->findByEmail($local_user['email']);

        if ($remote_user) {
            $local_user['user_id'] = $remote_user['uid'];
        }
    }
}
