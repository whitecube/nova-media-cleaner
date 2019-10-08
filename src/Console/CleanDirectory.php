<?php

namespace Whitecube\NovaMediaCleaner\Console;

use Illuminate\Console\Command;
use Whitecube\NovaMediaCleaner\Cleaner;

class CleanDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:directory {directory? : The directory to clean} {recursive? : default true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launch nova uploads cleanup for given directory';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $directory = $this->getDirectoryArgument();

        $cleaner = Cleaner::directory($directory, $this->argument('recursive') ?? true);

        $cleaner->inAvailableResources(request());

        dd($cleaner->analyze(request()));
    }

    /**
     * Get the directory
     *
     * @return string
     */
    public function getDirectoryArgument()
    {
        if(!$this->argument('directory')) {
            return storage_path('public');
        }

        return storage_path($this->argument('directory'));
    }
}
