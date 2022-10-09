<?php

namespace App\Services;

use App\Models\City;
use App\Models\User;

use Illuminate\Support\Collection;
use \DB;

class UsersService {

    public function getUsersWithSimilarRates(User $user, int $acceptance = 20): Collection {
        $visits = $user->visits;
        $users = User::where('id', '<>', $user->id)
                    ->when(!$visits->isEmpty(), function($query) use ($acceptance, $visits){
                        $query->whereHas('visitRate', function($query) use ($acceptance, $visits){
                            $query->where(function($query) use ($acceptance, $visits){
                                $visits->each(function($city) use ($acceptance, &$query){
                                    $rate = $city->pivot->rate;
                                    $minRate = $rate * ((100 - $acceptance) / 100);
                                    $maxRate = $rate * ((100 + $acceptance) / 100);
                                    $query->orWhere(function($query) use ($city, $minRate, $maxRate){
                                        $query->where('cityId', $city->id)->whereBetween('rate', [$minRate, $maxRate]);
                                    });
                                });
                        });
                    });
                })->get();
        return $users;
    }

    public function getFriendsOfFriends(User $user): Collection {
        $friendsOfFriends = collect();
        if(!$user->connections->isEmpty()){
            $friendsOfFriends = User::with('gender:id,name')->withWhereHas('connections', function($query) use ($user){
                $query->whereIn('connectionUserId', $user->connections->pluck('id'));
            })->whereDoesntHave('connections', function($query) use ($user){
                $query->where('connectionUserId', $user->id);
            })->where('id','<>', $user->id)->get();
        } 
        return $friendsOfFriends;
    }


    public function getFriendSuggestions(User $user): Collection {
        $suggestions = collect();
        
        if($user->connections->count() >= 2){           
            $suggestions = collect($this->getFriendsOfFriends($user)->filter(function($suggestion){
                return $suggestion->connections->count() >= 2; 
            })->values());
        } 

        return $suggestions;
    }
}