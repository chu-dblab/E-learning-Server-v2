<?php
/**
 * RecommandPoint.php
 */

namespace UElearning\Recommand;

require_once UELEARNING_ROOT.'/config.php';
require_once UELEARNING_LIB_ROOT.'/Target/Target.php';
require_once UELEARNING_LIB_ROOT.'/Database/DBRecommand.php';
require_once UELEARNING_LIB_ROOT.'/Study/Theme.php';
require_once UELEARNING_LIB_ROOT.'/Study/Study.php';
require_once UELEARNING_LIB_ROOT.'/Study/StudyActivity.php';
use UElearning\Target;
use UElearning\Study;
use UElearning\Database;

/**
 * 推薦學習點
 *
 * Usage:
 * $recommand = new RecommandPoint();
 *
 * @version         2.0.0
 * @package         UElearning
 * @subpackage      Study
 */
class RecommandPoint
{
/**
     * 正規化參數
     *
     * @access private
     * @type double
     */
    private $gamma;

    /**
     * 調和參數(常數)
     *
     * @access private
     * @type double
     */
    const ALPHA=0.5;

    private $recommand;
    private $theme;
    private $activity;


    public function __construct()
    {
        $gamma = 0;
        $this->recommand = new Database\DBRecommand();
    }

    /**
     * 計算正規化參數
     * @return double 正規化參數
     */
    private function computeNormalizationParameter($theme_number)
    {
        $normal = 0;  //正規化之後的GAMMA值
        $EntitySum = 0;  //實體學習點分別算權重之後的值
        $VirtualSum = 0;  //虛擬學習點分別算權重之後的值

        $edge = $this->recommand->queryEdgeByID('0');

        for($i=0;$i<count($edge);$i++)
        {
            $next_point = $edge[$i]["next_point"];
            $move_time = $edge[$i]["move_time"];
            $next_target = new Target\Target($next_point);
            $belong = $this->recommand->queryBelongByID($next_point,$theme_number);
            $weight = $belong["weight"];

            $VirtualSum += $weight / $next_target->getLearnTime();

            if($next_target->isNumberOfPeopleZero()) $Rj = 0;
            else $Rj = $next_target->getMj() / $next_target->getPLj();

            $EntitySum += $weight * ($next_target->getS() - $Rj + 1) / ($move_time + $next_target->getLearnTime());
        }
        return $EntitySum/$VirtualSum;
    }


    /**
     * 是否為可以學習的標的
     * @param $target_id 標的編號
     * @return bool enable or not
     */
    private function isEnableTargetId($activityObj,$target_id)
    {
        $target = new Target\Target($target_id);
        if(!$activityObj->isTargetLearned($target_id)) return true;
        else return false;
    }

    /**
     * 過濾非法的標的
     *
     * @param array $point_list 全部的標的清單
     * @param StudyActivity $activityObj 學習活動物件
     * @return array 合法的學習點
     */
    private function excludeIrregularTarget($point_list,$activityObj)
    {
        $regularTarget = array();
        for($i=0;$i<count($point_list);$i++)
        {
            $nextPoint = $point_list[$i]['next_point'];
            if($this->isEnableTargetId($activityObj,$nextPoint)) {
                array_push($regularTarget,$point_list[$i]);
            }
        }
        return $regularTarget;
    }

    /**
     * 推薦學習點
     * @param int $current_point 目前的標的編號
     * @param int $theme_number 主題編號
     * @param int activity_number 學習活動編號
     * @return array 學習點清單
     */
    public function recommand($current_point,$activity_number)
    {
        // 取得活動物件
        $this->activity = new Study\StudyActivity($activity_number);
        // 取得主題編號
        $themeID = $this->activity->getThemeId();
        // 取得這次活動是否使用虛擬學習點
        $enableVirtual = $this->activity->isEnableVirtual();

        $this->theme = new Study\Theme($themeID);
        // 計算正規化參數gamma值
        $this->gamma = $this->computeNormalizationParameter($themeID);
        // 取得這個點到所有下一個點的資訊
        $pointList = $this->recommand->queryEdgeByID($current_point);
        // 過濾不合法的學習點
        $targetList = $this->excludeIrregularTarget($pointList,$this->activity);

        //計算路徑的權重值
        $pathCost = 0;        // 實體推薦度分數
        $VirtualPathCost = 0; // 虛擬學習點推薦分數

        // 整理可用的學習點清單
        $recommand = array();
        for($i=0;$i<count($targetList);$i++)
        {
            $next_point = $targetList[$i]["next_point"];
            $moveTime = $targetList[$i]["move_time"];
            $nextPoint = new Target\Target($next_point);
            $weight = $this->theme->getWeightByNextTarget($next_point);

            // 這個學習點目前人數已滿
            if($nextPoint->isFullPeople())
            {
                $pathCost = 0;
                $virtualCost = RecommandPoint::ALPHA * $this->gamma * ($weight/$nextPoint->getLearnTime());
                $isEntity=false;

                // 若啟用虛擬學習點推薦
                if($enableVirtual) {
                    // 加入可推薦的學習點清單
                    array_push($recommand,array("nextPoint" => $next_point,
                                        "isEntity" => $isEntity,
                                        "PathCost" => $pathCost,
                                        "VirtualCost" => $virtualCost));
                }
            }
            // 這個學習點目前人數沒有滿
            else
            {
                $isEntity=true;
                if($nextPoint->isNumberOfPeopleZero()) $Rj = 0;
                else $Rj = $nextPoint->getMj()/$nextPoint->getPLj();
                $pathCost = RecommandPoint::ALPHA * $this->gamma * ($weight * ($nextPoint->getS()-$Rj+1)/($moveTime + $nextPoint->getLearnTime()));
                $virtualCost = RecommandPoint::ALPHA * $this->gamma * ($weight/$nextPoint->getLearnTime());

                // 加入可推薦的學習點清單
                array_push($recommand,array("nextPoint" => $next_point,
                                        "isEntity" => $isEntity,
                                        "PathCost" => $pathCost,
                                        "VirtualCost" => $virtualCost));
            }

        }

        // 依照算出來的推薦度分數PathCost排序
        if(count($recommand) >= 1) {

            foreach($recommand as $key=>$value)
            {
                $tmp[$key] = $value["PathCost"];
            }
            array_multisort($tmp,SORT_DESC,$recommand,SORT_DESC);
        }

        return $recommand;
    }

    public function insertRecommandHistory($activity_id, $target_ids) {
        if( count($target_ids) > 0 ) {
            return $this->recommand->insertRecommandHistory($activity_id, $target_ids);
        }
    }
}
