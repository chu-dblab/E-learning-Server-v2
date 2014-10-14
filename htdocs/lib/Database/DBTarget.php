<?php
/**
 * 學習標的資料表
 *
 * 此檔案針對學習標的，以及學習標的的區域、廳等的資料庫查詢用。
 */

namespace UElearning\Database;

use UElearning\Exception;

require_once UELEARNING_LIB_ROOT.'/Database/Database.php';
require_once UELEARNING_LIB_ROOT.'/Database/Exception.php';

/**
 * 學習標的資料表
 * 
 * 此檔案針對學習標的，以及學習標的的區域、廳等的資料表進行操作。
 * 
 *
 * @author          Yuan Chiu <chyuaner@gmail.com>
 * @version         2.0.0
 * @package         UElearning
 * @subpackage      Database
 */
class DBTarget extends Database {
    
    private function queryTargetByWhere($where) {
        
        $sqlString = "SELECT `TID`, Target.`AID`, Area.`HID`, ".
                     "`TNum`, `TName`, `TMapID`, `TLearnTime`, `PLj`, `Mj`, `S`, `Fi` ".
                     "FROM `".$this->table('Target')."` as Target ".
                     "LEFT JOIN `".$this->table('Area')."` as Area ".
                     "ON Area.`AID` = Target.`AID` ".
                     "WHERE ".$where;
		
		$query = $this->connDB->prepare($sqlString);
		$query->execute();
		
		$queryResultAll = $query->fetchAll();
        // 如果有查到一筆以上
        if( count($queryResultAll) >= 1 ) {
            // 製作回傳結果陣列
            $result = array();
            foreach($queryResultAll as $key => $thisResult) { 
                
                array_push($result,
                    array( 'target_id'     => $thisResult['TID'],
                           'area_id'       => $thisResult['AID'],
                           'hall_id'       => $thisResult['HID'],
                           'target_number' => $thisResult['TNum'],
                           'name'          => $thisResult['TName'],
                           'map_url'       => $thisResult['TMapID'],
                           'learn_time'    => $thisResult['TLearnTime'],
                           'PLj'           => $thisResult['PLj'],
                           'Mj'            => $thisResult['Mj'],
                           'S'             => $thisResult['S'],
                           'Fi'            => $thisResult['Fi']
                ));
            }
            return $result;
        }
        // 若都沒查到的話
        else {
            return null;
        }
    }
    
    
    /**
     * 查詢一個標的資料
     * 
     * 
     * 範例: 
     * 
     *     require_once __DIR__.'/../config.php';
     *     require_once UELEARNING_LIB_ROOT.'/Database/DBTarget.php';
     *     use UElearning\Database;
     * 
     *     $db = new Database\DBTarget();
     *     
     *     $targetInfo = $db->queryTarget(4);
     *     echo '<pre>'; print_r($targetInfo); echo '</pre>';
     *     
     * 
     * @param int $tId 標的ID
     * @return array 標的資料陣列，格式為: 
     *     array( 
     *         'target_id'     => <標的ID>,
     *         'area_id'       => <標的所在的區域ID>,
     *         'hall_id'       => <標的所在的廳ID>,
     *         'target_number' => <地圖上的標的編號>,
     *         'name'          => <標的名稱>,
     *         'map_url'       => <地圖路徑>,
     *         'learn_time'    => <預估的學習時間>,
     *         'PLj'           => <學習標的的人數限制>,
     *         'Mj'            => <目前人數>,
     *         'S'             => <學習標的飽和率上限>,
     *         'Fi'            => <學習標的滿額指標>
     *     );
     * 
     */ 
    public function queryTarget($tId) {
		
		$queryResultAll = $this->queryTargetByWhere("`TID`=".$this->connDB->quote($tId));
        
        // 如果有查到一筆以上
        if( count($queryResultAll) >= 1 ) {
            return $queryResultAll[0];
        }
        // 若都沒查到的話
        else {
            return null;
        }
    }
    
    /**
     * 查詢所有標的資料
     * 
     * @return array 標的資料陣列，格式為: 
     *     
     *     array(
     *         array( 
     *             'target_id'     => <標的ID>,
     *             'area_id'       => <標的所在的區域ID>,
     *             'hall_id'       => <標的所在的廳ID>,
     *             'target_number' => <地圖上的標的編號>,
     *             'name'          => <標的名稱>,
     *             'map_url'       => <地圖路徑>,
     *             'learn_time'    => <預估的學習時間>,
     *             'PLj'           => <學習標的的人數限制>,
     *             'Mj'            => <目前人數>,
     *             'S'             => <學習標的飽和率上限>,
     *             'Fi'            => <學習標的滿額指標>
     *         )
     *     );
     * 
     */ 
    public function queryAllTarget() {
        
        return $this->queryAreaByWhere("1");
    }
    
