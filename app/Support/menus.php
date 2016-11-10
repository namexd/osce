<?php

Menu::create('navbar', function($menu)
{


    $menu->add([
        'url' => 'about',
        'title' => '用户管理',
        'name' => '用户管理',
        'attributes' => [
            'target' => '_blank'
        ]
    ]);

    $menu->add([
        'url' => '',
        'title' => '用户审核',

        'attributes' => [
            'target' => '_blank',
            'active'=>true,
        ]
    ]);


});