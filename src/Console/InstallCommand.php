<?php

namespace AhmedAliraqi\LaravelMediaUploader\Console;

use Illuminate\Console\Command;
use AhmedAliraqi\LaravelMediaUploader\Entities\TemporaryFile;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'uploader:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the uploader icons.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'uploader:icons',
        ]);
    }
}
