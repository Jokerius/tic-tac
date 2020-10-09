<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;
    
    public function __construct(){
        $this->guid = Str::uuid();
        $this->board = '---------';
        $this->status = 'RUNNING';
    }
    
    /*
     * Checks if the move received from player is valid. 
     * Only placing X on the empty field ("-") once is considered a valid move
     */    
    public function checkValidMove($move){
        if($this->board === $move || strlen($move) != 9){
            return false;
        }
        for($i=0;$i<9;$i++){
            if($this->board[$i] != $move[$i]){
                if(!empty($moveMade)){
                    return false;
                }
                if($move[$i] == 'X' && $this->board[$i] == '-'){
                    $moveMade = true;
                }else{
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /*
     * Check if the game is ended with win or draw
     */    
    public function checkEndGame(){
        
        // all 3 in a row combinations
        $winningCombinations = [
            [0,1,2],
            [3,4,5],
            [6,7,8],
            [0,3,6],
            [1,4,7],
            [2,5,8],
            [0,4,8],
            [2,4,6],
        ];
        
        foreach($winningCombinations as $item){
            if($this->board[$item[0]] == $this->board[$item[1]] && $this->board[$item[1]] == $this->board[$item[2]] && $this->board[$item[0]] != '-'){
                return $this->board[$item[0]] == 'X' ? 'X_WON' : 'O_WON';
            }    
        }
        
        // draw is when all fields are filled and there is no winner
        if(strlen(str_replace('-','',$this->board)) == 9){
            return 'DRAW';
        }
        
        return false;        
    }
    
    /*
     * Placing O in the winning position, otherwise randomly in any free spot
     * @todo optimize algorithm to be more win-dedicated
     */
    public function makeMove(){
        
        $emptyFields = [];
        
        for($i=0;$i<9;$i++){
            if($this->board[$i] == '-'){
                $emptyFields[] = $i;
            }
        }
        
        $board = $this->board;

        foreach($emptyFields as $key){
            $this->board = $board; 
            $this->board = substr_replace($this->board, 'O', $key, 1);
            if($this->checkEndGame() == 'O_WON'){
                return;
            }
        }
        
        $this->board = $board; 
        $this->board = substr_replace($this->board, 'O', $emptyFields[array_rand($emptyFields)], 1);
        
        return;
    }
}
