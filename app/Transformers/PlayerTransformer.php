<?php

namespace App\Transformers;

use App\Player;

class PlayerTransformer extends \League\Fractal\TransformerAbstract
{

    /**
     * Transforms a Player into an array as below.
     *
     * @param  App\Player $player
     * @return array
     */
    public function transform(Player $player)
    {
        return [
            'id' => $player->id,
            'name' => $player->name,
            'temp' => $player->temp,
            'rounds' => count($player->rounds)
        ];
    }

}
