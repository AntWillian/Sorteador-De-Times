<?php

namespace App\Http\Controllers;

use App\Players;
use Exception;
use Illuminate\Http\Request;

class PlayersController extends Controller
{
    public function show()
    {
        $players = new Players();

        return $players->getPlayers();
    }

    public function newPlayer(Request $request)
    {
        try {

            $players = new Players();

            $response = $players->newPlayer($request->toArray());
            return response()->json(['error' => false, 'situacao' => 'Jogador cadastrado'], 200);

        } catch (Exception $e) {
            return response()->json(['error' => true, 'situacao' => 'Jogador não cadastrado'], 500);
        }
    }

    public function editPlayer(Request $request)
    {
        try {

            $players = new Players();

            $response = $players->editPlayer($request->toArray());
            return response()->json(['error' => false, 'situacao' => 'Jogador editado'], 200);

        } catch (Exception $e) {
            return response()->json(['error' => true, 'situacao' => 'Jogador não editado'], 500);
        }
    }

    public function getPlayerId($idplayer)
    {
        $players = new Players();

        return $players->getPlayerId($idplayer);
    }

    public function confirmPresence($idPlayer, $presence)
    {
        try {

            $players = new Players();

            $response = $players->confirmPresence($idPlayer, $presence);
            return response()->json(['error' => false, 'situacao' => 'presença atualizada'], 200);

        } catch (Exception $e) {
            return response()->json(['error' => true, 'situacao' => 'presença não atualizada'], 500);
        }
    }

