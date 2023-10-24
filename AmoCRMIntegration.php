<?php
include_once __DIR__ . '/vendor/autoload.php';

use AmoCRM\OAuth2\Client\Provider\AmoCRM;

class AmoCRMIntegration
{
    private $provider;
    private $tokenFile;

    public function __construct($clientId, $clientSecret, $redirectUri, $baseDomain)
    {
        // Создаем экземпляр AmoCRM Provider с переданными параметрами
        $this->provider = new AmoCRM([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
        ]);

        // Устанавливаем базовый домен
        $this->provider->setBaseDomain($baseDomain);

        // Определяем имя файла для хранения токена
        $this->tokenFile = 'token.json';
    }

    public function getAccessToken($code)
    {
        try {
            // Запрашиваем токен с использованием кода авторизации
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            // Сохраняем полученный токен
            $this->saveToken([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $this->provider->getBaseDomain(),
            ]);

            // Возвращаем полученный токен
            return $accessToken;
        } catch (Exception $e) {
            die((string)$e); // Обработка ошибки
        }
    }

    private function saveToken($accessTokenData)
    {
        if (
            isset($accessTokenData)
            && isset($accessTokenData['accessToken'])
            && isset($accessTokenData['refreshToken'])
            && isset($accessTokenData['expires'])
            && isset($accessTokenData['baseDomain'])
        ) {
            // Сохраняем данные токена в файл
            $data = [
                'access_token' => $accessTokenData['accessToken'],
                'refresh_token' => $accessTokenData['refreshToken'],
                'expires' => $accessTokenData['expires'],
                'baseDomain' => $accessTokenData['baseDomain'],
            ];
            file_put_contents($this->tokenFile, json_encode($data));
        } else {
            exit('Неверный токен' . var_export($accessTokenData, true));
        }
    }
}

// Использование класса AmoCRMIntegration
$clientId = '2cfc702d-389e-44b8-8774-a62f146407f9';
$clientSecret = 'Y3rDfzVe335Bj45b2s78v1pCy37lfmidDeQsKkNSUYAgEWuv9KLZwcyWQBBVyYw4';
$redirectUri = 'https://www.amocrm.ru';
$baseDomain = 'dimaunychenko.amocrm.ru';

$code = 'def5020065231255e13de3953a198f14f6cfb364eb263240a5ccba96e03c8210c78a582711f6cc125bb633c3ef138611122884d202048c0ed5d779d05136807088cc872e7f8b8f7a58e529c6b139bc42a9bb44626d329803706cfe0307c36d806443644d53f2b9bbdba1290867d434f0dad0272cb531a3e1a1c28a9b5761821a86ee3b2e0298a2f66a519665ea08fcbe6e46cd7a8f99fa61645c46f26a063f6675d18323cf3338db662e87737d5da69c440b83129b467ad0dc2781002550566e6c9e755e6e826d99c3ab2ce09e5ec9e1246eb60e69c1fd7715da0b98d944eed0e07b92ef8cd30be09427d22addc47490e1b5a9ac5606f7e687aaeaeff2480d64bf9c3dc5e47f082ffde34206731c780791ed626131bf054bff87d62dbe00fe029cb7868b196ceac1eba60ad8ff5c4a30ae0f8bc77841539c563b277177751b30a3eb5cdf04ed8a96e7c389e5a162114476afc79372c6a5eeef62b92472f91ef36dcf321dfa14cf64f8ccd621386bf24d51ed750bf177eb52d49921a2be40d918b5c3d22472fa08d2c6e6f141c0081b9297d1a841932510bc7d56edfadfd019996c09a5666dfb48ae424e0e23c8f9f8ffba34f103bfeda9d0d7580b3c0b75226ad77766504181e8337cdde9119490b7fdc85d52f8a61f6479fe56ffbde2dfdaa1348153a8f02d';

$integration = new AmoCRMIntegration($clientId, $clientSecret, $redirectUri, $baseDomain);

$accessToken = $integration->getAccessToken($code);
echo 'Токен получен';
