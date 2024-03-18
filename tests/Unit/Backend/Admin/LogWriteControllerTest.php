<?php

namespace Tests\Unit\Backend\Admin;

use App\Exceptions\NonLoggableException;
use App\Models\LogCategory;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Logger;

class LogWriteControllerTest extends TestCase
{
    use DatabaseTransactions;
    public function test_exceptionLog_whenPassedExceptionIsAnInstanceNonLoggableException_shouldNotLogThatException()
    {
        $exception = new NonLoggableException('test_exception');
        $category = LogCategory::create(['name' => 'test_category']);
        Logger::exception($exception, $category->name);
        $this->assertNull($category->exception()->first());
    }
    public function test_exceptionLog_whenPassedExceptionIsNotAnInstanceNonLoggableException_shouldLogThatException()
    {
        $exception = new Exception('test_exception');
        $category = LogCategory::create(['name' => 'test_category']);
        Logger::exception($exception, $category->name);
        $exceptionLog = $category->exception()->first();
        $this->assertEquals('test_exception', $exceptionLog->message);
    }
}
