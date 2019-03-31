<?php

namespace Piano\Service\PianoApi\Publisher;

use Piano\Service\PianoApi\BasePianoApiService;

class PianoApiPublisherUserService extends BasePianoApiService {
    public function search($params = []) {
        $client = $this->getClient();

        $response = $client->request('publisher/user/search', [
            'form_params' => $params,
        ]);

        return json_decode($response->getBody(), true);
    }
}
