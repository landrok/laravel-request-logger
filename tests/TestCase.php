<?php

namespace Landrok\Laravel\RequestLoggerTest;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as TestBench;
use Landrok\Laravel\RequestLogger\RequestLoggerServiceProvider;

abstract class TestCase extends TestBench
{
    /** @var \Landrok\Laravel\RequestLoggerTest\User */
    protected $testUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->testUser = User::first();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            RequestLoggerServiceProvider::class,
        ];
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('requestlogger.connection', 'sqlite');
        $app['config']->set('auth.providers.users.model', User::class);
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        // Create users table
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Fill in with a simple user
        User::create([
            'name' => 'TESTUSER',
            'email' => 'test@test.com',
            'password' => 'testpwd',
        ]);

        // Run migrations
        include_once dirname(__DIR__) . '/database/migrations/2020_11_28_010000_create_request_logs_table.php';

        (new \CreateRequestLogsTable())->up();
    }
}
