<?php

namespace Fooino\Core\Commands;

use Fooino\Core\Models\Language;
use Fooino\Core\Tasks\Seeder\LoadSeederConfigTask;
use Fooino\Core\Tasks\Language\RecacheActiveLanguagesTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Fooino\Core\Tasks\Tools\PrettifyInputTask;
use Exception;

class SyncLanguagesCommand extends Command
{

    protected $signature = 'sync:languages';

    protected $description = 'Sync Languages base on fooino core languages';

    public function handle()
    {
        try {

            DB::beginTransaction();

            activity()->disableLogging();

            app(LoadSeederConfigTask::class)->run(path: base_path('vendor/fooino/core/config/fooino-core-languages.php'));

            $recache = false;
            $insert = [];
            $languages = app(PrettifyInputTask::class)->run('languages', config('fooino-core-languages', []));
            $dbLanguages = Language::pluck('id', 'code')->toArray(); // the array will be ['code' => 'id']


            foreach ($languages as $key => $lang) {

                $id = $dbLanguages[strtolower($lang['code'])] ?? null;

                if (
                    filled($id) // the language already inserted
                ) {
                    continue;
                }

                $insert[]   = [
                    'code'          => strtolower($lang['code']),
                    'name'          => $lang['name'],
                    'priority'      => $lang['priority'] ?? ($key * FOOINO_PRIORITY_STEP),
                    'direction'     => $lang['direction'],
                    'status'        => $lang['status'],
                    'state'         => $lang['state'],
                    'timezones'     => filled($lang['timezones'] ?? null) ? jsonEncode($lang['timezones']) : null,
                    'created_at'    => currentDate(),
                    'updated_at'    => currentDate(),
                ];
            }

            if (
                filled($insert)
            ) {
                $recache = true;
                Language::insert($insert);
            }

            if (
                $recache
            ) {
                app(RecacheActiveLanguagesTask::class)->run();
            }


            DB::commit();

            $this->info('Languages seeded successfully');

            // 
        } catch (Exception $e) {

            DB::rollBack();

            logger()->error('CAN_NOT_SYNC_LANGUAGES_EXCEPTION:' . $e);


            $this->error('There was an error in syncing languages');
        }
    }
}
