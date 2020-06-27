<?php

namespace AhmedAliraqi\LaravelMediaUploader\Console;

use AhmedAliraqi\LaravelMediaUploader\Entities\TemporaryFile;
use Illuminate\Console\Command;

class TemporaryClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'temporary:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired uploaded temporary files.';

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
        TemporaryFile::whereDate('created_at', '<=', today()->subHours(6))
            ->each(function (TemporaryFile $file) {
                $file->delete();
            });

        $this->info(
            "\nThe temporary files has been cleaned successfully. "
            .now()->toDateTimeString()
        );
    }
}
