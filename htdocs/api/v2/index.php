<?php
require_once __DIR__.'/../../config.php';
require UELEARNING_ROOT.'/vendor/autoload.php';
require_once __DIR__.'/src/ApiTemplates.php';
require_once UELEARNING_LIB_ROOT.'/User/User.php';
require_once UELEARNING_LIB_ROOT.'/User/UserSession.php';
require_once UELEARNING_LIB_ROOT.'/User/UserAdmin.php';
require_once UELEARNING_LIB_ROOT.'/Study/StudyActivity.php';
require_once UELEARNING_LIB_ROOT.'/Study/StudyActivityManager.php';
require_once UELEARNING_LIB_ROOT.'/Study/StudyManager.php';
require_once UELEARNING_LIB_ROOT.'/Target/Target.php';
require_once UELEARNING_LIB_ROOT.'/Target/TargetManager.php';
require_once UELEARNING_LIB_ROOT.'/Database/DBInfo.php';
require_once UELEARNING_LIB_ROOT.'/Database/DBQuestion.php';
require_once UELEARNING_LIB_ROOT.'/Recommand/RecommandPoint.php';
require_once UELEARNING_LIB_ROOT.'/Log/Log.php';
require_once UELEARNING_LIB_ROOT.'/Log/XApi.php';
require_once UELEARNING_LIB_ROOT.'/Util/UEHBase.php';
use UElearning\User;
use UElearning\Study;
use UElearning\Target;
use UElearning\Recommand;
use UElearning\Exception;
use UElearning\Database;
use UElearning\Log;
use UElearning\Util;

$app = new \Slim\Slim(array(
    'templates.path' => './', // 設定Path
    'debug' => true
));
$app_template = new ApiTemplates($app);

// 設定成將使用JSON格式輸出
function APIrequest() {
    $app = \Slim\Slim::getInstance();
    $app->view(new \JsonApiView());
    $app->add(new \JsonApiMiddleware());
}


/*
 * 測試用 Say hello!~~~
 * GET http://localhost/api/v2/hello/{string}
 */
$app->get('/hello/:name', 'APIrequest', function ($name) use ($app) {
    $app->render(200,array(
        'error'   => false,
        'msg' => 'Hello, '.$name
    ));
});

// ============================================================================

function login($user_id = null) {
    $app = \Slim\Slim::getInstance();

    // 取得帶來的參數
    $cType = $app->request->getContentType();
    if($cType == 'application/x-www-form-urlencoded') {
        if(!isset($user_id)) {
            $user_id = $_POST['user_id'];
        }
        $password = $_POST['password'];
        $browser  = isset($_POST['browser']) ? $_POST['browser'] : 'api';
    }
    else /*if($cType == 'application/json')*/ {
        $postData = $app->request->getBody();
        $postDataArray = json_decode($postData);
        $user_id = $postDataArray->user_id;
        $password = $postDataArray->password;
        $browser  = isset($postDataArray->browser)
                        ? $postDataArray->browser : 'api';
    }
    /*else {
        $app->render(400, array(
                'Content-Type'=> $cType,
                'error'       => true,
                'msg'     => '',
                'msg_cht' => '輸入參數的Content-Type不在支援範圍內 或是沒有輸入',
                'substatus'   => 102
            )
        );
    }*/

    // 進行登入
    try {
        $session = new User\UserSession();
        $loginToken = $session->login($user_id, $password, $browser);
        $user = $session->getUser($loginToken);
        $sessionInfo = $session->getTokenInfo($loginToken);

        // 取得現在時間，用字串的形式
        $nowDate = date("Y-m-d H:i:s");

        // 取得支援的教材類型
        $db_material = new Database\DBMaterial();
        $all_material_kind = $db_material->queryAllKind();

        // 輸出結果
        $app->render(201,array(
            'user_id'      => $user_id,
            'token'        => $loginToken,
            'browser'      => $browser,
            'user' => array(
                'id'            => $user->getId(),
                'user_id'            => $user->getId(),
                'nickname'           => $user->getNickName(),
                'group_id'           => $user->getGroupID(),
                'group_name'         => $user->getGroupName(),
                'class_id'           => $user->getClassId(),
                'class_name'         => $user->getClassName(),
                'enable'             => $user->isEnable(),
                'build_time'         => $user->getCreateTime(),
                'modify_time'        => $user->getModifyTime(),
                'learnStyle_mode'    => $user->getLearnStyle(),
                'material_mode'      => $user->getMaterialStyle(),
                'enable_noAppoint'   => $user->isEnableNoAppoint(),
                'realname'           => $user->getRealName(),
                'email'              => $user->getEmail(),
                'memo'               => $user->getMemo(),
            ),
            'login_time'   => $sessionInfo['login_date'],
            'current_time' => $nowDate,
            'material_kind'=> $all_material_kind,
            'error'        => false,
            'msg'          => '\''.$user_id.'\' is logined',
            'msg_cht'      => '\''.$user_id.'\'使用者已登入'
        ));
    }
    catch (Exception\UserNoFoundException $e) {
        $app->render(404,array(
            'user_id'     => $user_id,
            'browser'     => $browser,
            'error'       => true,
            'msg'         => '\''.$user_id.'\' is not found',
            'msg_cht'     => '找不到\''.$user_id.'\'使用者'
        ));
    }
    catch (Exception\UserPasswordErrException $e) {
        $app->render(401,array(
            'user_id'     => $user_id,
            'browser'     => $browser,
            'error'       => true,
            'msg'         => 'Input \''.$user_id.'\' password is wrong',
            'msg_cht'     => '\''.$user_id.'\'使用者密碼錯誤',
            'substatus'   => 201
        ));
    }
    catch (Exception\UserNoActivatedException $e) {
        $app->render(401,array(
            'user_id'     => $user_id,
            'browser'     => $browser,
            'error'       => true,
            'msg'         => '\''.$user_id.'\' is not enable',
            'msg_cht'     => '\''.$user_id.'\'帳號目前未啟用',
            'substatus'   => 202
        ));
    }
}

