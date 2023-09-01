<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Http\Controllers\SkillController;
use App\Models\Pokemon;

final readonly class SkillLearn
{
    private SkillController $skillController;

    public function __construct(SkillController $skillController)
    {
        $this->skillController = $skillController;
    }
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        //取得寶可夢
        $id=$args["input"]["id"];
        $pokemon = Pokemon::find($id);
        //取得可學習技能
        $stringId=(string)$id;
        $enableSkill = $this->skillController->index($stringId);
        //更新技能
        if (isset($args["input"]["skill1"]) && !empty($args["input"]["skill1"]) && in_array($args["input"]["skill1"], $enableSkill)) {
            $skill1 = $args["input"]["skill1"];
            $pokemon->skill1 = $skill1;
        }
        if (isset($args["input"]["skill2"]) && !empty($args["input"]["skill2"]) && in_array($args["input"]["skill2"], $enableSkill)) {
            $skill2 = $args["input"]["skill2"];
            $pokemon->skill2 = $skill2;
        }
        if (isset($args["input"]["skill3"]) && !empty($args["input"]["skill3"]) && in_array($args["input"]["skill3"], $enableSkill)) {
            $skill3 = $args["input"]["skill3"];
            $pokemon->skill3 = $skill3;
        }
        if (isset($args["input"]["skill4"]) && !empty($args["input"]["skill4"]) && in_array($args["input"]["skill4"], $enableSkill)) {
            $skill4 = $args["input"]["skill4"];
            $pokemon->skill4 = $skill4;
        }
        //確認技能是否相同
        $array = [];
        if ($pokemon->skill1 != null) {
            $array[] = $pokemon->skill1;
        }
        if ($pokemon->skill2 != null) {
            $array[] = $pokemon->skill2;
        }
        if ($pokemon->skill3 != null) {
            $array[] = $pokemon->skill3;
        }
        if ($pokemon->skill4 != null) {
            $array[] = $pokemon->skill4;
        }
        $uniqueArray = array_unique($array);
        if (count($array) != count($uniqueArray)) {
            return null;
        }
        //回傳值
        $pokemon->save();
        return $pokemon;
    }
}
