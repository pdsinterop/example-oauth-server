<!doctype html>
<?php

$algorithm = 'sha256';
$header = '{"typ":"JWT", "alg":"HS256"}';

$action = $_GET['action'] ?? 'show';
$encodeSecret = $_GET['encode_secret'] ?? '';
$decodeSecret = $_GET['decode_secret'] ?? '';
$jwt = $_GET['jwt'] ?? null;

$payload = $_GET['payload'] ?? '';

if ($action === 'encode') {
    $unsignedToken = vsprintf('%s.%s', [
        base64_encode($header),
        base64_encode($payload),
    ]);

    $signature = hash_hmac($algorithm, $unsignedToken, $encodeSecret);

    $jwt = vsprintf('%s.%s.%s', [
            base64_encode($header),
            base64_encode($payload),
            base64_encode($signature),
        ]
    );

    $decodeSecret = $encodeSecret;
} elseif ($action === 'decode') {

    [$externalHeader, $externalPayload, $externalSignature] = explode('.', $jwt);

    $header = base64_decode($externalHeader);
    $payload = base64_decode($externalPayload);

    $signedToken = vsprintf('%s.%s', [$externalHeader, $externalPayload]);
    $signature = hash_hmac($algorithm, $signedToken, $decodeSecret);

    $match = (base64_decode($externalSignature) === $signature)
        ? 'match--true'
        : 'match--false'
    ;
} else {
    $payload = json_encode(["admin" => true, "name" => "alex", "sub" => "1234567890",], JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
}
?>
<h1><a href="<?= $_SERVER['PHP_SELF'] ?>"><title>JSON Web Token (JWT) Encode/Decode</title></a></h1>

<section class="<?= $action, ' ', $match ?>">
    <form action="" class="encode-form" method="get">
        <h2>Encode</h2>
        <fieldset>

            <input type="hidden" name="action" value="encode"/>

            <label><strong>Header</strong><input type="text" name="header" value="<?= htmlentities($header) ?>" readonly/></label>
            <label><strong>Payload</strong><textarea name="payload"><?= htmlentities($payload) ?></textarea></label>
            <label><strong>Secret</strong><input type="text" name="encode_secret" value="<?=$encodeSecret?>"/></label>

            <button>Encode</button>
        </fieldset>
    </form>
    <form action="" class="decode-form" method="get">
        <h2>Decode</h2>
        <fieldset>
            <input type="hidden" name="action" value="decode" />
            <input type="hidden" name="encode_secret" value="<?=$encodeSecret?>"/>

            <label><strong>JWT</strong><textarea name="jwt"><?= $jwt ?></textarea></label>
            <label><strong>Secret</strong><input type="text" name="decode_secret" value="<?=$decodeSecret?>"/></label>

            <button>Decode</button>
        </fieldset>
    </form>
</section>

<style>
    a { color:black; }

    body { background-color: #FDFDFD; color: #333; }

    button { bottom: 1em; position: absolute; right: 1em; }

    form { width: 40%; }

    fieldset, input, textarea { border: 1px solid #CCC; }

    fieldset {
        background-color: #FFF;
        height: 100%;
        line-break: anywhere;
        margin: 0;
        overflow: hidden;
        padding: 1em;
        position: relative;
        white-space: normal;
    }

    input, textarea { display: block; margin: 0.5em 1em 1.5em 0; width: calc(100% - 2em); }

    input[readonly] { border-color: white; }

    label { display: block }

    section { display: flex; justify-content: space-evenly; }

    textarea { height: 10em; }

    title { display: inline; }

    .decode.match--false .encode-form fieldset {border-color: #CCC;}
    .decode.match--false fieldset {border-color: #FCC;}
    .decode .encode-form fieldset,
    .encode .decode-form fieldset {border-color: #6F6;}
</style>
