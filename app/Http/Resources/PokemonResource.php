<?php

namespace App\Http\Resources;

use App\Models\Ability;
use App\Models\Nature;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PokemonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "level" => $this->level,
            "race" => $this->race,
            "nature" => $this->nature->name,
            "ability" => $this->ability->name,
            "skill1" => $this->skill1,
            "skill2" => $this->skill2,
            "skill3" => $this->skill3,
            "skill4" => $this->skill4,
            "created_at"=>$this->created_at->toDateTimeString(),
            "updated_at"=>$this->updated_at->toDateTimeString(),
        ];
    }
}
