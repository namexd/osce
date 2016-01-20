<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/20
 * Time: 15:28
 */


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;

class DrawlotsTest extends TestCase
{
    private $user_id = 51;

    public function test()
    {
        $userOb =   \App\Entities\User::find(51);
        $response   =   $this->actingAs($userOb)
            ->action('get','\Modules\Osce\Http\Controllers\Api\Pad\DrawlotsController@getExaminee');
        $view = $response->getContent();
        $data = json_decode($view);
        $this->assertTrue($data->code==1);
    }
}