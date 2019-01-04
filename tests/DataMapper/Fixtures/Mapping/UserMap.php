<?php

namespace Simplex\Tests\DataMapper\Fixtures\Mapping;

use Simplex\Tests\DataMapper\Fixtures\Entity\User;

return [
    User::class => [
        'table' => 'users',
        'id' => 'id',
        'fields' => [
            'id' => [
                'type' => 'int'
            ],
            'name' => [
                'type' => 'string',
                'column' => 'username'
            ],
            'email' => [
                'type' => 'string'
            ],
            'password' => [
                'type' => 'string'
            ]
        ]
    ]
];