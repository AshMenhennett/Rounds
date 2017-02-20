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
<<<<<<< HEAD
            'recent' => $player->recent,
=======
>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
            'rounds' => count($player->rounds)
        ];
    }

}
