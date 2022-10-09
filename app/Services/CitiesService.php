<?php

namespace App\Services;

use App\Models\City;
use App\Models\User;
use App\Services\UsersService;

use Illuminate\Support\Collection;


class CitiesService {

    public function __construct(private UsersService $userService){}

    /**
     * Get cities recommendation for user based on rate similarities
     * from other users
     *
     * @param User $user
     * @return Collection
     */
    public function getRecommendations(User $user): Collection{
        $visits = $user->visits;
        $users = $this->userService->getUsersWithSimilarRates($user);
        
        $users->loadMissing(['visits' => function($query) use ($visits){
            $query->whereNotIn('cities.id',$visits->pluck('id'));
        }]);

        $cities = $users->pluck('visits')->flatten()->groupBy('id')->map(function($userVisits){
            $visit = $userVisits->first();
            $visit->avgRate = $userVisits->avg('pivot.rate');
            return $visit->only(['name','avgRate','id']);
        })->where('avgRate', '>=' , !$visits->isEmpty() ? 80 : 60);
        
        return collect($cities->values());
    }
}