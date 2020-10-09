<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Game;

class GameController extends Controller
{
    /**
     * Return list of games.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
        
            $games = Game::select('guid', 'board', 'status')->get();

            return new JsonResponse(json_encode($games));

        }catch(\Exception $e){
            return new JsonResponse('Internal Server Error', 500);            
        }           
    }
    
    /**
     * Create a game
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try{
            $game = new Game();
            $game->save();

            return response()->json(['url' => route('Show Game', ['game_id' => $game->guid])], 201)
                    ->withHeaders(['Location' => route('Show Game', ['game_id' => $game->guid])]);

        }catch(\Exception $e){
            return new JsonResponse('Internal Server Error', 500);            
        }           
    }

    /**
     * Shows game
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $game_id)
    {
        try{
            $game = Game::where('guid', $game_id)->first();

            if($game){
                return new JsonResponse(['id' => $game->guid, 'board' => $game->board, 'status' => $game->status]);
            }else{
                return new JsonResponse(['error' => 'Resource not found'], 404);
            }

        }catch(\Exception $e){
            return new JsonResponse('Internal Server Error', 500);            
        }           
    }

    /**
     * Receives player's turn
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $game_id)
    {
        try{
            $move = $request->game['board'];

            if(empty($move)){
                return new JsonResponse(['reason' => 'Move is empty'], 400);
            }

            $game = Game::where('guid', $game_id)->first();        

            if(!$game){
                return new JsonResponse(['error' => 'Resource not found'], 404);
            }

            if($game->status != 'RUNNING'){
                return new JsonResponse(['reason' => 'Game has ended'], 400);            
            }        

            if(!$game->checkValidMove($move)){
                return new JsonResponse(['reason' => 'Invalid move'], 400);            
            }        

            $game->board = $move;

            $status = $game->checkEndGame();
            if($status){
                $game->status = $status;
                $game->save();
            }

            if($game->status == 'RUNNING'){
                $game->makeMove();
            }             

            $status = $game->checkEndGame();
            if($status){
                $game->status = $status;
            }                

            $game->save();

            return new JsonResponse([$game->board, $game->status]);            
        }catch(\Exception $e){
            return new JsonResponse('Internal Server Error', 500);            
        }
    }    
    
    /**
     * Delete game
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $game_id)
    {
        try{
            $game = Game::where('guid', $game_id)->first();

            if($game){
                $game->delete();
                return new JsonResponse(['description' => 'Game successfully deleted']);
            }else{
                return new JsonResponse(['error' => 'Resource not found'], 404);
            }        

        }catch(\Exception $e){
            return new JsonResponse('Internal Server Error', 500);            
        }        
    }        
}
