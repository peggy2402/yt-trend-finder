<?php

return [
    'plans' => [
        'free' => [
            'name' => 'Dùng thử (Free)',
            'daily_scans' => 10, // Đã nâng lên 10 theo yêu cầu của bạn
            'can_export' => false,
            'ai_analysis' => false,
            'price' => 0,
        ],
        'basic' => [
            'name' => 'Cơ bản (Basic)',
            'daily_scans' => 50,
            'can_export' => true,
            'ai_analysis' => false,
            'price' => 20000,
        ],
        'pro' => [
            'name' => 'Chuyên nghiệp (Pro)',
            'daily_scans' => 200,
            'can_export' => true,
            'ai_analysis' => true,
            'price' => 79000,
        ],
        'premium' => [
            'name' => 'Cao cấp (Premium)',
            'daily_scans' => 1000,
            'can_export' => true,
            'ai_analysis' => true,
            'price' => 150000,
        ]
    ]
];
