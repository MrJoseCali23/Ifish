<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Criadero;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CriaderoSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('criaderos')->truncate();
        Schema::enableForeignKeyConstraints();

        $dueño = User::where('email', 'carlos.r@ifish.app')->first();
        if ($dueño) {
            $criadero = Criadero::create([
                'nombre' => 'Criadero Los Pacuses',
                'user_id' => $dueño->id,
                'ubicacion' => 'Valle Alto, Cochabamba',
            ]);
            $dueño->criadero_id = $criadero->id;
            $dueño->save();
        }
    }
}