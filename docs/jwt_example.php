<?php declare(strict_types=1);

/* URL-safe Base64 encode and decode, as PHP does not natively have this function  */
function base64_urlencode($subject): string
{
    return strtr(rtrim(base64_encode($subject), '='), '+/', '-_');
}

function base64_urldecode($subject): string
{
    return base64_decode(strtr($subject, '-_', '+/'));
}

function jwt_encode(string $secret, array $header, array $payload) : string
{
    $header_code = base64_urlencode(json_encode($header));
    $payload_code = base64_urlencode(json_encode($payload));

    $token = vsprintf('%s.%s', [
        $header_code,
        $payload_code,
    ]);

    $signature = hash_hmac('sha256', $token, $secret);

    $signature_code = base64_urlencode($signature);

    return vsprintf('%s.%s.%s', [
            $header_code,
            $payload_code,
            $signature,
        ]
    );
}

function jwt_decode(string $secret, string $jwt) : array
{
    [$header_code, $payload_code, $signature] = explode('.', $jwt);

    $header = json_decode(base64_urldecode($header_code), true);
    $payload = json_decode(base64_urldecode($payload_code), true);

    $available_algorithms = [
        'HS256' => 'sha256',
        'HS512' => 'sha512',
        // ... etc ...
    ];

    $algorithm = $available_algorithms[$header['alg']];

    $token = vsprintf('%s.%s', [
        $header_code,
        $payload_code,
    ]);

    $jwt_signature = hash_hmac($algorithm, $token, $secret);

    if ($jwt_signature !== $signature) {
        throw new \UnexpectedValueException('Signature verification failed!');
    }

    return $payload;
}

$header = ['typ' => 'JWT', 'alg' => 'HS256'];

$payload = [
    'claim_name' => 'claim value'
];

$secret = 'My S3cR3t!';

$jwt = jwt_encode($secret, $header, $payload);

$jwt_payload = jwt_decode($secret, $jwt);

var_dump($jwt, $jwt_payload === $payload, $payload);

/*/
string(158) "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJUaGlzIGlzIjoic29tZSBkYXRhIn0.d885e9952da69fad379d3d6f9fe6b021f45b608d9811682ee0f77b7c3eaa71fd"

bool(true)

array(1) {
    ["This is"]=>
  string(9) "some data"
}

Proof:
 - https://ideone.com/Yw0qg8
 - https://jwt.io/#debugger-io?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJUaGlzIGlzIjoic29tZSBkYXRhIn0.t6axO7Iz-bBeA8d9VdAhNF3gzJtyqlZPOTzdihnEtq4
/*/
