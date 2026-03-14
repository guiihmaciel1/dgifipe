<?php

return [

    'cities' => [
        'São José do Rio Preto',
        'Mirassol',
        'Santa Fé do Sul',
    ],

    'listing_lookback_days' => 7,

    'min_listings_warning' => 5,

    'trim_percentage' => 20,

    'default_condition_discounts' => [
        'no_box' => 2,
        'no_cable' => 1,
        'screen_replaced' => 8,
        'face_id_issue' => 15,
    ],

    'default_depreciation_rules' => [
        ['min' => 90, 'max' => 100, 'discount' => 0],
        ['min' => 80, 'max' => 89,  'discount' => 3],
        ['min' => 70, 'max' => 79,  'discount' => 7],
        ['min' => 0,  'max' => 69,  'discount' => 12],
    ],

    'models' => [
        'iPhone 11'         => ['64GB', '128GB', '256GB'],
        'iPhone 11 Pro'     => ['64GB', '256GB', '512GB'],
        'iPhone 11 Pro Max' => ['64GB', '256GB', '512GB'],

        'iPhone 12 mini'    => ['64GB', '128GB', '256GB'],
        'iPhone 12'         => ['64GB', '128GB', '256GB'],
        'iPhone 12 Pro'     => ['128GB', '256GB', '512GB'],
        'iPhone 12 Pro Max' => ['128GB', '256GB', '512GB'],

        'iPhone 13 mini'    => ['128GB', '256GB', '512GB'],
        'iPhone 13'         => ['128GB', '256GB', '512GB'],
        'iPhone 13 Pro'     => ['128GB', '256GB', '512GB', '1TB'],
        'iPhone 13 Pro Max' => ['128GB', '256GB', '512GB', '1TB'],

        'iPhone 14'         => ['128GB', '256GB', '512GB'],
        'iPhone 14 Plus'    => ['128GB', '256GB', '512GB'],
        'iPhone 14 Pro'     => ['128GB', '256GB', '512GB', '1TB'],
        'iPhone 14 Pro Max' => ['128GB', '256GB', '512GB', '1TB'],

        'iPhone 15'         => ['128GB', '256GB', '512GB'],
        'iPhone 15 Plus'    => ['128GB', '256GB', '512GB'],
        'iPhone 15 Pro'     => ['128GB', '256GB', '512GB', '1TB'],
        'iPhone 15 Pro Max' => ['256GB', '512GB', '1TB'],

        'iPhone 16'         => ['128GB', '256GB', '512GB'],
        'iPhone 16 Plus'    => ['128GB', '256GB', '512GB'],
        'iPhone 16 Pro'     => ['128GB', '256GB', '512GB', '1TB'],
        'iPhone 16 Pro Max' => ['256GB', '512GB', '1TB'],
        'iPhone 16e'        => ['128GB', '256GB', '512GB'],

        'iPhone 17'         => ['128GB', '256GB', '512GB'],
        'iPhone 17 Air'     => ['128GB', '256GB', '512GB'],
        'iPhone 17 Pro'     => ['128GB', '256GB', '512GB', '1TB'],
        'iPhone 17 Pro Max' => ['256GB', '512GB', '1TB'],
    ],

];
