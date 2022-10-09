<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\City;
use App\Models\CityVisit;
use App\Models\Gender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class StartDataBase extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $collectionData = collect(
            json_decode(Storage::get('database/databaseContent.json'), true)
        );
        $collectionData = $collectionData->keyBy('id');
        $possibleGendersOnData = $collectionData->pluck('gender')->flip();
        $possibleGendersOnData = $possibleGendersOnData->mapWithKeys(function($value, $gender){
            $genderInstance = Gender::firstOrCreate([
                "name" => $gender
            ]);

            return [$gender => $genderInstance->id];
        });

        $possibleCitiesOnData = collect();
        $collectionData->pluck('cities')->each(
            function($cities) use (&$possibleCitiesOnData){
                $possibleCitiesOnData = $possibleCitiesOnData->merge($cities);
            }
        );
        
        $possibleCitiesOnData = $possibleCitiesOnData->mapWithKeys(function($value, $city){
            $cityInstance = City::firstOrCreate([
                "name" => $city
            ]);

            return [$city => $cityInstance->id];
        });

        $registers = $collectionData->map(function($data) use ($possibleGendersOnData, $possibleCitiesOnData){
            $user = User::firstOrCreate([
                'id' => $data['id']
                ],
                [
                    'firstName' => $data['firstName'],
                    'surname' => $data['surname'],
                    'age' => $data['age'] ?? 0,
                    'genderId' => $possibleGendersOnData->get($data['gender'])
                ]
            );

            $citiesVisit = array_map(function($city, $cityRate) use ($possibleCitiesOnData, $user) {
                return [
                    'rate' => $cityRate,
                    'userId' => $user->id,
                    'cityId' => $possibleCitiesOnData->get($city),
                ];
            }, array_keys($data['cities']), $data['cities']);

            $cityVisity = CityVisit::upsert(
                $citiesVisit, ['userId','cityId'], []
            );

            return [
                'user' => $user, 'connections' => $data['connections']
            ];
        });

        $registers->each(function($register){
            $register['user']->connections()->attach($register['connections']);
        });

        
    }
}
