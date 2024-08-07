<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function deploy(Request $request)
    {
        $githubPayload = $request->getContent();
        $githubHash = $request->header('X-Hub-Signature');
        $localToken = env('GITHUB_WEBHOOK_SECRET');
        $localHash = 'sha1=' . hash_hmac('sha1', $githubPayload, $localToken, false);

        if (hash_equals($githubHash, $localHash)) {

            $branch = env('GITHUB_WEBHOOK_BRANCH');
            $root_path = base_path();
            $process = shell_exec("cd {$root_path} && git checkout {$branch} && git pull");
            Log::info($process);
        }
        else
        {
            Log::error('Kunci deploy tidak sama dengan yang ada di github');
        }
    }
}
