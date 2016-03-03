<?php

$version='1.0';


/*
 * 后台页面
 * */
Route::group(['prefix' => "admin",'middleware' => []], function()
{


    Route::controller('user', 'IndexController');
    Route::get('/login',['uses'=>'IndexController@Login']);
    Route::get('/index',['uses'=>'IndexController@Index']);
    Route::post('/index',['as'=>'login.op','uses'=>'IndexController@LoginOp']);
});


/**
 * 公开接口
 */

Route::group(['prefix' => "api/1.0/public",'middleware' => ['cors']], function()
{
    Route::post('oauth/access_token', function(){
        try{
            $userEnter=Authorizer::issueAccessToken();
             return $userEnter;
        }catch (\Exception $ex) {
            if( $ex->getMessage()=='The user credentials were incorrect.'){
                return \Response::json( [ 'access_token' => 'defeat', 'token_type' =>'defeat','expires_in'=>0,'user_id'=>'defeat'] );

            }else{
                return Response::json(Authorizer::issueAccessToken());
            }
        }
    });

    Route::group(['prefix'=>'msc','namespace' => 'V1\Sys'],function()
    {
        Route::controller('user', 'UserController');
    });
});
Route::group(['prefix' => "commom"],function(){
    Route::post('upload-image','\App\Http\Controllers\V1\Msc\CommonController@postUploadImage');
});


/**
 * password作用域
 * cors 为测试时 跨域js提交专用，发布时去除
 */
Route::group(['prefix' => "api/1.0/private", 'namespace' => 'V1','middleware' => ['oauth','cors'],], function()
{
    Route::group(['prefix'=>'user', 'namespace' => 'Sys'],function() {
        //Route::post('getuser', 'UserController@getUserById');
        Route::controller('membercenter', 'MemberCenterController');
    });
    Route::group(['prefix'=>'admin', 'namespace' => 'Sys'],function() {
        Route::controller('user', 'UserManagerController');
    });
    Route::group(['prefix'=>'admin', 'namespace' => 'Msc'],function() {
        Route::controller('resource', 'ResourcesManagerController');
        Route::controller('common', 'CommonController');
    });

});


/**
 *client_credentials作用域
 */
Route::group(['prefix' => "api/1.0/client", 'namespace' => 'App\Http\Controllers\V1','middleware' => 'oauth'], function()
{
    Route::group(['prefix'=>'users'],function() {

    });
});
Route::group(['prefix' => "test"],function(){
    Route::get('index', 'IndexController@index');
});


/*
 * 权限管理
 * */
Route::group(['prefix' => "auth",'middleware' => []], function()
{
    Route::get('/auth-manage', ['uses'=>'AuthController@AuthManage','as'=>'auth.AuthManage']);

    Route::get('/new-role-page', ['uses'=>'AuthController@newRolePage','as'=>'auth.newRolePage']);
    Route::post('/add-new-role', ['uses'=>'AuthController@postAddNewRole','as'=>'auth.postAddNewRole']);
    Route::get('/delete-role', ['uses'=>'AuthController@deleteRole','as'=>'auth.deleteRole']);

    Route::get('/set-permissions/{id}', ['uses'=>'AuthController@SetPermissions','as'=>'auth.SetPermissions']);
    Route::get('/edit-role', ['uses'=>'AuthController@editRole','as'=>'auth.editRole']);
    Route::post('/save-permissions', ['uses'=>'AuthController@SavePermissions','as'=>'auth.SavePermissions']);

    Route::get('/sdd-auth', ['uses'=>'AuthController@AddAuth','as'=>'auth.AddAuth']);



});