    /**
     * 修改一個標的資訊
     * 
     * @param int    $tId   標的編號
     * @param string $field 欄位名稱
     * @param string $value 內容
     */ 
    function changeTargetData($tId, $field, $value) {
        $sqlField = null;
        switch($field) {
            case 'area_id':       $sqlField = 'AID';         break;
            case 'hall_id':       $sqlField = 'HID';         break;
            case 'target_number': $sqlField = 'TNum';        break;
            case 'name':          $sqlField = 'TName';       break;
            case 'map_url':       $sqlField = 'TMapID';      break;
            case 'learn_time':    $sqlField = 'TLearnTime';  break;
            case 'PLj':           $sqlField = 'PLj';         break;
            case 'Mj':            $sqlField = 'Mj';          break;
            case 'S':             $sqlField = 'S';           break;
            case 'Fi':            $sqlField = 'Fi';          break;
            default:              $sqlField = $field;        break;
        }
        
        
        $sqlString = "UPDATE ".$this->table('Target').
                     " SET `".$sqlField."` = :value".
                     " WHERE `TID` = :tid";
        
        $query = $this->connDB->prepare($sqlString);
		$query->bindParam(':tid', $tId);
        $query->bindParam(':value', $value);
		$query->execute();
    }
    
    // ========================================================================
    
    private function queryAreaByWhere($where) {
        
        $sqlString = "SELECT * FROM `".$this->table('Area')."`".
                     "WHERE ".$where;
		
		$query = $this->connDB->prepare($sqlString);
		$query->execute();
		
		$queryResultAll = $query->fetchAll();
        // 如果有查到一筆以上
        if( count($queryResultAll) >= 1 ) {
            // 製作回傳結果陣列
            $result = array();
            foreach($queryResultAll as $key => $thisResult) { 
                
                array_push($result,
                    array( 'area_id'       => $thisResult['AID'],
                           'hall_id'       => $thisResult['HID'],
                           'floor'         => $thisResult['AFloor'],
                           'area_number'   => $thisResult['ANum'],
                           'name'          => $thisResult['AName'],
                           'map_url'       => $thisResult['AMapID'],
                           'introduction'  => $thisResult['AIntroduction']
                ));
            }
            return $result;
        }
        // 若都沒查到的話
        else {
            return null;
        }
    }
    
    /**
     * 查詢一個區域資料
     * 
     * 
     * 範例: 
     * 
     *     require_once __DIR__.'/../config.php';
     *     require_once UELEARNING_LIB_ROOT.'/Database/DBTarget.php';
     *     use UElearning\Database;
     * 
     *     $db = new Database\DBTarget();
     *     
     *     $areaInfo = $db->queryArea(4);
     *     echo '<pre>'; print_r($areaInfo); echo '</pre>';
     *     
     * 
     * @param int $aId 區域ID
     * @return array 區域資料陣列，格式為: 
     *     array( 
     *         'area_id'       => <區域ID>,
     *         'hall_id'       => <區域所在的廳ID>,
     *         'floor'         => <區域所在的樓層>,
     *         'number'        => <地圖上的區域編號>,
     *         'name'          => <區域名稱>,
     *         'map_url'       => <地圖路徑>,
     *         'introduction'  => <區域簡介>
     *     );
     * 
     */ 
    public function queryArea($aId) {
		
		$queryResultAll = $this->queryAreaByWhere("`AID`=".$this->connDB->quote($aId));
        
        // 如果有查到一筆以上
        if( count($queryResultAll) >= 1 ) {
            return $queryResultAll[0];
        }
        // 若都沒查到的話
        else {
            return null;
        }
    }
    
    /**
     * 查詢所有區域資料
     * 
     * @return array 區域資料陣列，格式為: 
     *     
     *     array(
     *         array( 
     *             'area_id'       => <區域ID>,
     *             'hall_id'       => <區域所在的廳ID>,
     *             'floor'         => <區域所在的樓層>,
     *             'number'        => <地圖上的區域編號>,
     *             'name'          => <區域名稱>,
     *             'map_url'       => <地圖路徑>,
     *             'introduction'  => <區域簡介>
     *         )
     *     );
     * 
     */ 
    public function queryAllArea() {
        
        return $this->queryAreaByWhere("1");
    }
    
