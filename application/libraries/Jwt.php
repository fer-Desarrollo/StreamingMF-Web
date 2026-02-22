<?php
class Jwt {

    private $secret;

    public function __construct()
    {
        $this->secret = config_item('encryption_key');
    }

    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64url_decode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function encode($payload, $minutes = 120)
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload['iat'] = time();
        $payload['exp'] = time() + ($minutes * 60);

        $h = $this->base64url_encode(json_encode($header));
        $p = $this->base64url_encode(json_encode($payload));

        $s = hash_hmac('sha256', "$h.$p", $this->secret, true);
        $s = $this->base64url_encode($s);

        return "$h.$p.$s";
    }

    public function decode($jwt)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return false;

        [$h, $p, $s] = $parts;

        $valid = hash_equals(
            $this->base64url_encode(
                hash_hmac('sha256', "$h.$p", $this->secret, true)
            ),
            $s
        );

        if (!$valid) return false;

        $payload = json_decode($this->base64url_decode($p), true);
        if ($payload['exp'] < time()) return false;

        return $payload;
    }
}