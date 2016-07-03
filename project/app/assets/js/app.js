/**
 * Created by Boris on 2016/4/10.
 */
var myApp = angular.module('myApp', ['ui.router','ngFileUpload', 'ui.bootstrap','myControllers']);
myApp.config(function ($stateProvider, $urlRouterProvider) {

    $urlRouterProvider.otherwise('/home');

    $stateProvider
        .state('home', {
            url: "/home",
            templateUrl: "tpl/home.html",
            controller:"homeCtrl"
        })
        .state('goods-show', {
            url: "/goods-show/:brand",
            templateUrl: "tpl/goods-show.html",
            controller: 'goodsShowCtrl'
        })
        .state('goods-detail', {
            url: "/goods-detail/:id/:img",
            templateUrl: "tpl/goods-detail.html",
            controller: 'goodsDetailCtrl'
        })
        .state('login', {
            url: "/login",
            templateUrl: "tpl/login.html",
            controller:'loginCtrl'
        })
        .state('register', {
            url: "/register",
            templateUrl: "tpl/register.html",
            controller: 'registerCtrl'
        })
        .state('purchaser', {
            url: "/purchaser",
            templateUrl: "tpl/purchaser.html",
            controller: 'purchaserCtrl'
        })
        .state('shopping', {
            url: "/shopping/:id/:description/:price/:color/:num/:img",
            templateUrl: "tpl/shopping-car.html",
            controller:"shoppingCtrl"
        })
        .state('purchaser.add-goods',{
            url:"/add-goods",
            templateUrl:"tpl/post-trade.html",
            controller: 'addGoodsCtrl'
        })
        .state('purchaser.order',{
            url:"/order/:role",
            templateUrl:"tpl/order.html",
            controller: 'orderCtrl'
        })
        .state('purchaser.address',{
            url:"/address",
            templateUrl:"tpl/address.html",
            controller: 'addressCtrl'
        })
        .state('purchaser.all-goods',{
            url:"/all-goods",
            templateUrl:"tpl/all-goods.html",
            controller: 'allGoodsCtrl'
        })
});