<?php

namespace Src\Services;

use Analog\Analog;
use Illuminate\Database\Capsule\Manager as Capsule;
use Src\Models\Request;
use Src\Models\ResponseHeaders;
use Throwable;

class UrlService
{
    private Capsule $capsule;

    public function __construct(Capsule $capsule)
    {
        $this->capsule = $capsule;
    }

    /**
     * @throws Throwable
     */
    public function updateUrl(
        string $url,
        string|null $body,
        int|null $statusCode,
        string $curlStatus,
        array $headers
    ): bool {
        $this->capsule::connection()->beginTransaction();
        try {
            $request = Request::create([
                'request_url' => $url,
                'body' => $body,
                'status_code' => $statusCode,
                'curl_status' => $curlStatus
            ]);
            $requestId = $request->id;
            $request->save();

            $this->handleRequestHeaders($headers, $requestId);
            $this->capsule::connection()->commit();
            return true;
        } catch (\Exception $exception) {
            Analog::log([
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTraceAsString(),
            ]);
            $this->capsule::connection()->rollBack();
            return false;
        }
    }

    private function handleRequestHeaders(array $headers, int $requestId)
    {
        if (empty($headers)) {
            return;
        }
        foreach ($headers as $key => $value) {
            $requestHeaders = ResponseHeaders::create([
                'request_id' => $requestId,
                'header_key' => $key,
                'header_value' => $value[0]
            ]);
            $requestHeaders->save();
        }
    }
}
