<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;

class Json
{
    public static function decode(string $data)
    {
        return json_decode($data, true);
    }

    public static function response(mixed $data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return new JsonResponse($data, $status, $headers, $json);
    }
}
