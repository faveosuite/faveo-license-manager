<?php

namespace Tests\Unit\Backend\Admin;

use App\Models\LogCategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Logger;
use Exception;
use Tests\TestCase;

class LogViewControllerTest extends TestCase
{
    use DatabaseTransactions;
    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }
    public function test_getExceptionLogs_whenNoParametersIsPassed()
    {
        Logger::exception(new Exception('test_exception_1'));
        Logger::exception(new Exception('test_exception_2'));

        $response = $this->call('GET', 'api/admin/logs/exception');
        $response->assertStatus(200);
        $exceptions = json_decode($response->getContent())->data->data;
        $this->assertCount(2, $exceptions);
        if($exceptions[0]->message == 'test_exception_1') {
            $this->assertEquals('test_exception_1', $exceptions[0]->message);
            $this->assertEquals('test_exception_2', $exceptions[1]->message);
        }
        else{
            $this->assertEquals('test_exception_2', $exceptions[0]->message);
            $this->assertEquals('test_exception_1', $exceptions[1]->message);
        }
    }

    #[\PHPUnit\Framework\Attributes\Group('getExceptionLogs')]
    public function test_getExceptionLogs_whenSearchQueryIsPassed()
    {
        Logger::exception(new Exception('test_exception_1'));
        Logger::exception(new Exception('test_exception_2'));

        $response = $this->call('GET', 'api/admin/logs/exception', ['search_query' => 'exception_1']);
        $response->assertStatus(200);
        $exceptions = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $exceptions);
        $this->assertEquals('test_exception_1', $exceptions[0]->message);
    }

    #[\PHPUnit\Framework\Attributes\Group('getExceptionLogs')]
    public function test_getExceptionLogs_whenLimitIsPassed()
    {
        Logger::exception(new Exception('test_exception_1'));
        Logger::exception(new Exception('test_exception_2'));

        $response = $this->call('GET', 'api/admin/logs/exception', ['perPage' => 1]);
        $response->assertStatus(200);
        $exceptions = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $exceptions);
    }

    #[\PHPUnit\Framework\Attributes\Group('getExceptionLogs')]
    public function test_getExceptionLogs_whenStartTimeIsPassed()
    {
        Logger::exception(new Exception('test_exception_1'));
        Logger::exception(new Exception('test_exception_2'));
        $categoryId = LogCategory::where('name', 'default')->value('id');

        $response = $this->call('GET', 'api/admin/logs/exception', ['search_query' => '3000-11-27']);
        $response->assertStatus(200);
        $exceptions = json_decode($response->getContent())->data->data;
        $this->assertCount(0, $exceptions);
    }

    #[\PHPUnit\Framework\Attributes\Group('getExceptionLogs')]
    public function test_getExceptionLogs_whenSearchQueryForCategoryIsPassed()
    {
        $categoryOne = LogCategory::create(['name' => 'test_category_1']);
        $categoryTwo = LogCategory::create(['name' => 'test_category_2']);
        Logger::exception(new Exception('exception_one'), $categoryOne->name);
        Logger::exception(new Exception('exception_two'), $categoryTwo->name);
        $response = $this->call('GET', 'api/admin/logs/exception', ['search_query' => 'test_category_1']);
        $response->assertStatus(200);
        $exceptionLog = json_decode($response->getContent())->data->data;
        $this->assertCount(1, $exceptionLog);
        $this->assertEquals('exception_one', $exceptionLog[0]->message);
    }
}
