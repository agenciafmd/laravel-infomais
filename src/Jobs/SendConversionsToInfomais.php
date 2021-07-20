<?php

namespace Agenciafmd\Infomais\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cookie;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SendConversionsToInfomais implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function handle()
    {
        if (!config('laravel-infomais.integration_url')) {
            return false;
        }

        $client = $this->getClientRequest();

        $options = [
            'headers' => [
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ],
            'json' => $this->data,
        ];

        $client->post('/V1/API/Simulador/GetDadosLandPage', $options);

    }

    private function getClientRequest()
    {
        $logger = new Logger('Infomais');
        $logger->pushHandler(new StreamHandler(storage_path('logs/infomais-' . date('Y-m-d') . '.log')));

        $stack = HandlerStack::create();
        $stack->push(
            Middleware::log(
                $logger,
                new MessageFormatter("{method} {uri} HTTP/{version} {req_body} | RESPONSE: {code} - {res_body}")
            )
        );

        return new Client([
            'base_uri' => config('laravel-infomais.integration_url'),
            'timeout' => 60,
            'connect_timeout' => 60,
            'http_errors' => false,
            'verify' => false,
            'handler' => $stack,
        ]);
    }
}
