<?php

namespace App\Policies;

use App\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    public function canUseWechat(Client $client = null)
    {
        return !is_null($client) && $client->priority > 0;
    }

    public function canCalibrateKeys(Client $client = null)
    {
        return !is_null($client) && $client->priority > 25;
    }

    public function canSeeClients(Client $client = null)
    {
        return !is_null($client) && $client->priority > 50;
    }

    public function canViewAllHistory(Client $client = null)
    {
        return !is_null($client) && $client->priority > 60;
    }

    public function canUpdateClient(Client $cclient = null, Client $rclient = null)
    {
        return $this->canViewAllHistory($cclient) && $cclient->priority > $rclient->priority;
    }
}
