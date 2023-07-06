<?php
define('IDA_PATH', BASEPATH . '/addons/ida');

Route::get('/ida/auth', function () {
    include BASEPATH . "/header.php";
    include IDA_PATH . "/pages/ida-login.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::post('/ida/auth', function () {
    require_once IDA_PATH . "/php/IDA.php";
    // Borsigstr3!?
    $IDA = new IDA($_POST['email'], $_POST['password']);
    if (!$IDA->is_authorized()) {
        include BASEPATH . "/header.php";
        printMsg($IDA->msg, 'error');
        include IDA_PATH . "/pages/ida-login.php";
        include BASEPATH . "/footer.php";
        die;
    }
    redirect("/ida/dashboard");
}, 'login');



// Route::get('/ida/(fields)', function ($fun) {
//     require_once IDA_PATH . "/php/IDA.php";

//     $IDA = new IDA();

//     $result = $IDA->$fun();
//     dump($result, true);

//     dump($_SESSION['ida-token']);
// }, 'login');



Route::get('/ida/dashboard', function () {
    require_once IDA_PATH . "/php/IDA.php";

    // init IDA and check authorization status
    $IDA = new IDA();
    if ($IDA->is_authorized()) {
        $dashboard = $IDA->dashboard();
    }
    if (!$IDA->is_authorized()) {
        include BASEPATH . "/header.php";
        printMsg($IDA->msg, 'error');
        include IDA_PATH . "/pages/ida-login.php";
        include BASEPATH . "/footer.php";
        die;
    }

    include BASEPATH . "/header.php";
    include IDA_PATH . "/pages/ida-dashboard.php";

    dump($dashboard, true);

    // $updateResult = $osiris->activities->updateMany(
    //     [
    //         'authors.aoi' => ['$in' => ['1', 1, "true"]]
    //     ],
    //     ['$set' => ["authors.$.aoi" => true]]
    // );
    // echo $updateResult->getModifiedCount();
    // $updateResult = $osiris->activities->updateMany(
    //     [
    //         'authors.aoi' => ['$in' => ['0', 0, "false", ""]]
    //     ],
    //     ['$set' => ["authors.$.aoi" => false]]
    // );
    // echo "<br>";
    // echo $updateResult->getModifiedCount();

    include BASEPATH . "/footer.php";
}, 'login');



Route::post('/ida/update-institute', function () {
    if (!isset($_POST['institute'])) die ('No institute selected');
    $_SESSION['ida-institute_id'] = $_POST['institute'];
    redirect('/ida/dashboard');
});


Route::get('/ida/formular/(\d+)', function ($formular_id) {
    require_once IDA_PATH . "/php/IDA.php";

    // init IDA and check authorization status
    $IDA = new IDA();
    if (!$IDA->is_authorized()) {
        include BASEPATH . "/header.php";
        printMsg($IDA->msg, 'error');
        include IDA_PATH . "/pages/ida-login.php";
        include BASEPATH . "/footer.php";
        die;
    }

    $formular = $IDA->formular($formular_id);
    include BASEPATH . "/header.php";

    if (!empty($IDA->msg)){
        printMsg($IDA->msg, 'error');
    }
    if (!empty($formular)){
        include IDA_PATH . "/pages/ida-formular.php";
    }

    dump($formular, true);

    include BASEPATH . "/footer.php";
}, 'login');
