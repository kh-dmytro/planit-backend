<?php 
return [

'annotations' => [
    base_path('app'),
],
'documentation' => [
    'route' => 'api/documentation',
    'middleware' => [
        // Добавьте сюда middleware, если нужно
    ],
],
'paths' => [
    'docs' => 'api-docs.json',
],
];