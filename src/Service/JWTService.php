<?php

namespace App\Service;

/**
 * JSON Web Token (JWT) Service.
 */
class JWTService
{
    /**
     * Generates a JSON Web Token (JWT) with the provided header, payload, and secret.
     *
     * @param mixed $header   the JWT header data
     * @param mixed $payload  the JWT payload data
     * @param mixed $secret   the secret key used for token signing
     * @param mixed $validity the validity period of the token in seconds (default is 3 hours)
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        // Set token expiration if validity period is provided
        if ($validity > 0) {
            $now = new \DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        // Encode header and payload to base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // Clean encoded values (replace characters not allowed in URL : + / = )
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // Encode the secret key
        $secret = base64_encode($secret);

        // Generate the signature
        $signature = hash_hmac(
            'sha256',
            $base64Header.'.'.$base64Payload,
            $secret,
            true
        );

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // Combine header, payload, and signature to create the JWT
        $jwt = $base64Header.'.'.$base64Payload.'.'.$base64Signature;

        return $jwt;
    }

    /**
     * Checks if the provided token is in a valid format.
     *
     * @param mixed $token the token to validate
     *
     * @return bool true if the token is in a valid format, false otherwise
     */
    public function isValid(string $token): bool
    {
        // Use regular expression to validate the token format
        // Token format should consist of three parts separated by dots
        // Each part should contain only alphanumeric characters, hyphens, underscores, or equal signs
        return 1 === preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        );
    }

    /**
     * Extracts and decodes the header part of the JWT.
     *
     * @param mixed $token the JWT from which to extract the header
     *
     * @return array the decoded header as an associative array
     */
    public function getHeader(string $token): array
    {
        // Split the token into its components
        $array = explode('.', $token);

        // Decode the base64-encoded header and convert it to an associative array
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    /**
     * Extracts and decodes the payload part of the JWT.
     *
     * @param mixed $token the JWT from which to extract the payload
     *
     * @return array the decoded payload as an associative array
     */
    public function getPayload(string $token): array
    {
        // Split the token into its components
        $array = explode('.', $token);

        // Decode the base64-encoded payload and convert it to an associative array
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    /**
     * Checks if the provided JWT token has expired.
     *
     * @param mixed $token the JWT token to check for expiration
     *
     * @return bool true if the token is expired, false otherwise
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new \DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    /**
     * Checks if the provided token matches the token generated using the given secret.
     *
     * @param mixed $token  the JWT token to check
     * @param mixed $secret the secret key used for generating the token
     *
     * @return bool true if the provided token matches the generated token, false otherwise
     */
    public function check(string $token, string $secret): bool
    {
        // Retrieve the header and payload from the token
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // Generate a verification token using the provided header, payload, and secret
        $verifToken = $this->generate($header, $payload, $secret, 0);

        // Check if the provided token matches the generated verification token
        return $token === $verifToken;
    }
}
