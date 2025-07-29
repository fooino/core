<?php

namespace fooino\Core\Commands;

use fooino\Core\Models\Language;
use fooino\Core\Tasks\Seeder\LoadSeederConfigTask;
use fooino\Core\Tasks\Language\RecacheActiveLanguagesTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class SyncLanguagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:languages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Languages base on fooino core languages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            DB::beginTransaction();

            app(LoadSeederConfigTask::class)->run('fooino-core-languages');

            $recache = false;
            $insert = [];
            $dbLanguages = Language::pluck('id', 'code')->toArray(); // the array will be ['code' => 'id']


            foreach (config('fooino-core-languages', []) as $lang) {

                $id = $dbLanguages[strtolower($lang['code'])] ?? null;

                if (filled($id)) {
                    continue;
                }

                $insert[]   = [
                    'code'          => strtolower($lang['code']),
                    'name'          => $lang['name'],
                    'direction'     => $lang['direction'],
                    'status'        => $lang['status'],
                    'state'         => $lang['state'],
                    'timezones'     => filled($lang['timezones'] ?? null) ? jsonEncode($lang['timezones']) : null,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];
            }

            if (
                filled($insert)
            ) {
                $recache = true;
                Language::insert($insert);
            }

            if ($recache) {
                app(RecacheActiveLanguagesTask::class)->run();
            }


            DB::commit();

            $this->info('Languages seeded successfully');

            // 
        } catch (Exception $e) {

            DB::rollBack();

            logger()->error('CAN_NOT_SYNC_LANGUAGES_EXCEPTION:' . $e);


            $this->error('There was an error syncing languages');
        }
    }
}
