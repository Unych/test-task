<?php

$subdomain = 'dimaunychenko'; //Поддомен нужного аккаунта

$access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImZmZDFjNjljNDM2YTQ0ZWNhMWY5YzdkMGFjMTcxNWJjYjcyNWJmOGU0OTIxYzVkZmVmMWYyODRlNTRjNjBiZjRkMjQ0MjhmZjlkMWFiMDQyIn0.eyJhdWQiOiJjZGJhY2UyNy1kYzNjLTRhZDMtYmNmYy03NDFlMjY5ZjliMDciLCJqdGkiOiJmZmQxYzY5YzQzNmE0NGVjYTFmOWM3ZDBhYzE3MTViY2I3MjViZjhlNDkyMWM1ZGZlZjFmMjg0ZTU0YzYwYmY0ZDI0NDI4ZmY5ZDFhYjA0MiIsImlhdCI6MTY5ODA2MjU3NiwibmJmIjoxNjk4MDYyNTc2LCJleHAiOjE2OTgxNDg5NzYsInN1YiI6IjEwMjE4OTEwIiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxMzUyNzEwLCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJwdXNoX25vdGlmaWNhdGlvbnMiLCJmaWxlcyIsImNybSIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiXX0.LqOuGV1NqQfKmaEKh54C-CdbiAaH1iXMB0MngDbcbAQzzXHHKpVgQnsqWKZVbfbW1g_8HvsDkGe1kig18TvgOMp7w8_G2pYUnTAiWhp3SIPCKhqfcPXzgqh2GsshRz7C9V4yrIXJhRzx92OoTOm-asGT1g4yn-GZD6x8soQuMFiLgs2dvTRD2F7W5f8NiznoS6oaCAG4UwZ1DV5jBuZ3hr4fVhxgLPstJqiGWBQM0Vld_a6VhvC-HBpsJdRssAwHibEwEogfA9Um8su9MvTD05yDh0MpvGdZfgnMzkGytYZzQexpq7T_FY_zfQKPufVHoxqWgz2-VfVktwZ2KBvJ0w';


    // Получаем данные из формы
    $name = $_POST['name'];
    $price = $_POST['price'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Создаем массив данных для создания контакта
    $contact_data = [
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

    // Создаем контакт в AmoCRM
    $contact_url = "https://$subdomain.amocrm.ru/api/v4/contacts";
    $contact_data = json_encode([$contact_data]);

    $contact_headers = [
        "Authorization: Bearer $access_token",
        "Content-Type: application/json",
    ];

    $ch = curl_init($contact_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $contact_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $contact_headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $contact_response = curl_exec($ch);
    $contact_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($contact_http_code === 200) {
        $contact_data = json_decode($contact_response, true);
        $contact_id = $contact_data['_embedded']['contacts'][0]['id'];

        // Создаем массив данных для создания сделки и привязываем к ней контакт
        $lead_data = [
            'name' => 'Новая сделка',
            '_embedded' => [
                'contacts' => [
                    [
                        'id' => $contact_id,
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

        $lead_url = "https://$subdomain.amocrm.ru/api/v4/leads";
        $lead_data = json_encode([$lead_data]);

        $lead_headers = [
            "Authorization: Bearer $access_token",
            "Content-Type: application/json",
        ];

        $ch = curl_init($lead_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $lead_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $lead_headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $lead_response = curl_exec($ch);
        $lead_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($lead_http_code === 200) {
            echo 'Данные добавлены и контакт привязан к сделке (лиду)!';
        } else {
            echo 'Ошибка при создании сделки (лида): ' . $lead_http_code;
        }
    } else {
        echo 'Ошибка при создании контакта: ' . $contact_http_code;
    }