$app->group('/users', 'APIrequest', function () use ($app, $app_template) {

    /*
     * 建立帳號
     * POST http://localhost/api/v2/users
     */
    $app->post('/', function () use ($app) {
        // 取得帶來的參數
        $cType = $app->request->getContentType();
        if($cType == 'application/x-www-form-urlencoded') {
            $user_id          = $_POST['user_id'];
            $password         = $_POST['password'];
            $group_id         = $_POST['group_id'];
            $class_id         = isset($_POST['class_id'])
                                    ? $_POST['class_id'] : null;
            $enable           = isset($_POST['enable'])
                                    ? $_POST['enable'] : null;
            $learnStyle_mode  = isset($_POST['learnStyle_mode'])
                                    ? $_POST['learnStyle_mode'] : null;
            $material_mode    = isset($_POST['material_mode'])
                                    ? $_POST['material_mode'] : null;
            $enable_noAppoint = isset($_POST['enable_noAppoint'])
                                    ? $_POST['enable_noAppoint'] : null;
            $nickname         = isset($_POST['nickname'])
                                    ? $_POST['nickname'] : null;
            $realname         = isset($_POST['realname'])
                                    ? $_POST['realname'] : null;
            $email            = isset($_POST['email'])
                                    ? $_POST['email'] : null;
            $memo             = isset($_POST['memo'])
                                    ? $_POST['memo'] : null;;
        }
        else /*if($cType == 'application/json')*/ {
            $postData = $app->request->getBody();
            $postDataArray = json_decode($postData);
            $user_id          = $postDataArray->user_id;
            $password         = $postDataArray->password;
            $group_id         = $postDataArray->group_id;
            $class_id         = isset($postDataArray->class_id)
                                    ? $postDataArray->class_id : null;
            $enable           = isset($postDataArray->enable)
                                    ? $postDataArray->enable : null;
            $learnStyle_mode  = isset($postDataArray->learnStyle_mode)
                                    ? $postDataArray->learnStyle_mode : null;
            $material_mode    = isset($postDataArray->material_mode)
                                    ? $postDataArray->material_mode : null;
            $enable_noAppoint = isset($postDataArray->enable_noAppoint)
                                    ? $postDataArray->enable_noAppoint : null;
            $nickname         = isset($postDataArray->nickname)
                                    ? $postDataArray->nickname : null;
            $realname         = isset($postDataArray->realname)
                                    ? $postDataArray->realname : null;
            $email            = isset($postDataArray->email)
                                    ? $postDataArray->email : null;
            $memo             = isset($postDataArray->memo)
                                    ? $postDataArray->memo : null;
        }
        /*else {
            $app->render(400, array(
                    'Content-Type'=> $cType,
                    'error'       => true,
                    'msg'     => '',
                    'msg_cht' => '輸入參數的Content-Type不在支援範圍內 或是沒有輸入',
                    'substatus'   => 102
                )
            );
        }*/

        // 建立使用者帳號
        try {
            $userAdmin = new User\UserAdmin();
            $userAdmin->create(
                array( 'user_id'            => $user_id,
                       'password'           => $password,
                       'group_id'           => $group_id,
                       'class_id'           => $class_id,
                       'enable'             => $enable,
                       'learnStyle_mode'    => $learnStyle_mode,
                       'material_mode'      => $material_mode,
                       'enable_noAppoint'   => $enable_noAppoint,
                       'nickname'           => $nickname,
                       'realname'           => $realname,
                       'email'              => $email,
                       'memo'               => $memo)
            );

            // 顯示建立成功
            $app->render(201,array(
                'user_id'            => $user_id,
                'group_id'           => $group_id,
                'class_id'           => $class_id,
                'enable'             => $enable,
                'learnStyle_mode'    => $learnStyle_mode,
                'material_mode'      => $material_mode,
                'enable_noAppoint'   => $enable_noAppoint,
                'nickname'           => $nickname,
                'realname'           => $realname,
                'email'              => $email,
                'memo'               => $memo,
                'error'   => false,
                'msg'     => '\''.$user_id.'\' is created.',
                'msg_cht' => '\''.$user_id.'\'使用者已成功建立'
            ));

        }
        // 若已有重複帳號名稱
        catch (Exception\UserIdExistException $e) {
             $app->render(409,array(
                'user_id'     => $user_id,
                'error'       => true,
                'msg'     => '\''.$user_id.'\' is exist.',
                'msg_cht' => '\''.$user_id.'\'使用者名稱已被使用'
            ));
        }
    });

    /*
     * 取得帳號資訊
     * GET http://localhost/api/v2/users/{帳號ID}
     */
    $app->get('/:user_id', function ($user_id) use ($app) {

        try {
            $user = new User\User($user_id);

            $app->render(200,array(
                'user_id'     => $user_id,
                'nickname'    => $user->getNickName(),
                'class_name'  => $user->getClassName(),
                'error'       => false
            ));
        }
        catch (Exception\UserNoFoundException $e) {
            $app->render(404,array(
                'user_id'     => $user_id,
                'error'       => true,
                'msg'     => '\''.$user_id.'\' is not found',
                'msg_cht' => '找不到\''.$user_id.'\'使用者'
            ));
        }
    });

    /*
     * 登入帳號
     * POST http://localhost/api/v2/users/{帳號ID}/login
     */
    $app->post('/:user_id/login', 'login');

});