    public function randomDraw($playerTeam)
    {
        //try {

            $players = new Players();

            $response = $players->randomDraw();

            $teams = [];
            $j = 1;
            $k = 0;
            $f = 0;

            $arrayControl = [];
            $goalKeeper = [];
            $goalKeeperLevel = [];
            $goalKeeperHigherLevel = [];
            $saveLevelGoal = [];
            $g = 0;
            if ($playerTeam < 3) {
                return response()->json(['error' => true, 'situacao' => 'Escolher no minimo 3 jogadores por time'], 500);
            }

            if (($playerTeam * 2) <= count($response)) {

                ########################################################################################
                // separando os indices dos jogadores
                for ($i = 0; $i < count($response); $i++) {
                    $arrayControl[$i] = $i;
                    // sera utilizado quando for realizar a disparidade de times
                    $response[$i]->change = false;
                }

                ########################################################################################

                //verifica quantos goleiros existe
                $goalTotal = 0;
                for ($i = 0; $i < count($response); $i++) {
                    if ($response[$i]->goalkeeper == 1) {
                        $goalTotal += 1;
                    }
                }

                $foundGoal = false;
                if ($goalTotal < 2) {

                    //caso exista apenas um goleiro, sera escolhido um jogador aleaorios para se tornar goleiro
                    while (!$foundGoal) {
                        $rand_keys = rand(1, count($response));

                        if ($response[$rand_keys]->goalkeeper != 1) {
                            $response[$rand_keys]->goalkeeper = 1;
                            $foundGoal = true;
                        }
                    }

                }

                // separar os goleiros antes de separar os times
                for ($i = 0; $i < count($response); $i++) {
                    if ($response[$i]->goalkeeper == 1) {
                        $goalKeeper[$g] = $response[$i];
                        $goalKeeper[$g]->position = $i;

                        // sera utilizado quando for realizar a disparidade de times (os goleiros nao serao trocados)
                        $goalKeeper[$g]->change = true;

                        // esse array servira de base para pegar os goleiros de maior nivel
                        $goalKeeperLevel[$g] = $response[$i]->level;
                        $g += 1;
                    }
                }

                ########################################################################################
                //pegar os 2 goleiros com maior level
                $loop = 0;
                $z = 0;

                while ($loop < 2) {

                    $goalKeeperLevel = array_values($goalKeeperLevel);

                    $higherLevel = max($goalKeeperLevel);

                    $found = false;

                    while (!$found) {
                        for ($q = 0; $q < count($goalKeeperLevel); $q++) {
                            if ($goalKeeperLevel[$q] == $higherLevel) {
                                //tirar o maior level da lista (assim na proxima vez pegar o segundo maior level da lista)
                                unset($goalKeeperLevel[$q]);

                                for ($i = 0; $i < count($goalKeeper); $i++) {
                                    // verificar qual goleiro tem esse level alto na lista de goleiros
                                    if ($goalKeeper[$i]->level == $higherLevel) {
                                        // salvar goleiros de maior nivel separados para ser adicionados posteriormente nos times
                                        $goalKeeperHigherLevel[$z] = $goalKeeper[$i];
                                        // salva apenas o nivel do goleiro (sera usado para escalar o goleiro para os times)
                                        $saveLevelGoal[$z] = $higherLevel;
                                        // tirar jogador da lista de sorteio
                                        unset($arrayControl[$goalKeeper[$i]->position]);
                                    }
                                }
                                $found = true;
                            }
                        }

                        $z += 1;
                    }

                    $loop += 1;
                }

                ########################################################################################
                $t = count($arrayControl);

                // tirar do total de jogadores por times os goleiros já escolhidos
                $playerTeam -= 1;

                // Sorteando os jogadores para cada time
                for ($i = 0; $i < $t; $i++) {

                    $rand_keys = array_rand($arrayControl, 1);
                    unset($arrayControl[$rand_keys]);

                    $teams['team' . $j][$k] = $response[$rand_keys];
                    $k += 1;
                    $f += 1;

                    if ($f == $playerTeam) {
                        $j += 1;
                        $k = 0;
                        $f = 0;
                    }
                }

                ########################################################################################
                // Aplicando balancemaneto de nivel nos dois times principais (nao sera levado em consideracao os jogadores sobressalentes)
                // e escalando os goleiros para os dois times

                // somando os levels dos times (ainda sem goleiros)
                $teamsBalancing = $this->sumlevel($teams, $j);

                // separar apenas os niveis dos jogadores de cada time antes de escalar os goleiros (eles nao serao trocados nos times)
                $teamsLevels = $this->separateLevels($teams);

                //adicionando goleiros de acordo com o nivel dos times (o time de menor level recebe o goleiro de maior level)

                // ajuste de indices do array
                $saveLevelGoal = array_values($saveLevelGoal);

                $big = "";
                $small = "";

                if ($teamsBalancing['team1'] > $teamsBalancing['team2']) {
                    $big = "team1";
                    $small = "team2";
                } else {
                    $big = "team2";
                    $small = "team1";
                }

                $higherLevel = max($saveLevelGoal);

                $goalFound = false;
                $m = 0;

                while (!$goalFound) {
                    // verificar qual goleiro tem o level alto na lista de goleiros
                    if ($goalKeeperHigherLevel[$m]->level == $higherLevel) {
                        // escalando goleiro de maior nivel no time de menor nivel
                        $teams[$small][4] = $goalKeeperHigherLevel[$m];

                        // essa verificao so é possivel pois sempre tera apenas dois goleiros seleconados
                        if ($m > 0) {
                            $teams[$big][4] = $goalKeeperHigherLevel[0];
                        } else {
                            $teams[$big][4] = $goalKeeperHigherLevel[1];
                        }

                        $goalFound = true;
                    }

                    $m += 1;
                }

                //verificando disparidade dos time (sera usado o teor de disparidade  = 3)

                // somando os levels dos times (com goleiros)
                $teamsBalancing = $this->sumlevel($teams, $j);

                $disparity = $this->checkDisparity($teamsBalancing['team1'], $teamsBalancing['team2']);

                while ($disparity) {

                    // Aqui começa a troca de jogadores entre os dois time, sempre o maior level sera trocado pelo de menor level mas, depois de uma troca
                    // o jogador nao sera mais trocado do time
                    if ($teamsBalancing['team1'] > $teamsBalancing['team2']) {
                        $big = "team1";
                        $small = "team2";
                    } else {
                        $big = "team2";
                        $small = "team1";
                    }

                    // sempre tira o jogador de maior level do time que tem a maior soma de level
                    //depois realiza uma nova verificacao de disparidade

                    if (count($teamsLevels[$big]) == 0) {
                        // quando atinge a quantidade maxima de jogadores e nao conseguir balancear deixa passar (pensar melhor em como ajustar)
                        $disparity = false;
                    } else {
                        $higherLevelBig = max($teamsLevels[$big]);
                        $higherLevelSmall = min($teamsLevels[$small]);

                        $tempPlayer = [];
                        $loopB = false;
                        $loopS = false;

                        $teams[$big] = array_values($teams[$big]);
                        //pegando jogador de maior nivel do time que possui a maior soma de niveis
                        for ($i = 0; $i < count($teams[$big]); $i++) {
                            if ($teams[$big][$i]->level == $higherLevelBig & !$teams[$big][$i]->change & !$loopB) {
                                $teams[$big][$i]->change = true;
                                $tempPlayer = $teams[$big][$i];
                                $loopB = true;
                                //eliminando level do array de levels
                                $key = array_search($higherLevelBig, $teamsLevels[$big]);
                                unset($teamsLevels[$big][$key]);

                                $teams[$small] =array_values($teams[$small]);
                                //pegando jogador de maior nivel do time que possui a menor soma de niveis
                                for ($a = 0; $a < count($teams[$small]); $a++) {

                                    if ($teams[$small][$a]->level == $higherLevelSmall & !$teams[$small][$a]->change & !$loopS) {
                                        $teams[$small][$a]->change = true;
                                        // realizando a troca de jogadores
                                        $teams[$big][$i] = $teams[$small][$a];
                                        $teams[$small][$a] = $tempPlayer;
                                        $loopS = true;

                                        //eliminando level do array de levels
                                        $key = array_search($higherLevelSmall, $teamsLevels[$small]);
                                        unset($teamsLevels[$small][$key]);
                                    }
                                }
                            }
                        }

                        // soma de novo os niveis dos times
                        $teamsBalancing = $this->sumlevel($teams, $j);

                        // verifica se os times ainda estao com disparidade
                        $disparity = $this->checkDisparity($teamsBalancing['team1'], $teamsBalancing['team2']);

                    }
                }

                $teams['team1'] = array_values($teams['team1']);
                $teams['team2'] = array_values($teams['team2']);

                return response()->json(['error' => false, 'situacao' => '', 'teams' => count($teams), 'dados' => $teams], 200);

            } else {
                return response()->json(['error' => false, 'situacao' => 'Nao é possivel forma dois times com a quantidade de jogadores informado', 'dados' => ''], 200);
            }

        // } catch (Exception $e) {
        //     return response()->json(['error' => true, 'situacao' => 'erro ao tentar montar os times'], 500);
        // }
    }

