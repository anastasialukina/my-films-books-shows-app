<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Locale;

class UpdateLanguagesFromIsoCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:languages-from-iso-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update languages name from ISO 639-1 code';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->updateLanguages();
    }

    protected function updateLanguages(): void
    {
        $languages = \App\Models\Language::all();
        foreach ($languages as $language) {
            $this->updateLanguageName($language);
        }
    }

    protected function updateLanguageName($language): void
    {
        //find name from ISO 639-1 code
        $language->name = Locale::getDisplayName($language->name, 'en');
        $language->save();
    }
}
