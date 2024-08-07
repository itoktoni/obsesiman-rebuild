<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Deploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deploy';

    /**
     * The console deploy with github.
     *
     * @var string
     */
    protected $description = 'deploy with github';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            $branch = env('GITHUB_WEBHOOK_BRANCH');
            $root_path = base_path();
            $process = shell_exec("cd {$root_path} && git checkout {$branch} && git pull");
            $this->info($process);

            Log::info('Deploy Success');

        } catch (\Throwable $th) {

            Log::error($th->getMessage());
        }
    }
}