$app->group('/tokens', 'APIrequest', function () use ($app, $app_template) {

    /*
     * 登入帳號
     * POST http://localhost/api/v2/tokens
     */
    $app->post('/', 'login');

    /*
     * 取得已登入的帳號資訊
     * GET http://localhost/api/v2/tokens/{登入Token}
     */
    $app->get('/:token', function ($token) use ($app) {

        try {
            // 正常寫法
            $userSession = new User\UserSession();
            $user = $userSession->getUser($token);

            $app->render(200,array(
                'token' => $token,
                'user' => array(
                    'id'            => $user->getId(),
                    'user_id'            => $user->getId(),
                    'nickname'           => $user->getNickName(),
                    'group_id'           => $user->getGroupID(),
                    'group_name'         => $user->getGroupName(),
                    'class_id'           => $user->getClassId(),
                    'class_name'         => $user->getClassName(),
                    'enable'             => $user->isEnable(),
                    'build_time'         => $user->getCreateTime(),
                    'modify_time'        => $user->getModifyTime(),
                    'learnStyle_mode'    => $user->getLearnStyle(),
                    'material_mode'      => $user->getMaterialStyle(),
                    'enable_noAppoint'   => $user->isEnableNoAppoint(),
                    'realname'           => $user->getRealName(),
                    'email'              => $user->getEmail(),
                    'memo'               => $user->getMemo(),
                ),
                'error' => false
            ));
        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(404,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
    });

    /*
     * 登出此登入階段
     * DELETE http://localhost/api/v2/tokens/{登入Token}
     */
    $app->delete('/:token', function ($token) use ($app) {

        try {
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);
            $session->logout($token);

            $app->render(204,array(
                'token'   => $token,
                'user_id' => $user_id,
                'error'   => false,
                'msg'     => '\''.$user_id.'\' this session is logout.',
                'msg_cht' => '\''.$user_id.'\'此登入階段已登出'
            ));
        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(404,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
    });

    /*
     * 登出此此使用者其他登入階段
     * DELETE http://localhost/api/v2/tokens/{登入Token}/session/other
     */
    $app->delete('/:token/session/other', function ($token) use ($app) {

        try {
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);
            $logoutTotal = $session->logoutOtherSession($token);
            $inLoginTotal = $session->getCurrentLoginTotalByUserId($user_id);

            $app->render(204,array(
                'token'        => $token,
                'user_id'      => $user_id,
                'logout_total' => $logoutTotal,
                'login_total'  => $inLoginTotal,
                'error'        => false,
                'msg'          => '\''.$user_id.'\' other session is logout.',
                'msg_cht'      => '\''.$user_id.'\'此登入階段之外的皆已登出'
            ));
        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(404,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
    });

    // ------------------------------------------------------------------------

    /*
     * 取得可用的學習活動
     * GET http://localhost/api/v2/tokens/{登入Token}/activitys
     */
    $app->get('/:token/activitys', function ($token) use ($app) {
        try {
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);

            $studyMgr = new Study\StudyActivityManager();
            $studyList = $studyMgr->getEnableActivityByUserId($user_id);

            // TODO: $studyList 分離重新包裝陣列
            $app->render(200,array(
                'token'           => $token,
                'user_id'         => $user_id,
                'enable_activity' => $studyList,
                'error'           => false,
            ));
        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(401,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
    });

    /*
     * 開始進行一場學習活動
     * POST http://localhost/api/v2/tokens/{登入Token}/activitys
     */
    $app->post('/:token/activitys', function ($token) use ($app) {

        // 取得帶來的參數
        $cType = $app->request->getContentType();
        if($cType == 'application/x-www-form-urlencoded') {
            $themeId          = $_POST['theme_id'];
            $learnTime        = isset($_POST['learn_time'])
                                    ? $_POST['learn_time'] : null;
            $timeForce        = isset($_POST['time_force'])
                                    ? $_POST['time_force'] : null;
            $learnStyle       = isset($_POST['learnStyle_mode'])
                                    ? $_POST['learnStyle_mode'] : null;
            $learnStyle_force = isset($_POST['learnStyle_force'])
                                    ? $_POST['learnStyle_force'] : null;
            $enable_virtual   = isset($_POST['enable_virtual'])
                                    ? $_POST['enable_virtual'] : null;
            $materialMode     = isset($_POST['material_mode'])
                                    ? $_POST['material_mode'] : null;
        }
        else /*if($cType == 'application/json')*/ {
            $postData = $app->request->getBody();
            $postDataArray = json_decode($postData);
            //$user_id          = $postDataArray->user_id;
            $app->render(400, array(
                    'Content-Type'=> $cType,
                    'error'       => true,
                    'msg'     => '',
                    'msg_cht' => '輸入參數的Content-Type不在支援範圍內 或是沒有輸入',
                    'substatus'   => 102
                )
            );
        }
        /*else {
            $app->render(400, array(
                    'Content-Type'=> $cType,
                    'error'       => true,
                    'msg'     => '',
                    'msg_cht' => '輸入參數的Content-Type不在支援範圍內 或是沒有輸入',
                    'substatus'   => 102
                )
            );
        }*/

        try {
            // 查詢使用者
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);

            // 開始進行學習活動
            $studyMgr = new Study\StudyActivityManager();
            $studyId  = $studyMgr->startActivity($user_id, $themeId,
                                                 $learnTime, $timeForce,
                                                 $learnStyle, $learnStyle_force,
                                                 $enable_virtual, $materialMode);

            // 取得開始後的學習活動資訊
            $sact = new Study\StudyActivity($studyId);

            // 取得此活動的主題
            $tid = $sact->getThemeId();

            // 取得主題內所有的標的資訊
            $target_manager = new Target\TargetManager();
            $all_targets = $target_manager->getAllTargetInfoByTheme($tid);

            // 取得本次採用的教材風格
            $materialMode = $sact->getMaterialStyle();

            // 處理噴出結果
            $output_targets = array();
            foreach($all_targets as $thisTargetArray) {

                // 取得教材路徑
                $targetObject = new Target\Target($thisTargetArray['target_id']);
                $materialUrl = $targetObject->getMaterialUrl(true, $materialMode);
                $virtualMaterialUrl = $targetObject->getMaterialUrl(false, $materialMode);

                $thisOutput = array(
                    'theme_id'      => $thisTargetArray['theme_id'],
                    'target_id'     => $thisTargetArray['target_id'],
                    'weights'       => $thisTargetArray['weights'],
                    'hall_id'       => $thisTargetArray['hall_id'],
                    'hall_name'     => $thisTargetArray['hall_name'],
                    'area_id'       => $thisTargetArray['area_id'],
                    'area_name'     => $thisTargetArray['area_name'],
                    'floor'         => $thisTargetArray['floor'],
                    'area_number'   => $thisTargetArray['area_number'],
                    'target_number' => $thisTargetArray['target_number'],
                    'name'          => $thisTargetArray['name'],
                    'map_url'       => $thisTargetArray['map_url'],
                    'material_url'  => $materialUrl,
                    'virtual_material_url' => $virtualMaterialUrl,
                    'learn_time'    => $thisTargetArray['learn_time'],
                    'PLj'           => $thisTargetArray['PLj'],
                    'Mj'            => $thisTargetArray['Mj'],
                    'S'             => $thisTargetArray['S'],
                    'Fj'            => $thisTargetArray['Fj']
                );
                array_push($output_targets, $thisOutput);
            }

            // 噴出結果
            $app->render(200,array(
                'token'       => $token,
                'user_id'     => $user_id,
                'activity_id' => $sact->getId(),
                'activity'    => array(
                    'activity_id'      => $sact->getId(),
                    'theme_id'         => $sact->getThemeId(),
                    'theme_name'       => $sact->getThemeName(),
                    'start_target_id'  => $sact->getStartTargetId(),
                    'start_time'       => $sact->getStartTime(),
                    'expired_time'     => $sact->getExpiredTime(),
                    'have_time'        => $sact->getRealLearnTime(),
                    'learn_time'       => $sact->getLearnTime(),
                    'delay'            => $sact->getDelay(),
                    'remaining_time'   => $sact->getRealLearnTime(),
                    'time_force'       => $sact->isForceLearnTime(),
                    'learnStyle_mode'  => $sact->getLearnStyle(),
                    'learnStyle_force' => $sact->isForceLearnStyle(),
                    'enable_virtual'   => $sact->isEnableVirtual(),
                    'material_mode'    => $sact->getMaterialStyle(),
                    'target_total'     => $sact->getPointTotal(),
                    'learned_total'    => $sact->getLearnedPointTotal()
                ),
                'targets'    => $output_targets,
                'error'            => false
            ));
        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(401,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
        catch (Exception\StudyActivityNoFoundException $e) {
            $app->render(500,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'Start activity fail.',
                'msg_cht' => '建立學習活動失敗'
            ));
        }

    });

    /*
     * 取得學習中狀況資料
     * GET http://localhost/api/v2/tokens/{登入Token}/activitys/{學習中活動編號}
     */
    $app->get('/:token/activitys/:said', function ($token, $saId) use ($app) {

        try {
            // 查詢使用者
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);

            // 取得開始後的學習活動資訊
            $sact = new Study\StudyActivity($saId);

            // TODO: 取得主題內所有的標的資訊

            // 確認此學習活動是否為本人所有
            if($sact->getUserId() == $user_id) {

                // 取得此活動的主題
                $tid = $sact->getThemeId();

                // 取得主題內所有的標的資訊
                $target_manager = new Target\TargetManager();
                $all_targets = $target_manager->getAllTargetInfoByTheme($tid);

                // 取得本次採用的教材風格
                $materialMode = $sact->getMaterialStyle();

                // 處理噴出結果
                $output_targets = array();
                foreach($all_targets as $thisTargetArray) {

                    // 取得教材路徑
                    $targetObject = new Target\Target($thisTargetArray['target_id']);
                    $materialUrl = $targetObject->getMaterialUrl(true, $materialMode);
                    $virtualMaterialUrl = $targetObject->getMaterialUrl(false, $materialMode);

                    $thisOutput = array(
                        'theme_id'      => $thisTargetArray['theme_id'],
                        'target_id'     => $thisTargetArray['target_id'],
                        'weights'       => $thisTargetArray['weights'],
                        'hall_id'       => $thisTargetArray['hall_id'],
                        'hall_name'     => $thisTargetArray['hall_name'],
                        'area_id'       => $thisTargetArray['area_id'],
                        'area_name'     => $thisTargetArray['area_name'],
                        'floor'         => $thisTargetArray['floor'],
                        'area_number'   => $thisTargetArray['area_number'],
                        'target_number' => $thisTargetArray['target_number'],
                        'name'          => $thisTargetArray['name'],
                        'map_url'       => $thisTargetArray['map_url'],
                        'material_url'  => $materialUrl,
                        'virtual_material_url' => $virtualMaterialUrl,
                        'learn_time'    => $thisTargetArray['learn_time'],
                        'PLj'           => $thisTargetArray['PLj'],
                        'Mj'            => $thisTargetArray['Mj'],
                        'S'             => $thisTargetArray['S'],
                        'Fj'            => $thisTargetArray['Fj']
                    );
                    array_push($output_targets, $thisOutput);
                }

                // 噴出資訊
                $app->render(200,array(
                    'token'       => $token,
                    'user_id'     => $user_id,
                    'activity_id' => $sact->getId(),
                    'activity'    => array(
                        'activity_id'      => $sact->getId(),
                        'theme_id'         => $sact->getThemeId(),
                        'theme_name'       => $sact->getThemeName(),
                        'start_target_id'  => $sact->getStartTargetId(),
                        'start_time'       => $sact->getStartTime(),
                        'expired_time'     => $sact->getExpiredTime(),
                        'have_time'        => $sact->getRealLearnTime(),
                        'learn_time'       => $sact->getLearnTime(),
                        'delay'            => $sact->getDelay(),
                        'remaining_time'   => $sact->getRemainingTime(),
                        'time_force'       => $sact->isForceLearnTime(),
                        'learnStyle_mode'  => $sact->getLearnStyle(),
                        'learnStyle_force' => $sact->isForceLearnStyle(),
                        'enable_virtual'   => $sact->isEnableVirtual(),
                        'material_mode'    => $sact->getMaterialStyle(),
                        'target_total'     => $sact->getPointTotal(),
                        'learned_total'    => $sact->getLearnedPointTotal()
                    ),
                    'targets'    => $output_targets,
                    'error'            => false
                ));
            }
            // 若非本人所有，則視同無此活動
            else {
                throw new Exception\StudyActivityNoFoundException($saId);
            }

        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(401,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
        catch (Exception\StudyActivityNoFoundException $e) {
            $app->render(404,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No found this activity.',
                'msg_cht' => '沒有此學習活動'
            ));
        }
    });

    /*
     * 結束這場學習活動
     * POST http://localhost/api/v2/tokens/{登入Token}/activitys/{學習中活動編號}/finish
     */
    $app->post('/:token/activitys/:said/finish', function ($token, $saId) use ($app) {

        try {
            // 查詢使用者
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);

            // 取得開始後的學習活動資訊
            $sact = new Study\StudyActivity($saId);

            // 確認此學習活動是否為本人所有
            if($sact->getUserId() == $user_id) {

                // 結束學習活動
                $sact->finishActivity();

                // 傳送紀錄給紀錄模組伺服器
                if(UELEARNING_UEHBASE_ENABLE) {
                    $user = new User\User($user_id);
                    $xapi = new Log\XApi();
                    $u_name = $user->getName();
                    $u_email = $user->getEmail();
                    $c_name = $user->getClassName();
                    $lmode = $user->getLearnStyle();

                    //$date = strtotime("now");
                    $date = date('Y-m-d H:i:s');
                    $duration = 1; // TODO: 期間
                    $post_data = $xapi->endStudyActivity($date, $token, $duration, $u_name, $u_email, $c_name,
                    $saId, $lmode);

                    $hbase = new Util\UEHBase();
                    $hbase->sendLog($post_data);
                }

                // 噴出學習完畢後的活動資料
                $app->render(201,array(
                    'token'       => $token,
                    'user_id'     => $user_id,
                    'activity_id' => $sact->getId(),
                    'activity'    => array(
                        'activity_id'      => $sact->getId(),
                        'theme_id'         => $sact->getThemeId(),
                        'theme_name'       => $sact->getThemeName(),
                        'start_time'       => $sact->getStartTime(),
                        'end_time'         => $sact->getEndTime(),
                        'learnStyle_mode'  => $sact->getLearnStyle(),
                        'learnStyle_force' => $sact->isForceLearnStyle(),
                        'enable_virtual'   => $sact->isEnableVirtual(),
                        'material_mode'    => $sact->getMaterialStyle(),
                        'target_total'     => $sact->getPointTotal(),
                        'learned_total'    => $sact->getLearnedPointTotal()
                    ),
                    'error'            => false
                ));
            }
            // 若非本人所有，則視同無此活動
            else {
                throw new Exception\StudyActivityNoFoundException($saId);
            }
        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(401,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
        catch (Exception\StudyActivityNoFoundException $e) {
            $app->render(404,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No found this activity.',
                'msg_cht' => '沒有此學習活動'
            ));
        }
        catch (Exception\StudyActivityFinishedException $e) {
            $app->render(405,array(
                'token'       => $token,
                'user_id'     => $user_id,
                'activity_id' => $sact->getId(),
                'activity'    => array(
                    'activity_id'      => $sact->getId(),
                    'theme_id'         => $sact->getThemeId(),
                    'theme_name'       => $sact->getThemeName(),
                    'start_time'       => $sact->getStartTime(),
                    'end_time'         => $sact->getEndTime(),
                    'learnStyle_mode'  => $sact->getLearnStyle(),
                    'learnStyle_force' => $sact->isForceLearnStyle(),
                    'material_mode'    => $sact->getMaterialStyle(),
                    'target_total'     => $sact->getPointTotal(),
                    'learned_total'    => $sact->getLearnedPointTotal()
                ),
                'error'   => true,
                'msg'     => 'The activity is endded',
                'msg_cht' => '此活動已經結束了'
            ));
        }
    });

    /*
     * 預約學習活動資料
     * GET http://localhost/api/v2/tokens/{登入Token}/will/{預約編號}
     */
    $app->get('/:token/will/:swid', function ($token, $swId) use ($app) {
        // TODO: 學習中狀況資料
    });

    // ------------------------------------------------------------------------

    /*
     * 取得此活動中所有的標的資料
     * GET http://localhost/api/v2/tokens/{登入Token}/activitys/{學習中活動編號}/points
     */
    $app->get('/:token/activitys/:said/points', function ($token, $saId) use ($app) {

        try {
            // 查詢使用者
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);

            // 取得開始後的學習活動資訊
            $sact = new Study\StudyActivity($saId);

            // 確認此學習活動是否為本人所有
            if($sact->getUserId() == $user_id) {

                // 取得此活動的主題
                $tid = $sact->getThemeId();

                // 取得主題內所有的標的資訊
                $target_manager = new Target\TargetManager();
                $all_targets = $target_manager->getAllTargetInfoByTheme($tid);

                // 取得本次採用的教材風格
                $materialMode = $sact->getMaterialStyle();

                // 處理噴出結果
                $output_targets = array();
                foreach($all_targets as $thisTargetArray) {

                    // 取得教材路徑
                    $targetObject = new Target\Target($thisTargetArray['target_id']);
                    $materialUrl = $targetObject->getMaterialUrl(true, $materialMode);
                    $virtualMaterialUrl = $targetObject->getMaterialUrl(false, $materialMode);

                    $thisOutput = array(
                        'theme_id'      => $thisTargetArray['theme_id'],
                        'target_id'     => $thisTargetArray['target_id'],
                        'weights'       => $thisTargetArray['weights'],
                        'hall_id'       => $thisTargetArray['hall_id'],
                        'hall_name'     => $thisTargetArray['hall_name'],
                        'area_id'       => $thisTargetArray['area_id'],
                        'area_name'     => $thisTargetArray['area_name'],
                        'floor'         => $thisTargetArray['floor'],
                        'area_number'   => $thisTargetArray['area_number'],
                        'target_number' => $thisTargetArray['target_number'],
                        'name'          => $thisTargetArray['name'],
                        'map_url'       => $thisTargetArray['map_url'],
                        'material_url'  => $materialUrl,
                        'virtual_material_url' => $virtualMaterialUrl,
                        'learn_time'    => $thisTargetArray['learn_time'],
                        'PLj'           => $thisTargetArray['PLj'],
                        'Mj'            => $thisTargetArray['Mj'],
                        'S'             => $thisTargetArray['S'],
                        'Fj'            => $thisTargetArray['Fj']
                    );
                    array_push($output_targets, $thisOutput);
                }

                // 噴出結果
                $app->render(200,array(
                    'token'       => $token,
                    'user_id'     => $user_id,
                    'activity_id' => $sact->getId(),
                    'targets'    => $output_targets,
                    'error'            => false
                ));

            }
            // 若非本人所有，則視同無此活動
            else {
                throw new Exception\StudyActivityNoFoundException($saId);
            }

        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(401,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
        catch (Exception\StudyActivityNoFoundException $e) {
            $app->render(404,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No found this activity.',
                'msg_cht' => '沒有此學習活動'
            ));
        }
    });

    /*
     * 取得此標的資料
     * GET http://localhost/api/v2/tokens/{登入Token}/activitys/{學習中活動編號}/points/{標的編號}
     */
    $app->get('/:token/activitys/:said/points/:tid', function ($token, $saId, $tId) use ($app) {

        try {
            // 查詢使用者
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);

            // 取得開始後的學習活動資訊
            $sact = new Study\StudyActivity($saId);

            // 確認此學習活動是否為本人所有
            if($sact->getUserId() == $user_id) {

                // 取得此活動的主題
                $thid = $sact->getThemeId();

                // 取得本次採用的教材風格
                $materialMode = $sact->getMaterialStyle();

                // 取得主題內所有的標的資訊
                $target = new Target\Target($thid);
                $materialUrl = $target->getMaterialUrl(true, $materialMode);
                $virtualMaterialUrl = $target->getMaterialUrl(false, $materialMode);

                // 處理噴出結果
                $output_targets = array(
                        'theme_id'      => $thid,
                        'target_id'     => $target->getId(),
                        'hall_id'       => $target->getHallId(),
                        //'hall_name'     => $thisTargetArray['hall_name'],
                        'area_id'       => $target->getAreaId(),
                        //'area_name'     => $thisTargetArray['area_name'],
                        //'floor'         => $thisTargetArray['floor'],
                        //'area_number'   => $thisTargetArray['area_number'],
                        'target_number' => $target->getNumber(),
                        'name'          => $target->getName(),
                        'map_url'       => $target->getMapUrl(),
                        'material_url'  => $materialUrl,
                        'virtual_material_url' => $virtualMaterialUrl,
                        'learn_time'    => $target->getLearnTime(),
                        'PLj'           => $target->getPLj(),
                        'Mj'            => $target->getMj(),
                        'S'             => $target->getS(),
                        'Fj'            => $target->getFj()
                );

                // 噴出結果
                $app->render(200,array(
                    'token'       => $token,
                    'user_id'     => $user_id,
                    'activity_id' => $sact->getId(),
                    'target'      => $output_targets,
                    'error'       => false
                ));

            }
            // 若非本人所有，則視同無此活動
            else {
                throw new Exception\StudyActivityNoFoundException($saId);
            }

        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(401,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
        catch (Exception\StudyActivityNoFoundException $e) {
            $app->render(404,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No found this activity.',
                'msg_cht' => '沒有此學習活動'
            ));
        }
    });


    /*
     * 進入此學習點
     * POST http://localhost/api/v2/tokens/{登入Token}/activitys/{學習中活動編號}/points/{標的編號}/toin
     */
    $app->post('/:token/activitys/:said/points/:tid/toin', function ($token, $saId, $tId) use ($app) {

        // 取得帶來的參數
        $cType = $app->request->getContentType();
        if($cType == 'application/x-www-form-urlencoded') {
            $is_entity  = isset($_POST['is_entity']) ? $_POST['is_entity'] : true;
        }
        else /*if($cType == 'application/json')*/ {
            $postData = $app->request->getBody();
            $postDataArray = json_decode($postData);
            $is_entity  = isset($postDataArray->is_entity)
                            ? $postDataArray->is_entity : true;
        }
        /*else {
            $app->render(400, array(
                    'Content-Type'=> $cType,
                    'error'       => true,
                    'msg'     => '',
                    'msg_cht' => '輸入參數的Content-Type不在支援範圍內 或是沒有輸入',
                    'substatus'   => 102
                )
            );
        }*/

        try {
            // 查詢使用者
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);

            // 取得開始後的學習活動資訊
            $sact = new Study\StudyActivity($saId);

            // 確認此學習活動是否為本人所有
            if($sact->getUserId() == $user_id) {

                // 進入學習點
                try{
                    $sid = $sact->toInTarget($tId, $is_entity);

                    // 噴出結果
                    $app->render(200,array(
                        'token'       => $token,
                        'user_id'     => $user_id,
                        'activity_id' => $sact->getId(),
                        'study_id'    => $sid,
                        'error'       => false
                    ));
                }
                // 若狀態為正在標的內學習時，強制當成離開標的，重新進入
                catch (Exception\InLearningException $e) {

                    // 查詢目前所在的標的
                    $inTId = $sact->getCurrentInTarget();

                    // 登記離開此標的
                    $sact->toOutTarget($inTId);

                    // 重新登記進入此標的
                    $sid = $sact->toInTarget($tId, $is_entity);

                    // 噴出結果
                    $app->render(200,array(
                        'token'       => $token,
                        'user_id'     => $user_id,
                        'activity_id' => $sact->getId(),
                        'study_id'    => $sid,
                        'error'       => false
                    ));
                }

            }
            // 若非本人所有，則視同無此活動
            else {
                throw new Exception\StudyActivityNoFoundException($saId);
            }

        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(401,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
        catch (Exception\StudyActivityNoFoundException $e) {
            $app->render(404,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No found this activity.',
                'msg_cht' => '沒有此學習活動'
            ));
        }

    });

    /*
     * 離開此學習點
     * POST http://localhost/api/v2/tokens/{登入Token}/activitys/{學習中活動編號}/points/{標的編號}/toout
     */
    $app->post('/:token/activitys/:said/points/:tid/toout', function ($token, $saId, $tId) use ($app) {

        $app = \Slim\Slim::getInstance();

        // 取得帶來的參數
        $cType = $app->request->getContentType();

        if($cType == 'application/json') {
            $postData = $app->request->getBody();
            $postDataJson = json_decode($postData);

            $ans_json = $postDataJson->answers;
        }

        try {
            // 查詢使用者
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);

            // 取得開始後的學習活動資訊
            $sact = new Study\StudyActivity($saId);

            // 確認此學習活動是否為本人所有
            if($sact->getUserId() == $user_id) {

                // 離開學習點
                try {
                    $smang = new Study\StudyManager();
                    $the_study_id = $smang->getCurrentInStudyId($saId);
                    $the_study = new Study\Study($the_study_id);
                    $sact->toOutTarget($tId);

                    // 紀錄回答問題
                    $db_recommend = new Database\DBQuestion();
                    foreach($ans_json as $the_ans) {
                        $db_recommend->insert($saId, $the_ans->target_id, $the_ans->question_time, $the_ans->answer_time, $the_ans->quest_id, $the_ans->answer, $the_ans->correct);
                    }

                    // 傳送紀錄給紀錄模組伺服器
                    if(UELEARNING_UEHBASE_ENABLE) {
                        $user = new User\User($user_id);
                        $xapi = new Log\XApi();
                        $u_name = $user->getName();
                        $u_email = $user->getEmail();
                        $c_name = $user->getClassName();
                        $lmode = $user->getLearnStyle();
                        $target_id = $the_study->getTargetId();
                        $target = new Target\Target($target_id);
                        $target_name = $target->getName();
                        $floor = $target->getFloor();
                        $hall_id = $target->getHallId();
                        $hall_name = $target->getHallName();
                        $area_id = $target->getAreaId();
                        $area_name = $target->getAreaName();

                        //$date = strtotime("now");
                        $date = date('Y-m-d H:i:s');
                        $duration = $the_study->getElapsedSec();
                        // TODO: Material ID
                        $post_data = $xapi->readMaterial($date, $token, $duration, $u_name, $u_email, $c_name,
                        $saId, $lmode,
                        $hall_id, $hall_name, $area_id, $area_name, $floor, $target_id, $target_name, '1');

                        $hbase = new Util\UEHBase();
                        $hbase->sendLog($post_data);

                        $debug_array = array();
                        foreach($ans_json as $the_ans) {
                            $qDate = strtotime($the_ans->question_time);
                            $aDate = strtotime($the_ans->answer_time);
                            $ans_duration = (int)$aDate - (int)$qDate;

                            $post_ans_data = $xapi->answerQuestion($the_ans->question_time, $token, $ans_duration, $u_name, $u_email, $c_name,
                            $saId, $lmode,
                            $hall_id, $hall_name, $area_id, $area_name, $floor, $target_id, $target_name,
                            $the_ans->quest_id, $the_ans->answer, $the_ans->correct);

                            array_push($debug_array, $post_ans_data);
                            $hbase->sendLog($post_ans_data);
                        }
                    }

                    // 噴出結果
                    $app->render(201,array(
                        'token'       => $token,
                        'user_id'     => $user_id,
                        'answers'     => $ans_json,
                        'activity_id' => $sact->getId(),
                        'error'       => false,
                        'debug' => $debug_array
                    ));
                }
                // 如果此標的尚未登記為已進入
                catch (Exception\NoInLearningException $e) {
                    // 當作進去此標的
                    // TODO: 這邊先暫時當成是以實體方式進入，之後要修成Client發出離開訊息時，也一併帶入剛剛的為實體or虛擬
                    $sact->toInTarget($tId, true);
                    $sact->toOutTarget($tId);

                    // 紀錄回答問題
                    $db_recommend = new Database\DBQuestion();
                    foreach($ans_json as $the_ans) {
                        $db_recommend->insert($saId, $the_ans->target_id, $the_ans->question_time, $the_ans->answer_time, $the_ans->quest_id, $the_ans->answer, $the_ans->correct);
                    }

                    // 噴出結果
                    $app->render(201,array(
                        'token'       => $token,
                        'user_id'     => $user_id,
                        'answers'     => $ans_json,
                        'activity_id' => $sact->getId(),
                        'error'       => false
                    ));

                    // 噴出結果
                    $app->render(201,array(
                        'token'       => $token,
                        'user_id'     => $user_id,
                        'activity_id' => $sact->getId(),
                        'error'       => false
                    ));
                }

            }
            // 若非本人所有，則視同無此活動
            else {
                throw new Exception\StudyActivityNoFoundException($saId);
            }

        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(401,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
        catch (Exception\StudyActivityNoFoundException $e) {
            $app->render(404,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No found this activity.',
                'msg_cht' => '沒有此學習活動'
            ));
        }

    });

    /*
     * 推薦學習點
     * POST http://localhost/api/v2/tokens/{登入Token}/activitys/{學習中活動編號}/recommand?current_point={目前所在的學習點編號}
     * TODO: 將上方的重複的程式碼片段獨立開來
     */
    $app->post('/:token/activitys/:said/recommand', function ($token, $saId) use ($app) {
        if(isset($_GET['current_point'])) { $currentTId = $_GET['current_point']; }

        function output_the_target_array($tId, $isEntity, $materialMode) {
            $thisOutput = array();
            $target = new Target\Target($tId);
            $thisOutput = array(
                'target_id'     => $target->getId(),
                'is_entity'     => $isEntity,
                'hall_id'       => $target->getHallId(),
                'area_id'       => $target->getAreaId(),
                'target_number' => $target->getNumber(),
                'name'          => $target->getName(),
                'map_url'       => $target->getMapUrl(),
                'material_url'  => $target->getMaterialUrl($isEntity, $materialMode),
                'learn_time'    => $target->getLearnTime(),
                'PLj'           => $target->getPLj(),
                'Mj'            => $target->getMj(),
                'S'             => $target->getS(),
                'Fj'            => $target->getFj()
            );
            return $thisOutput;
        }

        try {
            // 查詢使用者
            $session = new User\UserSession();
            $user_id = $session->getUserId($token);

            // 取得開始後的學習活動資訊
            $sact = new Study\StudyActivity($saId);

            // 確認此學習活動是否為本人所有
            if($sact->getUserId() == $user_id) {

                // 必填參數有填
                if( isset($currentTId) ) {

                    // 查詢目前所在的標的
                    $inTId = $sact->getCurrentInTarget();
                    // 登記離開此標的
                    if($inTId) {
                        $sact->toOutTarget($inTId);
                    }

                    $currentTId = (int)$currentTId;

                    $tid = $sact->getThemeId(); // 取得此活動的主題
                    $maxItemTotal = $sact->getLearnStyle(); // 取得最大推薦數

                    // 取得本次採用的教材風格
                    $materialMode = $sact->getMaterialStyle();

                    // 學習時間已過並設強制結束
                    if($sact->isForceLearnTime() && $sact->getRemainingTime() <= 0) {
                        $isEnd = true;

                        $result_recommand_total = 0;
                        $output_targets = array();
                    }
                    // 是否已經學完了
                    else if($sact->getRemainingPointTotal() <= 0) {
                        $isEnd = true;

                        $result_recommand_total = 0;
                        $output_targets = array();
                    }
                    else {
                        $isEnd = false;

                        // 若設定為自由探索
                        if($maxItemTotal == 0) {

                            // 取得主題內所有的標的資訊
                            $target_manager = new Target\TargetManager();
                            $all_targets = $target_manager->getAllTargetInfoByTheme($tid);

                            $theme_ids = array();
                            foreach($all_targets as $the_target) {
                                array_push($theme_ids, $the_target['target_id']);
                            }

                            // 過濾已學習過標的
                            $studyMng = new Study\StudyManager();
                            $learn_ids = $studyMng->getLearnedTargetId($saId);

                            if(count($learn_ids)>0) {
                                $recommandResult = array_diff($theme_ids,$learn_ids);
                                $result_recommand_total = count($theme_ids) - count($learn_ids);

                            }
                            else {
                                $recommandResult = $theme_ids;
                                $result_recommand_total = count($theme_ids);
                            }
                            $output_targets = array();
                            foreach($recommandResult as $theresult) {
                                array_push($output_targets, output_the_target_array($theresult, true, $materialMode));
                            }

                        }
                        else {
                            // 取得推薦的學習點
                            $recommand = new Recommand\RecommandPoint();
                            $recommandResult = $recommand->recommand($currentTId, $saId);
                            $recommandTotal = count($recommandResult);
                            if($recommandTotal > $maxItemTotal) {
                                $result_recommand_total = $maxItemTotal;
                            }
                            else {
                                $result_recommand_total = $recommandTotal;
                            }

                            // 製作
                            $output_targets = array();
                            $output_target_ids = array();
                            for($i=0; $i<$result_recommand_total; $i++) {
                                $target_id = $recommandResult[$i]['nextPoint'];
                                $isEntity = $recommandResult[$i]['isEntity'];
                                array_push($output_targets, output_the_target_array($target_id, $isEntity, $materialMode));
                                array_push($output_target_ids, $target_id);
                            }

                            // 紀錄所有推薦進歷程
                            $recommand->insertRecommandHistory($saId, $output_target_ids);
                        }

                    }


                    // 噴出結果
                    $app->render(201,array(
                        'token'             => $token,
                        'user_id'           => $user_id,
                        'activity_id'       => $sact->getId(),
                        'current_target_id' => $currentTId,
                        'is_end'            => $isEnd,
                        'recommand_total'   => $result_recommand_total,
                        'recommand_target'  => $output_targets,
                        'error'             => false
                    ));

                }
                else {
                    $app->render(400,array(
                        'token'   => $token,
                        'error'   => true,
                        'msg'     => 'No input \'current_point\' param.',
                        'msg_cht' => '缺少 \'current_point\' 參數'
                    ));
                }

            }
            // 若非本人所有，則視同無此活動
            else {
                throw new Exception\StudyActivityNoFoundException($saId);
            }

        }
        catch (Exception\LoginTokenNoFoundException $e) {
            $app->render(401,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No \''.$token.'\' session. Please login again.',
                'msg_cht' => '沒有\''.$token.'\'登入階段，請重新登入',
                'substatus'   => 204
            ));
        }
        catch (Exception\StudyActivityNoFoundException $e) {
            $app->render(404,array(
                'token'   => $token,
                'error'   => true,
                'msg'     => 'No found this activity.',
                'msg_cht' => '沒有此學習活動'
            ));
        }
    });
});

// ============================================================================

/*
 * 輸入所有紀錄
 * GET http://localhost/api/v2/logs
 */
$app->post('/logs', 'APIrequest', function () use ($app) {
    $app = \Slim\Slim::getInstance();

    // 取得帶來的參數
    $cType = $app->request->getContentType();

    if($cType == 'application/json') {
        $postData = $app->request->getBody();
        $postDataJson = json_decode($postData);

        $logs_json = $postDataJson->logs_data;
    }

    $log_utils = new Log\Log();

    for($i=0; $i<count($logs_json); $i++) {
        $lid = $logs_json[$i]->LID;
        $uid = $logs_json[$i]->UID;
        $date = $logs_json[$i]->Date;
        if(isset($logs_json[$i]->SaID)) {
            $said = $logs_json[$i]->SaID;
        } else {
            $said = null;
        }
        $actiongroup = $logs_json[$i]->ActionGroup;
        $encode = $logs_json[$i]->Encode;
        if(isset($logs_json[$i]->TID)) {
            $tid = $logs_json[$i]->TID;
        } else {
            $tid = null;
        }
        if(isset($logs_json[$i]->QID)) {
            $qid = $logs_json[$i]->QID;
        } else {
            $qid = null;
        }
        if(isset($logs_json[$i]->Answer)) {
            $answer = $logs_json[$i]->Answer;
        } else {
            $answer = null;
        }
        if(isset($logs_json[$i]->Other)) {
            $other = $logs_json[$i]->Other;
        } else {
            $other = null;
        }

        // 新增此筆記錄到資料庫裡
        $log_utils->insert( array(  'LID' => $lid,
                                    'UID' => $uid,
                                    'Date' => $date,
                                    'SaID' => $said,
                                    'TID' => $tid,
                                    'ActionGroup' => $actiongroup,
                                    'Encode' => $encode,
                                    'QID' => $qid,
                                    'Answer' => $answer,
                                    'Other' => $other)

        );
    }

    $app->render(201,array(
        'error'      => false
    ));
});

// ============================================================================

/*
 * 取得館場資訊
 * GET http://localhost/api/v2/info
 */
$app->get('/info', 'APIrequest', function () use ($app) {

    $db = new Database\DBInfo();
    $placeInfoResult = $db->queryAllPlaceInfo();
    $placeMapResult = $db->queryALLPlaceMap();

    // 噴出結果
    $app->render(200,array(
        'place_info' => $placeInfoResult,
        'place_map'  => $placeMapResult,
        'error'      => false
    ));
});

// ============================================================================

// 取得Client要求的格式
$requestType = $app->request->headers->get('Accept');
// 若要求網頁版
if(strpos($requestType, 'text/html') !== false) {

    // API首頁
    $app->get('/', function () use ($app) {
        include('html/index.html');
    });

    // 沒有此功能
    $app->notFound(function () use ($app) {
        include('html/404.html');
    });
}
// 要求其他格式時，將以JSON為主
else {

    // API首頁
    $app->get('/', 'APIrequest', function () use ($app) {

        //取得現在時間，用字串的形式
        $nowDate = date("Y-m-d H:i:s");

        $app->render(200, array(
            'title'   => '',
            'version' => '2.0',
            'current_time' => $nowDate,
            'error'   => false,
        ));
    });

    // 沒有此功能
    $app->notFound(function () use ($app) {
        $app->view(new \JsonApiView());
        $app->add(new \JsonApiMiddleware());

        $app->render(404,array(
            'error'       => true,
            'msg'     => 'No this function.',
            'msg_cht' => '沒有此功能'
        ));
    });
}

// 內部出錯
$app->error(function (\Exception $e) use ($app) {
    //$app->render('error.php');
});


$app->run();
