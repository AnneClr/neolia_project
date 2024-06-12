<?php
namespace Aelion\Http\Request\Datas\Test\PostDataTest;

use PHPUnit\Framework\TestCase;
use Aelion\Http\Request\Datas\PostData;
use Aelion\Http\Request\Request;
use Aelion\Kernel;



class PostDataTest extends TestCase
{
    public function testProcessSanitizesInput()
    {
        // Mock the Kernel object
        $kernel = $this->createMock(Kernel::class);

        // Create an instance of Request with the mocked Kernel
        $request = new Request($kernel);

        // Create an instance of PostData with the Request
        $postData = new PostData($request);

        // Define potentially dangerous POST data
        $_POST = [
            'test' => '<script>alert("xss")</script>'
        ];

        // Call the process method to sanitize the input
        $postData->process();

        // Retrieve the processed POST data
        $processedData = $request->getPayload();

        // Check that the data has been sanitized correctly
        $this->assertEquals('alert("xss")', $processedData['test']);
    }
}