    ///**
    // * 修改一個標的資訊
    // * 
    // * @param int    $tId   標的編號
    // * @param string $field 欄位名稱
    // * @param string $value 內容
    // */ 
    //function changeTargetData($tId, $field, $value) {
    ////TODO: 待修成Area
    //    $sqlField = null;
    //    switch($field) {
    //        case 'area_id':       $sqlField = 'AID';         break;
    //        case 'hall_id':       $sqlField = 'HID';         break;
    //        case 'floor':         $sqlField = 'AFloor';      break;
    //        case 'name':          $sqlField = 'TName';       break;
    //        case 'map_url':       $sqlField = 'TMapID';      break;
    //        case 'learn_time':    $sqlField = 'TLearnTime';  break;
    //        case 'PLj':           $sqlField = 'PLj';         break;
    //        case 'Mj':            $sqlField = 'Mj';          break;
    //        case 'S':             $sqlField = 'S';           break;
    //        case 'Fi':            $sqlField = 'Fi';          break;
    //        default:              $sqlField = $field;        break;
    //    }
    //    
    //    
    //    $sqlString = "UPDATE ".$this->table('Target').
    //                 " SET `".$sqlField."` = :value".
    //                 " WHERE `TID` = :tid";
    //    
    //    $query = $this->connDB->prepare($sqlString);
	//	$query->bindParam(':tid', $tId);
    //    $query->bindParam(':value', $value);
	//	$query->execute();
    //}
    
    // ========================================================================
    
    private function queryHallByWhere($where) {
        
        $sqlString = "SELECT * FROM `".$this->table('Hall')."`".
                     "WHERE ".$where;
		
		$query = $this->connDB->prepare($sqlString);
		$query->execute();
		
		$queryResultAll = $query->fetchAll();
        // 如果有查到一筆以上
        if( count($queryResultAll) >= 1 ) {
            // 製作回傳結果陣列
            $result = array();
            foreach($queryResultAll as $key => $thisResult) { 
                
                array_push($result,
                    array( 'hall_id'       => $thisResult['HID'],
                           'name'          => $thisResult['HName'],
                           'map_url'       => $thisResult['HMapID'],
                           'introduction'  => $thisResult['HIntroduction']
                ));
            }
            return $result;
        }
        // 若都沒查到的話
        else {
            return null;
        }
    }
    
    /**
     * 查詢一個廳資料
     * 
     * 
     * 範例: 
     * 
     *     require_once __DIR__.'/../config.php';
     *     require_once UELEARNING_LIB_ROOT.'/Database/DBTarget.php';
     *     use UElearning\Database;
     * 
     *     $db = new Database\DBTarget();
     *     
     *     $hallInfo = $db->queryHall(1);
     *     echo '<pre>'; print_r($hallInfo); echo '</pre>';
     *     
     * 
     * @param int $hId 廳ID
     * @return array 區域資料陣列，格式為: 
     *     array( 
     *         'hall_id'       => <廳的ID>,
     *         'name'          => <廳名稱>,
     *         'map_url'       => <地圖路徑>,
     *         'introduction'  => <區域簡介>
     *     );
     * 
     */ 
    public function queryHall($hId) {
		
		$queryResultAll = $this->queryHallByWhere("`HID`=".$this->connDB->quote($hId));
        
        // 如果有查到一筆以上
        if( count($queryResultAll) >= 1 ) {
            return $queryResultAll[0];
        }
        // 若都沒查到的話
        else {
            return null;
        }
    }
    
    /**
     * 查詢所有區域資料
     * 
     * @return array 區域資料陣列，格式為: 
     *     
     *     array(
     *         array( 
     *             'hall_id'       => <廳的廳ID>,
     *             'name'          => <廳名稱>,
     *             'map_url'       => <地圖路徑>,
     *             'introduction'  => <區域簡介>
     *     );
     * 
     */ 
    public function queryAllHall() {
        
        return $this->queryHallByWhere("1");
    }
    
    ///**
    // * 修改一個標的資訊
    // * 
    // * @param int    $tId   標的編號
    // * @param string $field 欄位名稱
    // * @param string $value 內容
    // */ 
    //function changeTargetData($tId, $field, $value) {
    ////TODO: 待修成Area
    //    $sqlField = null;
    //    switch($field) {
    //        case 'area_id':       $sqlField = 'AID';         break;
    //        case 'hall_id':       $sqlField = 'HID';         break;
    //        case 'floor':         $sqlField = 'AFloor';      break;
    //        case 'name':          $sqlField = 'TName';       break;
    //        case 'map_url':       $sqlField = 'TMapID';      break;
    //        case 'learn_time':    $sqlField = 'TLearnTime';  break;
    //        case 'PLj':           $sqlField = 'PLj';         break;
    //        case 'Mj':            $sqlField = 'Mj';          break;
    //        case 'S':             $sqlField = 'S';           break;
    //        case 'Fi':            $sqlField = 'Fi';          break;
    //        default:              $sqlField = $field;        break;
    //    }
    //    
    //    
    //    $sqlString = "UPDATE ".$this->table('Target').
    //                 " SET `".$sqlField."` = :value".
    //                 " WHERE `TID` = :tid";
    //    
    //    $query = $this->connDB->prepare($sqlString);
	//	$query->bindParam(':tid', $tId);
    //    $query->bindParam(':value', $value);
	//	$query->execute();
    //}
    
}