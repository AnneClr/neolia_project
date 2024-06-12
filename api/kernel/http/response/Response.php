<?php
/**
 * Response interface
 * @author Aélion <jean-luc.aubert@aelion.fr>
 * @version 1.0.0
 *  - Simple interface
 */
namespace Aelion\Http\Response;

interface Response {

}

class JsonResponse implements Response {
    public static function sendJson($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}