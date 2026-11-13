<?php
return [
    'api_key' => function_exists('env')
        ? (string) env('OPENROUTER_API_KEY', getenv('OPENROUTER_API_KEY') ?: '')
        : (getenv('OPENROUTER_API_KEY') ?: ''),
    'model' => 'openrouter/free',
];
