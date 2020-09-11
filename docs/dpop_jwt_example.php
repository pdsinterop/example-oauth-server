<?php declare(strict_types=1);

namespace Pdsinterop {
    /* URL-safe Base64 encode and decode, as PHP does not natively have this function  */
    class Base64Url
    {
        private const UNSAFE = '+/';
        private const SAFE = '-_';

        public static function encode($subject) : string
        {
            return strtr(rtrim(base64_encode($subject), '='), self::UNSAFE, self::SAFE);
        }

        public static function decode($subject) : string
        {
            return base64_decode(strtr($subject, self::SAFE, self::UNSAFE));
        }
    }
}

// !!! Do not allow the string sent to the Parser to dictate which signature algorithm to use, or else your application will be vulnerable to a critical JWT security vulnerability.

namespace Pdsinterop\Dpop {

    use Lcobucci\JWT\Builder;
    use Lcobucci\JWT\Signer;
    use Lcobucci\JWT\Signer\Key;
    use Lcobucci\JWT\Token;
    use Pdsinterop\Base64Url;

    class Dpop
    {
        /** @var Builder */
        private $builder;
        /** @var Signer */
        private $signer;
        /** @var \Lcobucci\JWT\ValidationData */
        private $validator;

        public function __construct(Builder $builder, Signer $signer, \Lcobucci\JWT\ValidationData $validator)
        {
            $this->builder = $builder;
            $this->signer = $signer;
            $this->validator = $validator;
        }

        /**
         * A DPoP proof is a JWT ([RFC7519]) that is signed (using JWS, [RFC7515]) using a private key chosen by the client.
         *
         * @param Key $privateKey
         *
         * @return Token
         */
        public function createProof(Key $publicKey, Key $privateKey) :  Token
        {
            $builder = $this->builder;
            $signer = $this->signer;

// -----------------------------------------------------------------------------
            $seed = random_bytes(64);
            $identifier = base64_urlencode($seed);
            // The HTTP method for the request to which the JWT is attached
            $http_method = 'POST';

            $jwk = $this->createJwk($publicKey);

            $headers = [
                'alg' => $signer->getAlgorithmId(),
                'jwk' => $jwk,
                'typ' => 'dpop+jwt',
            ];

            array_walk($headers, function ($name, $value) use (&$builder) {
                $builder = $builder->withHeader($name, $value);
            });

            $payload = [
                'htm' => $http_method,
                'htu' => 'The HTTP URI used for the request  (without query and fragment parts)',
                'iat' => 'Time at which the JWT was created',
                'jti' => $identifier,
            ];
// -----------------------------------------------------------------------------
            $time = time();

            // Add claims
            $builder
                -> canOnlyBeUsedAfter($time + 60) // 'nbf': Configures the time that the token can be used
                -> expiresAt($time + 3600) // 'exp': Configures the expiration time of the token
                -> identifiedBy('4f1g23a12aa', true) // 'jti': Configures the id, replicating as a header item
                -> issuedAt($time) // 'iat': Configures the time that the token was issue
                -> issuedBy('http://example.com') // 'iss': Configures the issuer
                -> permittedFor('http://example.org') // 'aud': Configures the audience
                // -> withClaim('uid', 1) // custom "uid"
            ;

            // Retrieves the generated token
            return $builder->getToken($signer, $privateKey);
        }

        public function checkProof()
        {
            /*/
               To check if a string that was received as part of an HTTP Request is
               a valid DPoP proof, the receiving server MUST ensure that

               1.  the string value is a well-formed JWT,

               2.  all required claims are contained in the JWT,

               3.  the "typ" field in the header has the value "dpop+jwt",

               4.  the algorithm in the header of the JWT indicates an asymmetric
                   digital signature algorithm, is not "none", is supported by the
                   application, and is deemed secure,

               5.  that the JWT is signed using the public key contained in the
                   "jwk" header of the JWT,

               6.  the "htm" claim matches the HTTP method value of the HTTP request
                   in which the JWT was received (case-insensitive),

               7.  the "htu" claims matches the HTTP URI value for the HTTP request
                   in which the JWT was received, ignoring any query and fragment
                   parts,

               8.  the token was issued within an acceptable timeframe (see
                   Section 9.1), and

               9.  that, within a reasonable consideration of accuracy and resource
                   utilization, a JWT with the same "jti" value has not been
                   received previously (see Section 9.1).

            /*/
        }

        /**
         * As the JWT library does not yet hae support for JWK, a custom solution is used for now.
         *
         * @param Key $publicKey
         *
         * @return array
         */
        private function createJwk(Key $publicKey) : array
        {
            $certificate = $publicKey->getContent();

            $key = openssl_pkey_get_public($certificate);

            $keyInfo = openssl_pkey_get_details($key);

            return [
                'keys' => [
                    [
                        'kty' => 'RSA',
                        'n' => Base64Url::encode($keyInfo['rsa']['n']),
                        'e' => Base64Url::encode($keyInfo['rsa']['e']),
                    ],
                ],
            ];

        }
    }
}

namespace {
/*
    $public_key_path = dirname(__DIR__) . '/tests/fixtures/keys/public.key';
    $private_key_path = dirname(__DIR__) . '/tests/fixtures/keys/private.key';
    $public_key = file_get_contents($public_key_path);
    $private_key = file_get_contents($private_key_path);
*/

    $secret = 'My S3cR3t!';

    $key = new \Lcobucci\JWT\Signer\Key($secret); // file content OR file path 'file://'

    $dpop = new \Pdsinterop\Dpop\Dpop(
        new \Lcobucci\JWT\Builder(),
        new \Lcobucci\JWT\Signer\Hmac\Sha256(),
        new \Lcobucci\JWT\ValidationData()
    );

    $jwt = $dpop->createProof($key);
}
