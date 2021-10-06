<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class Players extends Model
{
    public function getPlayers()
    {
        $players = DB::table('Players')->get();

        return $players;
    }

    public function newPlayer($request)
    {
        DB::table('players')->insert([
            'name' => $request['txtName'],
            'level' =>$request['txtLevel'],
            'presence' =>0,
            'goalkeeper' =>$request['txtGoal'],
        ]);
    }

    public function editPlayer($request)
    {
        DB::table('players')->where('idPlayer', $request['idPlayer'])->update(
            [
                'name' => $request['txtName'],
                'level' => $request['txtLevel'],
                'goalkeeper' => $request['txtGoal'],
            ]
        );
    }

    public function getPlayerId($idPlayer)
    {
        return DB::table('players')
            ->select('idPlayer', 'name', 'level', 'presence', 'goalkeeper')->where('idPlayer',"=",$idPlayer)
            ->get();
    }

    public function confirmPresence($idPlayer,$presence)
    {
         DB::table('players')
              ->where('idPlayer', $idPlayer)
              ->update(['presence' => $presence]);
    }

    public function randomDraw()
    {
        return DB::table('players')
            ->select('idPlayer', 'name', 'level', 'presence', 'goalkeeper')->where('presence',"=",1)
            ->get();
    }
}
