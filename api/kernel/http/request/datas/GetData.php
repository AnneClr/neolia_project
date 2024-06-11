<?php
/**
 * PostData Gather Request datas were passed to server by querystring (GET)
 * @author Aélion <jean-luc.aubert@aelion.fr>
 * @version 1.0.0
 *  - Simple implementation
 */
namespace Aelion\Http\Request\Datas;


use Aelion\Http\Request\Request;

final class GetData implements ProcessData {
    private Request $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function process(): void {
        foreach ($_GET as $key => $value) {
            // Sanitize the input data
            $sanitizedValue = $this->sanitize($value);
            $this->request->set($key, $sanitizedValue);
        }
    }

    private function sanitize($data) {
        // Perform sanitization
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}