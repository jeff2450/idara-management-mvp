<?php

namespace Database\Seeders;

use App\Models\LetterTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Template moja ya mfano kuonyesha muundo wa placeholders - angalia
 * architecture.md §2.5.
 */
class LetterTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@idara.test')->first();

        if (! $admin) {
            return;
        }

        LetterTemplate::firstOrCreate(
            ['name' => 'Barua ya Utambulisho'],
            [
                'body_template' => "Tarehe: {{ tarehe }}\n\n"
                    ."Ndugu {{ jina_mwanachama }},\n\n"
                    ."Kwa barua hii, tunathibitisha kwamba wewe ni mwanachama wa {{ idara }} "
                    ."ndani ya kanisa letu.\n\n"
                    ."Ahsante kwa huduma yako.\n\n"
                    .'Kiongozi wa Idara',
                'created_by' => $admin->id,
            ]
        );
    }
}
