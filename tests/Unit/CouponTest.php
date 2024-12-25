<?php


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CouponTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
//        var_dump($expiresAt);
        $service = new \App\Services\Business\coupon\CouponService();
        $data = $service->goods(0);
        var_dump($data);
    }
}
