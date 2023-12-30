<?php

return [
    1 => [
        'name' => 'Super Admin',
        'permissions' => [
            'dashboard',
            'inventory', 'inventory-manage', 'inventory-export',
            'purchase', 'purchase-manage', 'purchase-export',
            'sales', 'sales-manage', 'sales-export'
        ]
    ],
    2 => [
        'name' => 'Sales',
        'permissions' => [
            'dashboard',
            'sales', 'sales-manage', 'sales-export',
            'inventory'
        ]
    ],
    3 => [
        'name' => 'Purchase',
        'permissions' => [
            'dashboard',
            'purchase', 'purchase-manage', 'purchase-export',
        ]
    ],
    4 => [
        'name' => 'Manager',
        'permissions' => [
            'dashboard',
            'purchase','purchase-export',
            'sales', 'sales-export',
        ]
    ],
];