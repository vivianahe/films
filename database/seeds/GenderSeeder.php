<?php

use App\Gender;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genders = [
            [ "name" => 'Acción', "state" => 'active'],
            [ "name" => 'Aventuras', "state" => 'active'],
            [ "name" => 'Ciencia Ficción', "state" => 'active'],
            [ "name" => 'Comedia', "state" => 'active'],
            [ "name" => 'No- Ficción / documental', "state" => 'active'],
            [ "name" => 'Drama', "state" => 'active'],
            [ "name" => 'Fantasía', "state" => 'active'],
            [ "name" => 'Musical', "state" => 'active'],
        ];
        foreach ($genders as $gender){
            Gender::updateOrCreate([
                'name' => $gender['name'],
                'state' => $gender['state'],
            ]);
        }

    }
}
