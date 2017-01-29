<?php

use Illuminate\Database\Seeder;

class PlayersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $players = factory(App\Player::class, 100)->create();
        $team = App\Team::find(1);

        $players->each(function ($player) use ($team) {
            $team->players()->attach($player);
        });

    }
}
