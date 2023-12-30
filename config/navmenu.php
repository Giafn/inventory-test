<?php

return [
    [
        'name' => 'Dashboard',
        'route' => 'dashboard',
        'icon' => 'heroicon-s-home',
        'code' => 'dashboard',
        'child' => []
    ],
    [
        'name' => 'Inventory',
        'route' => 'inventory',
        'icon' => 'heroicon-o-folder',
        'code' => 'inventory',
        'child' => []
    ],
    [
        'name' => 'Purchase',
        'route' => 'purchase',
        'icon' => 'heroicon-o-folder-add',
        'code' => 'purchase',
        'child' => [
            [
                'name' => 'History Purchase',
                'route' => 'purchase',
                'code' => 'purchase'
            ]
        ]

    ],
    [
        'name' => 'Sales',
        'route' => 'sales',
        'icon' => 'heroicon-o-folder-remove',
        'code' => 'sales',
        'child' => [
            [
                'name' => 'History Sales',
                'route' => 'sales',
                'code' => 'sales'
            ],
        ]
    ]
];