    public function sumlevel($teams, $teamFormat)
    {
        $teamsBalancing = [];
        $loopTeam = 1;


        // somando os levels dos jogadores
        while ($loopTeam <= $teamFormat) {
            $sumLevel = 0;

            $teams['team' . $loopTeam] = array_values($teams['team' . $loopTeam]);

            for ($i = 0; $i < count($teams['team' . $loopTeam]); $i++) {
                $sumLevel += $teams['team' . $loopTeam][$i]->level;
            }

            $teamsBalancing['team' . $loopTeam] = $sumLevel;
            $loopTeam += 1;

        }

        return $teamsBalancing;
    }

    public function separateLevels($teams)
    {
        $teamslevels = [];
        $loopTeam = 1;

        // somando os levels dos jogadores
        while ($loopTeam < 3) {
            $m = 0;
            for ($i = 0; $i < count($teams['team' . $loopTeam]); $i++) {
                $teamslevels['team' . $loopTeam][$m] = $teams['team' . $loopTeam][$i]->level;
                $m += 1;
            }

            $loopTeam += 1;

        }

        return $teamslevels;
    }

    public function checkDisparity($team1, $team2)
    {
        $big = 0;
        $small = 0;

        if ($team1 > $team2) {
            $big = $team1;
            $small = $team2;
        } else {
            $big = $team2;
            $small = $team1;
        }

        if (($big - $small) >= 3) {
            return true;
        } else {
            return false;
        }
    }
}
