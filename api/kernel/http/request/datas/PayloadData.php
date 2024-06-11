<?php
/**
 * PostData Gather Request datas were passed to server as body payload
 * @author Aélion <jean-luc.aubert@aelion.fr>
 * @version 1.0.0
 *  - Simple implementation
 */
namespace Aelion\Http\Request\Datas;


use Aelion\Http\Request\Request;

final class PayloadData implements ProcessData {
    private Request $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function process(): void {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (is_array($payload)) {
            foreach ($payload as $key => $value) {
                // Sanitize the input data
                $sanitizedValue = $this->sanitize($value);
                $this->request->set($key, $sanitizedValue);
            }
        }
    }

    private function sanitize($data) {
        // Perform sanitization
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
            return $data;
        } else {
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
    }
}