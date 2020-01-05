<?php

namespace CCUPLUS\Attachment\Tests;

use CCUPLUS\Attachment\AttachmentServiceProvider;
use CCUPLUS\Attachment\Tests\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;

class AttachmentTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Fake laravel model.
     *
     * @var User
     */
    protected $user;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);

        $this->user = User::create(['name' => 'test', 'email' => 'test@example.com', 'password' => bcrypt('test')]);
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            AttachmentServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('attachments.disk', 'attachments');

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    public function test_attachments()
    {
        Storage::fake('attachments');

        $attachment = $this->user->addFile(UploadedFile::fake()->image('maple.jpg'))->save();

        $this->assertTrue($attachment->exists);

        $content = '';

        ob_start(function ($buffer) use (&$content) {
            return $content = $buffer;
        });

        echo 'buffering';

        $attachment->download()->sendContent();

        ob_end_clean();

        $this->assertSame($attachment->getAttribute('size'), strlen($content));

        $this->assertSame(1, $attachment->getAttribute('downloads'));
    }
}
