<?php

// Создаем класс для интеграции с AmoCRM
class AmoCRMActions
{
    private $subdomain;
    private $accessToken;

    // Конструктор класса
    public function __construct($subdomain, $accessToken)
    {
        $this->subdomain = $subdomain;
        $this->accessToken = $accessToken;
    }

    // Метод для создания контакта
    public function createContact($name, $email, $phone)
    {
        $contactData = [
            'name' => $name,
            'custom_fields_values' => [
                [
                    'field_id' => 519321,
                    'values' => [
                        [
                            'value' => $email,
                        ],
                    ],
                ],
                [
                    'field_id' => 519323,
                    'values' => [
                        [
                            'value' => $phone,
                        ],
                    ],
                ],
            ],
        ];

        $contactUrl = "https://{$this->subdomain}.amocrm.ru/api/v4/contacts";
        $contactData = json_encode([$contactData]);

        $contactHeaders = [
            "Authorization: Bearer {$this->accessToken}",
            "Content-Type: application/json",
        ];

        $ch = $this->initCurl($contactUrl, "POST", $contactData, $contactHeaders);

        $contactResponse = curl_exec($ch);
        $contactHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($contactHttpCode === 200) {
            $contactData = json_decode($contactResponse, true);
            $contactId = $contactData['_embedded']['contacts'][0]['id'];
            return $contactId;
        } else {
            return false;
        }
    }

    // Метод для создания сделки и привязки контакта к ней
    public function createLead($contactId, $price)
    {
        $leadData = [
            'name' => 'Новая сделка',
            '_embedded' => [
                'contacts' => [
                    [
                        'id' => $contactId,
                    ],
                ],
            ],
            'custom_fields_values' => [
                [
                    'field_id' => 584177,
                    'values' => [
                        [
                            'value' => $price,
                        ],
                    ],
                ],
            ],
        ];

        $leadUrl = "https://{$this->subdomain}.amocrm.ru/api/v4/leads";
        $leadData = json_encode([$leadData]);

        $leadHeaders = [
            "Authorization: Bearer {$this->accessToken}",
            "Content-Type: application/json",
        ];

        $ch = $this->initCurl($leadUrl, "POST", $leadData, $leadHeaders);

        $leadResponse = curl_exec($ch);
        $leadHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($leadHttpCode === 200) {
            return true;
        } else {
            return false;
        }
    }

    // Вспомогательный метод для инициализации cURL
    private function initCurl($url, $method, $data, $headers)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return $ch;
    }
}

// Устанавливаем параметры
$subdomain = 'dimaunychenko';
$accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjI3ZmFmODQ4YzdmNDczNzYwZWQ4NzEzZDUxZDhjMDliNzcxZjJhOTc1YWQ0MjhjZTJhODliNWUwNzQ0ODkxNjVhN2ZiZGVkNmYxNjdmZTg2In0.eyJhdWQiOiIyY2ZjNzAyZC0zODllLTQ0YjgtODc3NC1hNjJmMTQ2NDA3ZjkiLCJqdGkiOiIyN2ZhZjg0OGM3ZjQ3Mzc2MGVkODcxM2Q1MWQ4YzA5Yjc3MWYyYTk3NWFkNDI4Y2UyYTg5YjVlMDc0NDg5MTY1YTdmYmRlZDZmMTY3ZmU4NiIsImlhdCI6MTY5ODE0MTY2MiwibmJmIjoxNjk4MTQxNjYyLCJleHAiOjE2OTgyMjgwNjIsInN1YiI6IjEwMjE4OTEwIiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxMzUyNzEwLCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJwdXNoX25vdGlmaWNhdGlvbnMiLCJmaWxlcyIsImNybSIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiXX0.mEqbzKyNhrFo4U6l1XvhjO9ir30EE91Okrv5wu6OvzNg0xrrTsMaQWzUNEF3WjDF6oPzdjttOKL8SdVP032xz0NnstRZ9xJNIF94vZOgj-YA3WwrjTpfe8p8nNAspSWE8OgG-rQ9iFOXx0-WBu7mVdvFVWgMwJzNP4KPSSTGFl3PGDM8UwimVoallHAMTyMf8uDUFd4o5ItLPAlncs3cSHDT63y_gSWY7V8f7xBa5VHv-LepQx9HtwcL_Hyz63CxWV8U2ihZz9gKHvhTpMwVluTJUDbe3sWIfTF15XprjaL-QKl1WfWtDwEcT1UVw1lqAanOYo1_A80n36Ad3t5EzQ';

// Создаем экземпляр класса AmoCRMActions
$integration = new AmoCRMActions($subdomain, $accessToken);

// Получаем данные из формы
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$price = $_POST['price'];

// Создаем контакт
$contactId = $integration->createContact($name, $email, $phone);

if ($contactId !== false) {
    // Создаем сделку и привязываем контакт к ней
    $result = $integration->createLead($contactId, $price);
    if ($result) {
        echo 'Данные добавлены и контакт привязан к сделке (лиду)!';
    } else {
        echo 'Ошибка при создании сделки (лида).';
    }
} else {
    echo 'Ошибка при создании контакта.';
}
