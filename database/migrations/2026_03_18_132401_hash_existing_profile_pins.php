<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cette migration va parcourir tous les profils existants et hasher les PINs qui ne sont pas encore hashés.
      * On vérifie la longueur du PIN pour éviter de re-hasher un PIN déjà hashé (les hashes sont généralement plus longs que les PINs d'origine).
      * ATTENTION : Assurez-vous d'avoir une sauvegarde de votre base de données avant d'exécuter cette migration, car elle modifie les données existantes.
      */
    public function up(): void
    {
        $profiles = \App\Models\Profile::whereNotNull('pin')->get();

        foreach ($profiles as $profile) {
            // Vérifie si le PIN n'est pas déjà hashé
            if (strlen($profile->getRawOriginal('pin')) < 20) {
                \Illuminate\Support\Facades\DB::table('profiles')
                    ->where('id', $profile->id)
                    ->update([
                        'pin' => \Illuminate\Support\Facades\Hash::make(
                            $profile->getRawOriginal('pin')
                        )
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
