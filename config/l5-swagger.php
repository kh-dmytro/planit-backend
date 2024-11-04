<?php 
return [

    'paths' => [
        'annotations' => base_path('app/Http/Controllers'), // Путь к аннотациям
        'docs' => 'api-docs.json', // Путь к файлу сгенерированной документации
    ],

    'documentation' => [
        'route' => 'api/documentation', // Маршрут для просмотра документации
        'middleware' => [
            // Добавьте сюда middleware, если нужно, например 'auth'
        ],
    ],

];
