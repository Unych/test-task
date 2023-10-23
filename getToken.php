<?php
include_once __DIR__ . '/vendor/autoload.php';

// Токен будет храниться в файле token.json
define('TOKEN_FILE', 'token.json');

use AmoCRM\OAuth2\Client\Provider\AmoCRM;

$provider = new AmoCRM([
    'clientId' => 'cdbace27-dc3c-4ad3-bcfc-741e269f9b07',
    'clientSecret' => 'kwjm8NDKaLcLdwV1cGER9v3fp4NFi99ljXjKwh6oy3aXvvu1vljknAf8wo2EtjRt',
    'redirectUri' => 'https://www.amocrm.ru/',
]);
$code = 'def50200495aebc519831f7b248473fd6296b79167cc1157ea25e298410ac518d1490ea6921bb4886e798a6b7154a0f154a3f03d24301daa7eefe1db50d2b42a9ecb3f354721f4bc8b3c3e1a7bde1c738ce1b36baa82a67faad065315f2c3e6a7bc7067eb06638aee6b179a22c6ae7283f6e74323e544a9e4290de58b953a8594cf430db1ee7e6c21c68bc5d9db110b2b9c6f48dc564fb2d85c94fc606d3feee51a2eec5c01855df68fda8170bb013ec9267377ac093211d195315592395b928d9cfacaf9d2fdcb3eb320f12f221ae40e30c1b92d3b0ac67ad3c8012c269cc64556d09a1993ae702aa492d84ce9f3159cf554e931cebf7591c8dce7aa7965eaa9a8ef6f2999f699eb04606990a640158a2e87b717d67bef757ea7d7d9149559c3bfdde14755bc90f67e45d94d0d26f093d7316d15076d6d83be9720e1ad24541e0328c6d3a990eaeaed4fe04606cf8368d00648a38d90325fae34c4104345a534f9d620bd3d3967b6190c60706dc83d23fc7dd442f5db6c50dde0be1e5430e9831874e71f579be12f52777e2093507020ec70f7b7827920179dd6ab93c3eb558e6c870b6db26902ebb791f7fffb5ebb9c3031311d7856eeaeffa1119f1d61bf7e4c470ea374875ea266c8274efa10a6ce99458c8033845957e4002b785d86062f2a06121a20da156';
$provider->setBaseDomain('dimaunychenko.amocrm.ru');

function getAccessToken()
{
    global $provider, $code;
    try {
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);
        saveToken([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires(),
            'baseDomain' => $provider->getBaseDomain(),
        ]);
        die('Файл токена создан');

    } catch (Exception $e) {
        die((string)$e);
    }
}


function getToken()
{
    $accessToken = json_decode(file_get_contents(TOKEN_FILE), true);
    if (
        isset($accessToken)
        && isset($accessToken['accessToken'])
        && isset($accessToken['refreshToken'])
        && isset($accessToken['expires'])
        && isset($accessToken['baseDomain'])
    ) {
        return new  \League\OAuth2\Client\Token\AccessToken([
            'access_token' => $accessToken['accessToken'],
            'refresh_token' => $accessToken['refreshToken'],
            'expires' => $accessToken['expires'],
            'baseDomain' => $accessToken['baseDomain'],
        ]);
    } else {
        echo 'Неверный файл токена';
        getAccessToken();
    }
}

$accessToken = getToken();

function saveToken($accessToken)
{
    if (
        isset($accessToken)
        && isset($accessToken['accessToken'])
        && isset($accessToken['refreshToken'])
        && isset($accessToken['expires'])
        && isset($accessToken['baseDomain'])
    ) {
        $data = [
            'access_token' => $accessToken['accessToken'],
            'refresh_token' => $accessToken['refreshToken'],
            'expires' => $accessToken['expires'],
            'baseDomain' => $accessToken['baseDomain'],
        ];
        file_put_contents(TOKEN_FILE, json_encode($data));
    } else {
        exit('Неверный токен' . var_export($accessToken, true));
    }

}