<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Game;

class GameTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateGame()
    {
        $response = $this->postJson('/api/v1/games');

        $response->assertStatus(201);
        $this->assertTrue(!empty($response['url']));
    }
    
    public function testGetGames()
    {
        $response = $this->getJson('/api/v1/games');

        $response->assertStatus(200);

        $this->assertTrue(!empty($response->getData()));
    }    
    
    public function testShowGame()
    {
        $game = new Game();
        $game->save();
        
        $response = $this->getJson('/api/v1/games/'.$game->guid);

        $response->assertStatus(200);

        $this->assertTrue($response->getData()->id == $game->guid);
    }        
    
    public function testUpdate()
    {
        $game = new Game();
        $game->board = 'XOX-O----';
        $game->save();
        
        $game->board = 'XOX-O-X--';
        
        $response = $this->putJson('/api/v1/games/'.$game->guid, ['game' => $game]);
        $this->assertTrue($response->getData() == ['XOX-O-XO-','O_WON']);
        $response->assertStatus(200);
    }            
    
    public function testDelete()
    {
        $game = new Game();
        $game->save();

        $response = $this->deleteJson('/api/v1/games/XXX');
        $response->assertStatus(404); 
     
        $response = $this->deleteJson('/api/v1/games/'.$game->guid);
        $response->assertStatus(200);           
        $this->assertTrue($response['description'] == 'Game successfully deleted');

    }                
}
