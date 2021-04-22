<?php


namespace VertxOne\Symfony\Atol;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


/**
 * Class Client
 * @package App\Service\Atol
 * Documentation https://online.atol.ru/files/API_atol_online_v4.pdf
 */
class Client
{
    use HostTrait;

    private $apiUrl;

    private $login;
    private $password;
    private $companyGroupName;

    /** @var HttpClientInterface */
    private $httpClient;

    public function __construct(
        string $isTestMode,
        string $login,
        string $password,
        string $companyGroupName,

        HttpClientInterface $httpClient
    )
    {
        $this->isTestMode = $isTestMode;
        $this->login = $login;
        $this->password = $password;
        $this->companyGroupName = $companyGroupName;

        $this->httpClient = $httpClient;

        $this->apiUrl = $this->getHost();
    }

    /**
     * Отправка чека "Приход"
     * @param array $params
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function sell(array $params): array
    {
        return $this->sendDocument('sell', $params);
    }

    /**
     * Отправка чека "Возврат прихода"
     * @param array $params
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function sellRefund(array $params): array
    {
        return $this->sendDocument('sell_refund', $params);
    }

    /**
     * Отправка чека "Коррекция прихода"
     * @param array $params
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function sellCorrection(array $params): array
    {
        return $this->sendDocument('sell_correction', $params);
    }

    /**
     * Отправка чека "Расход"
     * @param array $params
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function buy(array $params): array
    {
        return $this->sendDocument('buy', $params);
    }

    /**
     * Отправка чека "Возврат расхода"
     * @param array $params
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function buyRefund(array $params): array
    {
        return $this->sendDocument('buy_refund', $params);
    }

    /**
     * Отправка чека "Коррекция расхода"
     * @param array $params
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function buyCorrection(array $params): array
    {
        return $this->sendDocument('buy_correction', $params);
    }

    /**
     * Отправка чека
     * @param $document_type
     * @param array $params
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function sendDocument($document_type, array $params): array
    {
        return $this->signedRequest('POST', $document_type, $params)->toArray();
    }


    /**
     * Получение результата обработки документа
     * @param string $uuid
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getReport(string $uuid): array
    {
        return $this->signedRequest('GET', 'report/' . $uuid)->toArray();
    }


    /**
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    private function getToken(): string
    {
        $response = $this->request('POST', 'getToken', [
            'login' => $this->login,
            'pass' => $this->password,
        ]);

        $data = $response->toArray();

        if (!$data || !array_key_exists('token', $data)) {
            throw new RuntimeException("Can't auth with ATOL");
        }

        return $data['token'];
    }

    /**
     * Непосредственно отправка запроса
     * @param string $method
     * @param string $action
     * @param array $params
     * @param string|null $authToken
     * @return bool|mixed|string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function request(string $method, string $action, array $params = [], ?string $authToken = null): ResponseInterface
    {
        $url = $this->apiUrl . '/' . $action;

        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        if (!empty($authToken)) {
            $headers['Token'] = $authToken;
        }

        $options = [
            'headers' => $headers,
        ];

        if ($method === 'POST') {
            $options['json'] = $params;
        }

        return $this->httpClient->request($method, $url, $options);
    }

    /**
     * Отправка запроса, авторизованного токеном
     * @param string $method
     * @param string $action
     * @param array $params
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function signedRequest(string $method, string $action, array $params = []): ResponseInterface
    {
        return $this->request($method, $this->companyGroupName . '/' . $action, $params, $this->getToken());
    }
}
