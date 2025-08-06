<?php

namespace App\Presentation\Http\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API Documentation for tasks management system',
    title: 'ESocial Tasks API Documentation',
    contact: new OA\Contact(name: 'Thiago', email: 'thiiagoms@proton.me'),
    license: new OA\License(
        name: 'Apache 2.0',
        url: 'https://www.apache.org/licenses/LICENSE-2.0.html'
    )
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
)]
#[OA\Server(
    url: 'http://localhost:8000/api/documentation',
    description: 'API Documentation Server'
)]
#[OA\Tag(
    name: 'Tasks',
    description: 'API Endpoints'
)]
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
