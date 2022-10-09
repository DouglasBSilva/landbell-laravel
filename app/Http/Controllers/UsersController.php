<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UsersService;
use App\Services\CitiesService;

class UsersController extends Controller
{

    public function __construct(
        private UsersService $usersService,
        private CitiesService $citiesService
    ){}

    /**
     * Display a list of Users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return User::with('gender')->paginate($request->get('per_page',10));
    }

    /**
     * Display the specified User.
     *
     * @param  int $user
     * @return \Illuminate\Http\Response
     */
    public function show(int $user)
    {   
        $user = User::with(['gender', 'visits'])->findOrFail($user);
        return response()->json($user);
    }

    /**
     * Get those people who are directly connected to the chosen user
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function friends(User $user)
    {
        return response()->json($user->connections);
    }

    /**
     * Get people in the group who know 2 or more direct friends of the 
     * chosen user but are not directly connected to the chosen user
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function suggestions(User $user)
    {
        return response()->json($this->usersService->getFriendSuggestions($user));
    }

    /**
     * Get those who are two steps away from the chosen 
     * user but not directly connected to the chosen user
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function friendsOfFriends(User $user)
    {
        return response()->json($this->usersService->getFriendsOfFriends($user));
    }

    /**
     * Get the city recommendations to the chosen user.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function cityRecommendations(User $user)
    {
        $cities = $this->citiesService->getRecommendations($user);
        return response()->json($cities);
    }

}
