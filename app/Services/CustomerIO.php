<?php

namespace App\Services;

use App\Models\User;
use Customerio\{Api,Request};

class CustomerIO
{
    private string $siteId;
    private string $apiSecret;
    protected object $user;
    protected object $api;

    public function __construct(User $user)
    {
        $this->siteId = config('services.customerio.site');
        $this->apiSecret = config('services.customerio.api');
        $this->user = $user;
        $this->api = new Api($this->siteId, $this->apiSecret, new Request);
    }

    public function create(): bool
    {
        if($this->apiSecret && $this->siteId) {
            $response = $this->api->createCustomer(
                $this->user->id,
                $this->user->email,
                [
                    'username' => $this->user->slug,
                    'created_at' => strtotime($this->user->created_at),
                ]
            );

            return $response->success();
        }

        return false;
    }

    public function update(): bool
    {
        if($this->apiSecret && $this->siteId) {
            $response = $this->api->updateCustomer(
                $this->user->id,
                $this->user->email,
                [
                    'username' => $this->user->slug,
                    'books' => $this->user->books()->count()
                ]
            );

            return $response->success();
        }

        return false;
    }
}
