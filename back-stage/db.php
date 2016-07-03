<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type:text/json;charset= UTF-8');
switch ($_GET['action']) {
    case "register":
        register();
        break;
    case "login":
        login();
        break;
    case "addGoods":
        addGoods();
        break;
    case "allGoods":
        allGoods();
        break;
    case "detailGoods":
        detailGoods();
        break;
    case "buyOrder":
        buyOrder();
        break;
    case "getAddress":
        getAddress();
        break;
    case "addAddress":
        addAddress();
        break;
    case "getOrder":
        getOrder();
        break;
    case "limitGoods":
        limitGoods();
        break;
    case "addImg";
        addImg();
        break;
    case "changeState":
        changeState();
        break;
    case "getAllGoods":
        getAllGoods();
        break;
    case "changeGoodsState":
        changeGoodsState();
        break;
}

function register()
{
    include('config.php');
    $data    = json_decode(file_get_contents("php://input"));
    $name    = $data->name;
    $pass    = $data->pass;
    $role    = $data->role;
    $qry     = 'INSERT INTO t_info (name,pass,role) values ("' . $name . '","' . $pass . '",' . $role . ')';
    $qry_res = mysqli_query($con, $qry);
    if ($qry_res) {
        echo $name;
    } else {
        $arr = array(
            'msg' => "",
            'error' => 'Error In Register'
        );
        $jsn = json_encode($arr);
        print_r($jsn);
    }
    //清空查询结果，释放内存空间，关闭数据库
    mysqli_close($con);
}
function login()
{
    class Goods
    {
        public $role;
        public $name;
    }
    
    include('config.php');
    $data    = json_decode(file_get_contents("php://input"));
    $user    = $data->user;
    $pass    = $data->pass;
    $qry     = "SELECT * FROM t_info WHERE name ='" . $user . "'" . " AND pass='" . $pass . "'";
    $qry_res = mysqli_query($con, $qry);
    //返回查询的数据数量
    $list    = mysqli_num_rows($qry_res);
    if ($list != 0) {
        while ($rows = mysqli_fetch_array($qry_res)) {
            $Goods       = new Goods();
            $Goods->role = $rows["role"];
            $Goods->name = $rows["name"];
            $array[]     = $Goods;
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    } else {
		$Goods       = new Goods();
        $Goods->error = "账户不存在或密码错误";
        $Goods->name = "";
        $array[]     = $Goods;
        //$username = array(
            //'error' => '账户不存在或密码错误'
        //);
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    mysqli_close($con);
}
function addGoods()
{
    include('config.php');
    $data        = json_decode(file_get_contents("php://input"));
    $brand       = $data->brand;
    $color       = $data->color;
    $size        = $data->size;
    $system      = $data->system;
    $ram         = $data->ram;
    $cpu         = $data->cpu;
    $type        = $data->type;
    $capacity    = $data->capacity;
    $price       = $data->price;
    $repertory   = $data->repertory;
    $description = $data->description;
    $path        = $data->path;
    $qry         = 'INSERT INTO t_goods (brand,color,size,system,ram,cpu,type,capacity,price,repertory,description,img,state)
              values ("' . $brand . '","' . $color . '","' . $size . '","' . $system . '","' . $ram . '","' . $cpu . '","' . $type . '","' . $capacity . '","' . $price . '","' . $repertory . '","' . $description . '","' . $path . '",0 );';
    $qry_res     = mysqli_query($con, $qry);
    //返回查询的数据数量
    if ($qry_res) {
        $username = array(
            'name' => "保存成功",
            'error' => ''
        );
        echo json_encode($username, JSON_UNESCAPED_UNICODE);
    } else {
        $username = array(
            'name' => "",
            'error' => '保存失败'
        );
        //结果返回json形式，并且将Unicode码转换为正常汉字
        echo json_encode($username, JSON_UNESCAPED_UNICODE);
    }
    mysqli_close($con);
}
function allGoods()
{
    class Goods
    {
        public $price;
        public $description;
        public $brand;
        public $repertory;
        public $size;
        public $cpu;
        public $id;
        public $img;
        public $state;
    }
    $data = json_decode(file_get_contents("php://input"));
    @$brand = $data->brand;
    @$sel_price = $data->sel_price;
    @$sel_size = $data->sel_size;
    
    //                     echo $sel_price;
    if ($sel_price == '0-2999') {
        $sel_left  = 0;
        $sel_right = 2999;
    } else if ($sel_price == "3000-5999") {
        $sel_left  = 3000;
        $sel_right = 5999;
    } else if ($sel_price == "6000以上") {
        $sel_left  = 6000;
        $sel_right = "";
    }
    //                     echo $sel_left." ".$sel_right;
    if ($brand == "" && $sel_price == "" && $sel_size == "") {
        $qry = "SELECT * FROM t_goods";
    }
    
    else if ($brand == "" && $sel_price != "" && $sel_size == "") {
        if ($sel_right == "") {
            $qry = "SELECT * FROM t_goods where price>'" . $sel_left . "'";
        } else {
            $qry = "SELECT * FROM t_goods where price>" . $sel_left . " and price <" . $sel_right;
        }
    }
    
    
    else if ($brand == "" && $sel_size != "" && $sel_price == "") {
        $qry = "SELECT * FROM t_goods where size='" . $sel_size . "'";
    }
    
    
    else if ($brand == "" && $sel_price != "" && $sel_size != "") {
        if ($sel_right == "") {
            $qry = "SELECT * FROM t_goods where price >" . $sel_left . " and size='" . $sel_size . "'";
        } else {
            $qry = "SELECT * FROM t_goods where price>" . $sel_left . " and price <" . $sel_right . " and size='" . $sel_size . "'";
        }
    }
    
    else if ($brand != "" && $sel_price == "" && $sel_size == "") {
        $qry = "SELECT * FROM t_goods where brand= '" . $brand . "'";
    }
    
    
    else if ($brand != "" && $sel_price != "" && $sel_size == "") {
        if ($sel_right == "") {
            $qry = "SELECT * FROM t_goods where brand='" . $brand . "' and price>" . $sel_left;
        } else {
            $qry = "SELECT * FROM t_goods where brand='" . $brand . "' and price>" . $sel_left . " and price <" . $sel_right;
        }
    }
    
    
    else if ($brand != "" && $sel_price == "" && $sel_size != "") {
        $qry = "SELECT * FROM t_goods where brand='" . $brand . "' and size='" . $sel_size . "'";
    }
    
    
    else if ($brand != "" && $sel_price != "" && $sel_size != "") {
        if ($sel_right == "") {
            $qry = "SELECT * FROM t_goods where brand='" . $brand . "' and  price>" . $sel_left . " and size='" . $sel_size . "'";
        } else {
            $qry = "SELECT * FROM t_goods where brand='" . $brand . "' and price>" . $sel_left . " and price <" . $sel_right . " and size='" . $sel_size . "'";
        }
        
    }
    include('config.php');
    $qry_res = mysqli_query($con, $qry);
    
    while ($rows = mysqli_fetch_array($qry_res)) {
        
        //可以直接把读取到的数据赋值给数组或者通过字段名的形式赋值也可以
        $Goods              = new Goods();
        $Goods->price       = $rows["price"];
        $Goods->description = $rows["description"];
        $Goods->brand       = $rows["brand"];
        $Goods->repertory   = $rows["repertory"];
        $Goods->size        = $rows["size"];
        $Goods->cpu         = $rows["cpu"];
        $Goods->id          = $rows["id"];
        $Goods->img         = $rows["img"];
        $Goods->state       = $rows["state"];
        $array[]            = $Goods;
    }
    echo json_encode($array, JSON_UNESCAPED_UNICODE);
    mysqli_close($con);
}
function detailGoods()
{
    class Goods
    {
        public $price;
        public $description;
        public $brand;
        public $color;
        public $system;
        public $ram;
        public $capacity;
        public $size;
        public $cpu;
    }
    
    include('config.php');
    $data    = json_decode(file_get_contents("php://input"));
    $id      = $data->id;
    $qry     = "SELECT * FROM t_goods where id ='" . $id . "';";
    $qry_res = mysqli_query($con, $qry);
    
    while ($rows = mysqli_fetch_array($qry_res)) {
        
        //可以直接把读取到的数据赋值给数组或者通过字段名的形式赋值也可以
        $Goods              = new Goods();
        $Goods->price       = $rows["price"];
        $Goods->description = $rows["description"];
        $Goods->brand       = $rows["brand"];
        $Goods->color       = $rows["color"];
        $Goods->size        = $rows["size"];
        $Goods->cpu         = $rows["cpu"];
        $Goods->system      = $rows["system"];
        $Goods->capacity    = $rows["capacity"];
        $Goods->type        = $rows["type"];
        $Goods->ram         = $rows["ram"];
        $array[]            = $Goods;
    }
    echo json_encode($array, JSON_UNESCAPED_UNICODE);
    mysqli_close($con);
}
function getAddress()
{
    include('config.php');
    class Goods
    {
        public $address;
        public $name;
        public $phone;
    }
    
    $data    = json_decode(file_get_contents("php://input"));
    $name    = $data->name;
    $qry     = "SELECT * FROM t_info WHERE name ='" . $name . "';";
    $qry_res = mysqli_query($con, $qry);
    
    while ($rows = mysqli_fetch_array($qry_res)) {
        //可以直接把读取到的数据赋值给数组或者通过字段名的形式赋值也可以
        $Goods          = new Goods();
        $Goods->address = $rows["address"];
        $Goods->name    = $rows["consignee"];
        $Goods->phone   = $rows["phone"];
        $array[]        = $Goods;
    }
    echo json_encode($array, JSON_UNESCAPED_UNICODE);
    mysqli_close($con);
}
function addAddress()
{
    include('config.php');
    $data      = json_decode(file_get_contents("php://input"));
    $name      = $data->name;
    $consignee = $data->consignee;
    $address   = $data->address;
    $phone     = $data->phone;
    $qry       = "UPDATE t_info set consignee='" . $consignee . "',address='" . $address . "', phone='" . $phone . "' where name ='" . $name . "'";
    
    $qry_res = mysqli_query($con, $qry);
    if ($qry_res) {
        $arr = array(
            'name' => $consignee,
            'address' => $address,
            'phone' => $phone
        );
        $jsn = json_encode($arr);
        echo $jsn;
    } else {
        $arr = array(
            'msg' => "",
            'error' => 'Error In Register'
        );
        $jsn = json_encode($arr);
        print_r($jsn);
    }
    mysqli_close($con);
}
function buyOrder()
{
    include('config.php');
    $data        = json_decode(file_get_contents("php://input"));
    $name        = $data->name;
    $consignee   = $data->consignee;
    $price       = $data->price;
    $description = $data->description;
    $num         = $data->num;
    $img         = $data->img;
    $state       = 0;
    $qry         = 'INSERT INTO t_order (account,buyer,description,price,quantity,state,img)
              values ("' . $name . '","' . $consignee . '","' . $description . '","' . $price . '","' . $num . '","' . $state . '","' . $img . '");';
    $qry_res     = mysqli_query($con, $qry);
    if ($qry_res) {
        $username = array(
            'name' => "保存成功",
            'error' => ''
        );
        echo json_encode($username, JSON_UNESCAPED_UNICODE);
    } else {
        $username = array(
            'name' => "",
            'error' => '保存失败'
        );
        //结果返回json形式，并且将Unicode码转换为正常汉字
        echo json_encode($username, JSON_UNESCAPED_UNICODE);
    }
    mysqli_close($con);
}
function getOrder()
{
    include('config.php');
    class Goods
    {
        public $price;
        public $account;
        public $quantity;
        public $description;
        public $img;
        public $state;
        public $id;
    }
    
    $data  = json_decode(file_get_contents("php://input"));
    $name  = $data->name;
    $role  = $data->role;
    $state = $data->state;
    if ($role == 0 && $state == 0) {
        $qry = "SELECT * FROM t_order WHERE account ='" . $name . "'";
    } else if ($role == 0 && $state == 1) {
        $qry = "SELECT * FROM t_order WHERE account ='" . $name . "' and state= '0'";
    } else if ($role == 0 && $state == 2) {
        $qry = "SELECT * FROM t_order WHERE account ='" . $name . "' and state= '1'";
    } else if ($role == 1 && $state == 0) {
        $qry = "SELECT * FROM t_order";
    } else if ($role == 1 && $state == 1) {
        $qry = "SELECT * FROM t_order WHERE state ='0'";
    } else {
        $qry = "SELECT * FROM t_order WHERE state ='1'";
    }
    
    $qry_res = mysqli_query($con, $qry);
    
    while ($rows = mysqli_fetch_array($qry_res)) {
        //可以直接把读取到的数据赋值给数组或者通过字段名的形式赋值也可以
        $Goods              = new Goods();
        $Goods->price       = $rows["price"];
        $Goods->account     = $rows["account"];
        $Goods->quantity    = $rows["quantity"];
        $Goods->description = $rows["description"];
        $Goods->img         = $rows["img"];
        $Goods->state       = $rows["state"];
        $Goods->id          = $rows["id"];
        $array[]            = $Goods;
    }
    echo json_encode($array, JSON_UNESCAPED_UNICODE);
    mysqli_close($con);
}

function limitGoods()
{
    class Goods
    {
        public $price;
        public $description;
        public $brand;
        public $repertory;
        public $size;
        public $cpu;
        public $id;
        public $img;
        public $state;
    }
    $data = json_decode(file_get_contents("php://input"));
    include('config.php');
    $qry     = "SELECT * FROM t_goods LIMIT 0,8";
    $qry_res = mysqli_query($con, $qry);
    
    while ($rows = mysqli_fetch_array($qry_res)) {
        
        //可以直接把读取到的数据赋值给数组或者通过字段名的形式赋值也可以
        $Goods              = new Goods();
        $Goods->price       = $rows["price"];
        $Goods->description = $rows["description"];
        $Goods->brand       = $rows["brand"];
        $Goods->repertory   = $rows["repertory"];
        $Goods->size        = $rows["size"];
        $Goods->cpu         = $rows["cpu"];
        $Goods->id          = $rows["id"];
        $Goods->img         = $rows["img"];
        $Goods->state       = $rows["state"];
        $array[]            = $Goods;
    }
    echo json_encode($array, JSON_UNESCAPED_UNICODE);
    mysqli_close($con);
}
function addImg()
{
    $allowedExts = array(
        "gif",
        "jpeg",
        "jpg",
        "png"
    );
    $temp        = explode(".", $_FILES["file"]["name"]);
    $extension   = end($temp);
    if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png")) && ($_FILES["file"]["size"] < 2000000) && in_array($extension, $allowedExts)) {
        if ($_FILES["file"]["error"] > 0) {
            echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
        } else {
            
            if (file_exists("./tem/" . $_FILES["file"]["name"])) {
                echo $_FILES["file"]["name"] . " already exists. ";
            } else {
                move_uploaded_file($_FILES["file"]["tmp_name"], "./update/" . $_FILES["file"]["name"]);
                
                $img = "http://localhost/back-stage/update/" . $_FILES["file"]["name"];
               
                echo $img;
            }
        }
    } else {
        echo "无效的文件";
    }
}
function changeState()
{
    include('config.php');
    $data  = json_decode(file_get_contents("php://input"));
    $id    = $data->id;
    $state = $data->state;
    
    $qry = "UPDATE t_order set state='" . $state . "' where id ='" . $id . "'";
    
    $qry_res = mysqli_query($con, $qry);
    if ($qry_res) {
        $username = array(
            'success' => "保存成功",
            'error' => ''
        );
        echo json_encode($username, JSON_UNESCAPED_UNICODE);
    } else {
        $username = array(
            'success' => "",
            'error' => '保存失败'
        );
        echo json_encode($username, JSON_UNESCAPED_UNICODE);
    }
    mysqli_close($con);
}
function getAllGoods()
{
    include('config.php');
    
    class Goods
    {
        public $price;
        public $description;
        public $brand;
        public $repertory;
        public $size;
        public $cpu;
        public $id;
        public $img;
        public $state;
    }
    $data  = json_decode(file_get_contents("php://input"));
    $state = $data->state;
    if ($state == 0) {
        $qry = "SELECT * FROM t_goods";
    } else if ($state == 1) {
        $qry = "SELECT * FROM t_goods where state= '0'";
    } else {
        $qry = "SELECT * FROM t_goods where state= '1'";
    }
    
    $qry_res = mysqli_query($con, $qry);
    while ($rows = mysqli_fetch_array($qry_res)) {
        
        //可以直接把读取到的数据赋值给数组或者通过字段名的形式赋值也可以
        $Goods              = new Goods();
        $Goods->price       = $rows["price"];
        $Goods->description = $rows["description"];
        $Goods->brand       = $rows["brand"];
        $Goods->repertory   = $rows["repertory"];
        $Goods->size        = $rows["size"];
        $Goods->cpu         = $rows["cpu"];
        $Goods->id          = $rows["id"];
        $Goods->img         = $rows["img"];
        $Goods->state       = $rows["state"];
        $array[]            = $Goods;
    }
    echo json_encode($array, JSON_UNESCAPED_UNICODE);
    mysqli_close($con);
}
function changeGoodsState()
{
    include('config.php');
    $data  = json_decode(file_get_contents("php://input"));
    $id    = $data->id;
    $state = $data->state;
    if ($state == 0) {
        $qry = "UPDATE t_goods set state='1' where id ='" . $id . "'";
    } else {
        $qry = "UPDATE t_goods set state='0' where id ='" . $id . "'";
    }
    
    
    $qry_res = mysqli_query($con, $qry);
    if ($qry_res) {
        $username = array(
            'success' => "保存成功",
            'error' => ''
        );
        echo json_encode($username, JSON_UNESCAPED_UNICODE);
    } else {
        $username = array(
            'success' => "",
            'error' => '保存失败'
        );
        //结果返回json形式，并且将Unicode码转换为正常汉字
        echo json_encode($username, JSON_UNESCAPED_UNICODE);
    }
    mysqli_close($con);
}
?>