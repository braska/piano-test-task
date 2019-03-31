<?php

namespace Piano\Service\PianoApi;

use Piano\Service\PianoApi\PianoApiClient;

abstract class BasePianoApiService {
    public function getClient() {
        return PianoApiClient::getInstance();
    }
}
