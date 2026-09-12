<?php

require_once __DIR__ . "/functions.php";

function falRequest($method, $url, $body = null) {

    $key = trim(getSetting("fal_key"));

    if ($key === "") {
        return [
            "ok" => false,
            "error" => "fal API key is not configured."
        ];
    }

    $ch = curl_init($url);

    $headers = [
        "Authorization: Key " . $key,
        "Content-Type: application/json"
    ];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_CONNECTTIMEOUT => 20
    ]);

    if ($body !== null) {

        $json = json_encode(
            $body,
            JSON_UNESCAPED_SLASHES
        );

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            $json
        );
    }

    $raw = curl_exec($ch);

    $err = curl_error($ch);

    $code = curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    curl_close($ch);

    if ($err) {
        return [
            "ok" => false,
            "error" => "cURL: " . $err
        ];
    }

    $data = json_decode(
        $raw,
        true
    );

    if ($code < 200 || $code >= 300) {

        $msg =
            $data["detail"]
            ?? $data["message"]
            ?? $data["error"]
            ?? ("HTTP " . $code);

        return [
            "ok" => false,
            "error" => $msg,
            "data" => $data
        ];
    }

    return [
        "ok" => true,
        "data" => $data
    ];
}

function falSubmitImageToVideo(
    $imagePath,
    $prompt
) {

    $mime = "";

    if (function_exists("finfo_open")) {

        $fi = finfo_open(
            FILEINFO_MIME_TYPE
        );

        $mime = finfo_file(
            $fi,
            $imagePath
        );

        finfo_close($fi);

    } elseif (
        function_exists("mime_content_type")
    ) {

        $mime = mime_content_type(
            $imagePath
        );
    }

    if (!$mime) {
        return [
            "ok" => false,
            "error" => "Could not detect image type."
        ];
    }

    $bytes = file_get_contents(
        $imagePath
    );

    if ($bytes === false) {
        return [
            "ok" => false,
            "error" => "Could not read uploaded image."
        ];
    }

    $dataUri =
        "data:" .
        $mime .
        ";base64," .
        base64_encode($bytes);

    $model = getSetting(
        "fal_model"
    );

    if (!$model) {
        $model =
            "wan/v2.6/image-to-video/flash";
    }

    $payload = [

        "prompt" => $prompt,

        "image_url" => $dataUri,

        "resolution" => "720p",

        "duration" => "5",

        "generate_audio" => false,

        "enable_prompt_expansion" => true,

        "enable_safety_checker" => true
    ];

    $r = falRequest(
        "POST",
        "https://queue.fal.run/" . $model,
        $payload
    );

    if (!$r["ok"]) {
        return $r;
    }

    $id =
        $r["data"]["request_id"]
        ?? "";

    if (!$id) {

        return [
            "ok" => false,
            "error" =>
                "fal did not return a request_id."
        ];
    }

    return [
        "ok" => true,
        "request_id" => $id
    ];
}

function falStatus($requestId) {

    $model = getSetting(
        "fal_model"
    );

    if (!$model) {
        $model =
            "wan/v2.6/image-to-video/flash";
    }

    return falRequest(
        "GET",
        "https://queue.fal.run/" .
        $model .
        "/requests/" .
        rawurlencode($requestId) .
        "/status?logs=1"
    );
}

function falResult($requestId) {

    $model = getSetting(
        "fal_model"
    );

    if (!$model) {
        $model =
            "wan/v2.6/image-to-video/flash";
    }

    return falRequest(
        "GET",
        "https://queue.fal.run/" .
        $model .
        "/requests/" .
        rawurlencode($requestId)
    );
}

?>