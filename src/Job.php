<?php

namespace App;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Bootstrap.php';

use App\Models\Job as ModelsJob;

class Job
{
    public static function start()
    {
        echo "Listening Job..." . PHP_EOL;
        while (true) {
            $job = ModelsJob::where(function ($q) {
                $q->whereNull('success_at')->whereNull('failed_at');
            })->orWhere(function ($q) {
                $q->whereNull('success_at');
            })->orderBy('id')->limit(1)->first();

            if ($job) {
                echo "Processing Job: {$job->name}" . PHP_EOL;
                $exec = $job->name::handle(json_decode($job->payload, true));
                if ($exec) {
                    ModelsJob::find($job->id)->update(['success_at' => now()]);
                } else {
                    ModelsJob::find($job->id)->update(['failed_at' => now(), 'attempts' => $job->attempts + 1]);

                    echo "Failed Job: {$job->name}" . PHP_EOL;
                }
                echo "Processed Job: {$job->name}" . PHP_EOL;
                echo "Listening Job..." . PHP_EOL;
            }
            sleep(5);
        }
    }
}
