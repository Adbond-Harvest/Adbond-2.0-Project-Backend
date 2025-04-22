<?php

namespace app\Services;

use app\Models\Reaction;

class ReactionService
{
    public $count = false;

    public function save($data, $reaction=null)
    {
        if($reaction) {
            $reaction->reaction = ($reaction->reaction == $data['reaction']) ? null : $data['reaction'];
            $reaction->update();
        }else{  
            $reaction = new Reaction;
            $reaction->user_id = $data['userId'];
            $reaction->user_type = $data['userType'];
            $reaction->entity_id = $data['entityId'];
            $reaction->entity_type = $data['entityType'];
            $reaction->reaction = $data['reaction'];

            $reaction->save();
        }
        
        return $reaction;
    }

    public function userReaction($userId, $userType, $entityId, $entityType)
    {
        return Reaction::where("user_id", $userId)->where("user_type", $userType)
                        ->where("entity_id", $entityId)->where("entity_type", $entityType)
                        ->first();
    }

}