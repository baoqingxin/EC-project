/**
 * Created by Boris on 2016/4/10.
 */
var myControllers = angular.module('myControllers', ['ui.router', 'ngFileUpload', 'ui.bootstrap']);
//var baseurl="http://192.168.191.5";
//var baseurl="http://10.16.213.8";
var baseurl = "http://localhost";
myControllers.controller('homeCtrl', ['$scope', '$http', '$rootScope', '$state',
    function ($scope, $http, $rootScope, $state) {
        $scope.myInterval = 2000;
        $scope.noWrapSlides = false;
        $scope.active = 0;
        var slides = $scope.slides = [];
        var currIndex = 0;
        $scope.addSlide = function () {
            slides.push({
                image: 'assets/images/banner_' + (slides.length % 5 + 1) + '.jpg',
                id: currIndex++
            });
        };
        for (var i = 0; i < 5; i++) {
            $scope.addSlide();
        }
        $http.get(baseurl + '/back-stage/db.php?action=limitGoods')
            .success(function (data) {
                $scope.goods = data;
            })
            .error(function (data) {
            })

        console.log(slides);
    }]).controller('purchaserCtrl', ['$scope', '$http', '$state', '$rootScope',
    function ($scope, $http, $state, $rootScope) {
        //设置页面左边的列表
        $scope.role = $rootScope.role;
        //$scope.role=0;
        if ($scope.role == 0) {
            $scope.purchasers = ["我的订单", "收货地址"];
            $scope.items = ["全部订单", "待收货", "已完成"];
        } else {
            $scope.purchasers = ["我的订单", "全部商品", "添加商品"];
            $scope.items = ["全部订单", "待发货", "已完成"];
            $scope.lists = ["全部商品", "待上架", "已上架"];
            //$scope.lists = ["全部商品"];
            $scope.bands = ["联想", "惠普", "戴尔"];
            $scope.systems = ["Windows7", "Windows8", "windows10"];

        }

        $scope.current = "我的订单";
        $state.go("purchaser.order", {role: $scope.role});
        $scope.setScreen = function (index) {
            $scope.current = $scope.purchasers[index];
            if ($scope.current == "我的订单") {
                $state.go("purchaser.order", {role: $scope.role});
            } else if ($scope.current == "添加商品") {
                $state.go("purchaser.add-goods");
            }
            else if ($scope.current == "收货地址") {
                $state.go("purchaser.address");
            } else {
                $state.go("purchaser.all-goods");
            }
        };
        $scope.sel_id = 0;
        //获取全部订单
        $http.post(baseurl + "/back-stage/db.php?action=getOrder", {
            'name': $rootScope.username,
            'role': $scope.role,
            'state': $scope.sel_id
        }).success(function (data) {
            $scope.orders = data;
            console.log(data);
        });

        //设置订单列表的点击样式
        $scope.active = [];
        $scope.active[0] = "active";

        $scope.setItem = function (index) {
            $scope.sel_id = index;
            $scope.active = [];
            $scope.active[index] = "active";
            $http.post(baseurl + "/back-stage/db.php?action=getOrder", {
                'name': $rootScope.username,
                'role': $scope.role,
                'state': $scope.sel_id
            }).success(function (data) {
                $scope.orders = data;
                console.log(data);
            })
        };
    }]).controller('registerCtrl', ['$scope', '$http', '$rootScope', '$state',
    function ($scope, $http, $rootScope, $state) {
        $scope.role = 0;

        //表单验证密码是否一致
        $scope.sure_pwd = true;
        $scope.checked = function () {
            if ($scope.register_pwd === $scope.register_pwd_once) {
                $scope.sure_pwd = false;
            } else {
                $scope.sure_pwd = true;
            }
        }
        $scope.register = function () {
            $http.post(baseurl + '/back-stage/db.php?action=register',
                {
                    'name': $scope.register_name,
                    'pass': $scope.register_pwd,
                    'role': $scope.role
                })
                .success(function (data) {
                    $rootScope.username = data;
                    $rootScope.role = 0;
                    $state.go("home");
                })
                .error(function (data, status, headers, config) {
                    console.log('error:' + data);
                });
            console.log($scope.register_name + " " + $scope.register_pwd + " " + $scope.role)
        }
    }]).controller('loginCtrl', ['$scope', '$http', '$rootScope', '$state',
    function ($scope, $http, $rootScope, $state) {
        $scope.login = function () {
            //console.log($scope.login.user + " " + $scope.login.pwd)
            $http.post(baseurl + '/back-stage/db.php?action=login',
                {
                    'user': $scope.login.user,
                    'pass': $scope.login.pwd
                })
                .success(function (data) {
                    if (data[0].name != "") {
                        $rootScope.username = data[0].name;
                        $rootScope.role = data[0].role;
                        $state.go("home");
                    } else {
                        $scope.login.err = data[0].error;
                    }
                })
        }
    }]).controller('addGoodsCtrl', ['$scope', '$http', '$rootScope', '$state', 'Upload', '$timeout',
    function ($scope, $http, $rootScope, $state, Upload, $timeout) {
        $scope.add_brand = "联想";
        $scope.add_system = "Windows7";

        $scope.path = "";
        $scope.addGoods = function () {
            $http.post(baseurl + '/back-stage/db.php?action=addGoods',
                {
                    'brand': $scope.add_brand,
                    'color': $scope.add_color,
                    'size': $scope.add_size,
                    'system': $scope.add_system,
                    'ram': $scope.add_ram,
                    'cpu': $scope.add_cpu,
                    'type': $scope.add_type,
                    'capacity': $scope.add_capacity,
                    'price': $scope.add_price,
                    'repertory': $scope.add_repertory,
                    'description': $scope.add_description,
                    'path': $scope.path
                })
                .success(function (data) {

                    console.log('success:' + data);
                    $state.go("purchaser.all-goods");

                })

        }
        $scope.uploadFiles = function (files, errFiles) {
            $scope.files = files;
            $scope.errFiles = errFiles;

            $scope.errorImg = null;
            angular.forEach(files, function (file) {
                file.upload = Upload.upload({
                    url: baseurl + '/back-stage/db.php?action=addImg',
                    data: {file: file}
                });

                file.upload.then(function (response) {
                    $timeout(function () {
                        file.result = response.data;
                        if (response.data != "无效的文件") {
                            $scope.path = response.data;
                        } else {
                            $scope.errorImg = "无效的文件，请重新上传"
                        }
                    });
                }, function (response) {
                    if (response.status > 0)
                        $scope.errorMsg = response.status + ': ' + response.data;
                }, function (evt) {
                    file.progress = Math.min(100, parseInt(100.0 *
                        evt.loaded / evt.total));
                });
            });
        }


    }]).controller('allGoodsCtrl', ['$scope', '$http', '$rootScope', '$state',
    function ($scope, $http, $rootScope, $state) {

        $scope.another_active = [];
        $scope.another_active[0] = "active";
        $scope.anotherSetItem = function (index) {
            $scope.index = index;
            $scope.another_active = [];
            $scope.another_active[index] = "active";
            $http.post(baseurl + "/back-stage/db.php?action=getAllGoods", {
                'state': index
            }).success(function (data) {
                $scope.goods = data;
                console.log($scope.goods);
            })
        };

        $http.get(baseurl + '/back-stage/db.php?action=allGoods')
            .success(function (data) {
                $scope.goods = data;
                //console.log('success:' + data);
            })
            .error(function (data) {
                //console.log('error:' + data);
            })

        $scope.changeGoodsState = function (id, state) {
            $http.post(baseurl + '/back-stage/db.php?action=changeGoodsState', {
                    'id': id,
                    'state': state
                })
                .success(function (data) {
                    $http.post(baseurl + "/back-stage/db.php?action=getAllGoods", {
                        'state': $scope.index
                    }).success(function (data) {
                        $scope.goods = data;
                        console.log($scope.goods);
                    })
                })

        }
    }]).controller('goodsShowCtrl', ['$scope', '$http', '$rootScope', '$state', '$stateParams',
    function ($scope, $http, $rootScope, $state, $stateParams) {
        $scope.sizes = ["11", '12', '13', '14', '15'];
        $scope.prices = ["0-2999", "3000-5999", "6000以上"];
        $scope.sel_size = "";
        $scope.sel_price = "";
        $scope.sizeSelect = function (index) {
            $scope.sel_size = $scope.sizes[index];

            $scope.top_blue = [];
            $scope.top_blue[index] = "blue";
            $http.post(baseurl + '/back-stage/db.php?action=allGoods', {
                    brand: $stateParams.brand,
                    sel_size: $scope.sel_size,
                    sel_price: $scope.sel_price
                })
                .success(function (data) {
                    $scope.goods = data;
                })
                .error(function (data) {
                })
        }
        $scope.priceSelect = function (index) {
            $scope.sel_price = $scope.prices[index];
            $scope.blue = [];
            $scope.blue[index] = "blue";
            $http.post(baseurl + '/back-stage/db.php?action=allGoods', {
                    brand: $stateParams.brand,
                    sel_size: $scope.sel_size,
                    sel_price: $scope.sel_price
                })
                .success(function (data) {
                    $scope.goods = data;
                })
                .error(function (data) {
                })
        }

        $http.post(baseurl + '/back-stage/db.php?action=allGoods', {
                brand: $stateParams.brand,
                sel_size: $scope.sel_size,
                sel_price: $scope.sel_price
            })
            .success(function (data) {
                $scope.goods = data;
            })
            .error(function (data) {
            })


    }]).controller('goodsDetailCtrl', ['$scope', '$http', '$rootScope', '$state', '$stateParams',
    function ($scope, $http, $rootScope, $state, $stateParams) {
        $scope.id = $stateParams.id;
        $scope.img = $stateParams.img;
        //console.log($scope.id);
        $http.post(baseurl + '/back-stage/db.php?action=detailGoods',
            {
                id: $stateParams.id
            })
            .success(function (data) {
                $scope.goods = data[0];
            })
            .error(function (data) {
            })
        $scope.num = 1;
        //商品购买数量
        $scope.numPlus = function () {
            $scope.num++;
        };
        $scope.numReduce = function () {
            $scope.num--;
            if ($scope.num < 1) {
                $scope.num = 1;
            }
        };
    }]).controller('shoppingCtrl', ['$scope', '$http', '$rootScope', '$state', '$stateParams',
    function ($scope, $http, $rootScope, $state, $stateParams) {
        $scope.id = $stateParams.id;
        $scope.img = $stateParams.img;
        $scope.description = $stateParams.description;
        $scope.price = parseInt($stateParams.price);
        $scope.color = $stateParams.color;
        $scope.num = parseInt($stateParams.num);
        $scope.total = $scope.price * $scope.num;
        $scope.consignee = "";
        $http.post(baseurl + '/back-stage/db.php?action=getAddress',
            {
                'name': $rootScope.username
            })
            .success(function (data) {
                if (data[0].address == null) {

                } else {
                    $scope.consignee_address = data[0].address;
                    $scope.consignee = data[0].name;
                    $scope.phone = data[0].phone;
                }
                console.log(data);
            })


        $scope.account = function () {
            if (!$rootScope.username) {
                alert("请登录")
            } else {
                $http.post(baseurl + '/back-stage/db.php?action=buyOrder',
                    {
                        num: $scope.num,
                        description: $scope.description,
                        name: $rootScope.username,
                        price: $scope.total,
                        consignee: $scope.consignee,
                        img: $scope.img
                    })
                    .success(function (data) {
                        console.log($scope.img)
                        console.log("成功");
                        $state.go("purchaser.order", {role: $rootScope.role});
                    })
                    .error(function (data) {
                    })
            }
        }
    }]).controller('addressCtrl', ['$scope', '$http', '$rootScope', '$state', '$stateParams',
    function ($scope, $http, $rootScope, $state, $stateParams) {
        //添加地址
        $scope.modal_show = false;
        $scope.addAddress = function () {
            $scope.modal_show = true;
        };
        $scope.modalClose = function () {
            $scope.modal_show = false;
            $http.post(baseurl + '/back-stage/db.php?action=getAddress',
                {
                    'name': $rootScope.username
                })
                .success(function (data) {
                    if (data[0].address == null) {

                    } else {
                        $scope.consignee_address = data[0].address;
                        $scope.consignee = data[0].name;
                        $scope.phone = data[0].phone;
                    }
                    console.log(data);
                })
        };
        $http.post(baseurl + '/back-stage/db.php?action=getAddress',
            {
                'name': $rootScope.username
            })
            .success(function (data) {
                if (data[0].address == null) {

                } else {
                    $scope.consignee_address = data[0].address;
                    $scope.consignee = data[0].name;
                    $scope.phone = data[0].phone;
                }
                console.log(data);
            })
        $scope.modalSave = function () {
            $http.post(baseurl + '/back-stage/db.php?action=addAddress',
                {
                    'name': $rootScope.username,
                    'consignee': $scope.consignee,
                    'address': $scope.consignee_address,
                    'phone': $scope.phone
                })
                .success(function (data) {
                    if (data.address == null) {

                    } else {
                        $scope.consignee_address = data.address;
                        $scope.consignee = data.name;
                        $scope.phone = data.phone;
                    }
                    $scope.modal_show = false;
                })
        }
    }]).controller('orderCtrl', ['$scope', '$http', '$rootScope', '$state', '$stateParams',
    function ($scope, $http, $rootScope, $state, $stateParams) {
        //添加地址
        if ($stateParams.role == 0) {
            $scope.role = 0;
        } else {
            $scope.role = 1;
        }
        $scope.changeState = function (index) {
            $http.post(baseurl + "/back-stage/db.php?action=changeState", {
                'id': index,
                'state': 1
            }).success(function (data) {
                $http.post(baseurl + "/back-stage/db.php?action=getOrder", {
                    'name': $rootScope.username,
                    'role': $stateParams.role
                }).success(function (data) {
                    $scope.orders = data;
                    console.log(data);
                })
            })
        }
    }])