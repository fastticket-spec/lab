<?php

namespace App\Http\Responses;

use App\Helpers\Flash;
use Illuminate\Http\Response;
use Inertia\Inertia;

trait HttpResponse
{
    /**
     * @param null $data
     * @param int $statusCode
     * @param null $flashMessage
     * @param string $messageType
     * @param string $component
     * @param string $returnType
     * @param array $headers
     * @return mixed
     */
    public function view($data = null, int $statusCode = Response::HTTP_OK, $flashMessage = null, $messageType = 'success', string $component = '', string $returnType = '', array $headers = []): mixed
    {
        if (request()->wantsJson()) {
            return response()->json($data, $statusCode, $headers);
        }

        if ($flashMessage) {
            Flash::notify($flashMessage, $messageType);
        }

        return $returnType === 'redirect'
            ? redirect($component)
            : Inertia::render($component, [...$data, 'code' => $statusCode]);
    }
}
