<?php

declare(strict_types=1);

namespace ResourceController\Tests\Traits;

trait WithRequestHeaders
{
    /**
     * @return array
     */
    public static function xRequestWithAjax(): array
    {
        return ['X-Requested-With' => 'XMLHttpRequest',];
    }

    /**
     * @return array
     */
    public static function requestAcceptJson(): array
    {
        return ['Accept' => 'application/json',];
    }

    /**
     * @return array
     */
    public static function requestContentTypeFormUrlencoded(): array
    {
        return ['Content-Type' => 'application/x-www-form-urlencoded'];
    }

    /**
     * @return array
     */
    public static function xCsrfToken(): array
    {
        return ['X-CSRF-TOKEN' => csrf_token(),];
    }

    /**
     * @param  bool  $ajax
     * @param  bool  $json
     * @param  bool  $token
     * @return array
     */
    protected function getAcceptJsonFormHeaders(bool $ajax = true, bool $json = true, bool $token = true)
    {
        return $this->getFormHeaders($ajax, $json, $token);
    }

    /**
     * @param  bool  $ajax
     * @param  bool  $json
     * @param  bool  $token
     * @return array
     */
    protected function getAcceptJsonHeaders(bool $ajax = true, bool $json = true, bool $token = true)
    {
        return $this->getRequestHeaders($ajax, $json, $token);
    }

    /**
     * @param  bool  $ajax
     * @param  bool  $json
     * @param  bool  $token
     * @return array
     */
    protected function getFormHeaders(bool $ajax = false, bool $json = false, bool $token = true)
    {
        $form = static::requestContentTypeFormUrlencoded();

        return $this->getRequestHeaders($ajax, $json, $token) + $form;
    }

    /**
     * @param  bool  $ajax
     * @param  bool  $json
     * @param  bool  $token
     * @return array
     */
    protected function getRequestHeaders(bool $ajax = false, bool $json = false, bool $token = true): array
    {
        $headers = [];
        $optionalHeaders = [
            'ajax' => static::xRequestWithAjax(),
            'json' => static::requestAcceptJson(),
            'token' => static::xCsrfToken(),
        ];
        foreach (compact('ajax', 'json', 'token') as $name => $shouldInclude) {
            if ($shouldInclude && isset($optionalHeaders[$name])) {
                $headers = $headers + $optionalHeaders[$name];
            }
        }

        return $headers;
    }
}