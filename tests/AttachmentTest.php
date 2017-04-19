<?php

use CCUPlus\Attachment\Attachment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttachmentTest extends Orchestra\Testbench\TestCase
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
            CCUPlus\Attachment\AttachmentServiceProvider::class,
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
        $this->app['router']->get('/', function () {
            return Attachment::first()->download();
        });

        Storage::fake('attachments');

        $this->user->addFile(UploadedFile::fake()->image('maple.jpg'))->save();

        $this->assertCount(1, Attachment::all());

        $this->get('/')->assertStatus(200);

        $this->assertSame(1, Attachment::first()->getAttribute('downloads'));
    }
}
