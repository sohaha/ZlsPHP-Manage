<style>
.page-user-menu .tree-placeholder {
    display: inline-block;
    width: 20px;
    line-height: 20px;
    height: 20px;
    text-align: center;
    margin-right: 3px;
}

.custom-tree-node {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.out-box {
    width: 100%;
    height: auto;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.left-box,
.right-box {
    width: 50%;
}

.right-box {
    padding: 20px 20px 0 30px;
    box-sizing: border-box;
    flex: 1;
}

.block {
    padding: 20px 20px 0 30px;
    text-align: center;
    /* border-right: 1px solid #eff2f6; */
    flex: 1;
}

.btn-box {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 10px;
}

.labelName {
    margin: 10px auto;
    display: block;
}

.el-input--small .el-input__inner {
    width: 300px;
}

.el-form-item__label {
    width: 138px !important;
}

.my-input {
    width: 56%;
    min-width: 300px;
}

.rightForm {
    padding: 30px 0 30px 0;
}

.el-form-item__content {
    margin-left: 0px !important;
}

.itemBox {
    display: flex;
    justify-content: center;
    align-items: center;
}

.el-form-item {
    display: flex;
    justify-content: center;
    align-items: center;
}

.treeBox {
    margin: 30px 0;
}

.btn-top {
    display: flex;
    justify-content: flex-end;
}

.tip-area {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

fieldset {
    min-width: 450px;
}

.el-select-dropdown__item {
    display: flex;
    justify-content: space-between;
}
</style>
<template>
<div class="page-user-menu">
    <div class="view-title float-clear">
        <div class="view-title-right">
            <el-form @submit.prevent.stop.native inline class="tip-top">
                <!--          <el-form-item>-->
                <!--            <el-button type="success" size="small" icon="el-icon-plus" @click="addMenu">增加菜单</el-button>-->
                <!--          </el-form-item>-->
            </el-form>
        </div>
    </div>
    <div class="tip-area">
        <div>
            温馨提示: 角色个性化菜单设置请前往 [
            <a v-link="'main/user/rules'">权限设置</a> ]
        </div>
        <div class="btn-top">
            <el-button type="primary" size="mini" @click="addMenu" icon="el-icon-plus">增加菜单</el-button>
        </div>
    </div>

    <fieldset>
        <legend v-text="title"></legend>
        <div class="out-box">
            <!--  左边内容  -->
            <div class="block left-box">
                <el-alert title="菜单目录" type="success" center :closable="false"></el-alert>
                <aside class="center" :aria-label="title">
                    <div class="treeBox">
                        <el-tree :data="treeData" :props="defaultProps" node-key="id" default-expand-all @node-drag-start="handleDragStart" @node-drag-enter="handleDragEnter" @node-drag-leave="handleDragLeave" @node-drag-over="handleDragOver" @node-drag-end="handleDragEnd" @node-drop="handleDrop" draggable :allow-drop="allowDrop" :allow-drag="allowDrag">
                            <div class="custom-tree-node" slot-scope="{ node, data }">
                                <div>
                                    <span :class="data.icon"></span>
                                    <span>
                                        {{ data.title }}------
                                        <span style="color: #409eff">{{data.index}}</span>
                                    </span>
                                </div>
                                <span>
                                    <template v-if="data.child !== undefined">
                                        <el-button type="text" size="mini" @click.stop="append(data)">增加</el-button>
                                    </template>
                                    <template v-if="data.title !== '首页'">
                                        <el-button type="text" size="mini" @click.stop="edit(node, data)">编辑</el-button>
                                        <el-button type="text" size="mini" @click.stop="remove(node, data)">删除</el-button>
                                    </template>
                                </span>
                            </div>
                        </el-tree>
                    </div>
                </aside>
            </div>

            <!--  右边内容  -->
            <div class="right-box">
                <el-alert title="添加（编辑）菜单" type="warning" center :closable="false"></el-alert>
                <el-form label-width="100px" class="rightForm">
                    <el-form-item label="菜单名称">
                        <el-input class="my-input" v-model="addMenuData.title" placeholder="请输入内容" size="small"></el-input>
                    </el-form-item>

                    <el-form-item label="菜单路径">
                        <el-input class="my-input" v-model="addMenuData.menuPath" placeholder="请输入内容" size="small"></el-input>
                    </el-form-item>
                    <el-form-item label="icon值">
                        <!-- <el-input class="my-input" v-model="addMenuData.iconVal" placeholder="请输入内容" size="small"></el-input> -->
                        <el-select v-model="addMenuData.iconVal" placeholder="请选择" size="small" clearable>
                            <el-option v-for="item in iconList" :key="item.font_class" :label="item.font_class" :value="item.font_class">
                                <span style="float: left">{{ item.font_class }}</span>
                                <span class="icon font_family" :class="['icon-'+item.font_class]"></span>
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="面包屑显示">
                        <components_el-select ref="breadShowSelect"></components_el-select>
                    </el-form-item>
                    <el-form-item label="面包屑可点击">
                        <components_el-select ref="breadClickSelect"></components_el-select>
                    </el-form-item>
                    <el-form-item label="导航栏显示">
                        <components_el-select ref="navigationSelect"></components_el-select>
                    </el-form-item>
                    <el-form-item label="上级菜单(默认顶级)">
                        <components_el-select ref="selectMenu"></components_el-select>
                        <!--          <el-cascader-->
                        <!--            :options="treeData"-->
                        <!--            v-model="addMenuData.menuVal"-->
                        <!--            :props="{ checkStrictly: true,label:'title',value:'id' }"-->
                        <!--            clearable></el-cascader>-->
                        <!--        </el-form-item>-->

                        <!--        <el-form-item label="上级菜单">-->
                        <!--          <components_el-select></components_el-select>-->
                    </el-form-item>

                    <div class="btn-box">
                        <el-button type="primary" @click="confirmBtn" size="mini" icon="el-icon-check">{{addMenuData.addAndEditTips}}</el-button>
                    </div>
                </el-form>
            </div>
        </div>
    </fieldset>

    <!--    增加菜单-->
    <!--    <el-dialog-->
    <!--      title="添加菜单"-->
    <!--      :close-on-click-modal	='false'-->
    <!--      :visible.sync="addMenuData.dialogVisible"-->
    <!--      width="30%"-->
    <!--      :before-close="cancelW">-->
    <!--      <el-form label-width="100px">-->
    <!--        <el-form-item label="菜单名称">-->
    <!--          <el-input v-model="addMenuData.title" placeholder="请输入内容" size="small"></el-input>-->
    <!--        </el-form-item>-->
    <!--        <el-form-item label="菜单路径">-->
    <!--          <el-input v-model="addMenuData.menuPath" placeholder="请输入内容" size="small"></el-input>-->
    <!--        </el-form-item>-->
    <!--        <el-form-item label="icon值">-->
    <!--          <el-input v-model="addMenuData.iconVal" placeholder="请输入内容" size="small"></el-input>-->
    <!--        </el-form-item>-->
    <!--        <el-form-item label="面包屑显示">-->
    <!--          <components_el-select ref="breadShowSelect"></components_el-select>-->
    <!--        </el-form-item>-->
    <!--        <el-form-item label="面包屑可点击">-->
    <!--          <components_el-select ref="breadClickSelect"></components_el-select>-->
    <!--        </el-form-item>-->
    <!--        <el-form-item label="导航栏显示">-->
    <!--          <components_el-select ref="navigationSelect"></components_el-select>-->
    <!--        </el-form-item>-->
    <!--        <el-form-item label="上级菜单(默认顶级)" v-if="addMenuData.isMenuShow">-->
    <!--          <components_el-select ref="selectMenu"></components_el-select>-->

    <!--&lt;!&ndash;          <el-cascader&ndash;&gt;-->
    <!--&lt;!&ndash;            :options="treeData"&ndash;&gt;-->
    <!--&lt;!&ndash;            v-model="addMenuData.menuVal"&ndash;&gt;-->
    <!--&lt;!&ndash;            :props="{ checkStrictly: true,label:'title',value:'id' }"&ndash;&gt;-->
    <!--&lt;!&ndash;            clearable></el-cascader>&ndash;&gt;-->
    <!--&lt;!&ndash;        </el-form-item>&ndash;&gt;-->

    <!--&lt;!&ndash;        <el-form-item label="上级菜单">&ndash;&gt;-->
    <!--&lt;!&ndash;          <components_el-select></components_el-select>&ndash;&gt;-->
    <!--        </el-form-item>-->

    <!--      </el-form>-->
    <!--      <span slot="footer" class="dialog-footer">-->
    <!--    <el-button @click="cancelW" size="small">取 消</el-button>-->
    <!--    <el-button type="primary" @click="confirmBtn" size="small">确 定</el-button>-->
    <!--  </span>-->
    <!--    </el-dialog>-->
</div>
</template>

<script>
Spa.define({
        mixins: [mixinLists, initTitle],
        title: "菜单管理(开发中)",
        data: function () {
            return {
                iconList: [],
                value: "",

                //树形数据
                treeData: [],
                //新的数据
                newList: [],
                // treeData: [
                //   {
                //     id: 1,
                //     pid: 0,
                //     title: "后台中心",
                //     index: "main",
                //     icon: "icon-home",
                //     breadcrumb: true, // 面包屑显示
                //     real: false, // 面包屑可点击
                //     show: false, // 导航栏显示
                //     child: []
                //   },
                //   {
                //     id: 2,
                //     pid: 0,
                //     title: "统计",
                //     index: "statistics",
                //     icon: "icon-pie-chart-",
                //     breadcrumb: true,
                //     real: false,
                //     show: false,
                //     child: [
                //       {
                //         id: 8,
                //         pid: 2,
                //         title: "商城概况",
                //         index: "statistics/shopInfo",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       }
                //     ]
                //   },
                //   {
                //     id: 3,
                //     pid: 0,
                //     title: "商城",
                //     index: "market",
                //     icon: "icon-shopping-cart-outlin",
                //     breadcrumb: true,
                //     real: false,
                //     show: false,
                //     child: [
                //       {
                //         id: 9,
                //         pid: 3,
                //         title: "首页设置",
                //         index: "market/homeSet",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       },
                //       {
                //         id: 10,
                //         pid: 3,
                //         title: "功能设置",
                //         index: "market/marketSet",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       }
                //     ]
                //   },
                //   {
                //     id: 4,
                //     pid: 0,
                //     title: "产品",
                //     index: "products",
                //     icon: "icon-grid-outline",
                //     breadcrumb: true,
                //     real: false,
                //     show: false,
                //     child: [
                //       {
                //         id: 11,
                //         pid: 4,
                //         title: "产品属性",
                //         index: "products/productAttribute",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       },
                //       {
                //         id: 12,
                //         pid: 4,
                //         title: "产品分类",
                //         index: "products/productCategory",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       },
                //       {
                //         id: 13,
                //         pid: 4,
                //         title: "管理产品",
                //         index: "products/productAdmin",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       }
                //     ]
                //   },
                //   {
                //     id: 5,
                //     pid: 0,
                //     title: "订单",
                //     index: "orders",
                //     icon: "icon-calendar-outline",
                //     breadcrumb: true,
                //     real: false,
                //     show: false,
                //     child: [
                //       {
                //         id: 14,
                //         pid: 5,
                //         title: "管理订单",
                //         index: "orders/ordersAdmin",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       }
                //     ]
                //   },
                //   {
                //     id: 6,
                //     pid: 0,
                //     title: "会员",
                //     index: "member",
                //     icon: "icon-people-outline",
                //     breadcrumb: true,
                //     real: false,
                //     show: false,
                //     child: [
                //       {
                //         id: 15,
                //         pid: 6,
                //         title: "管理会员",
                //         index: "member/memberAdmin",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       },
                //       {
                //         id: 16,
                //         pid: 6,
                //         title: "会员积分",
                //         index: "member/memberIntegral",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       }
                //     ]
                //   },
                //   {
                //     id: 7,
                //     pid: 0,
                //     title: "营销",
                //     index: "business",
                //     icon: "icon-gift-outline",
                //     breadcrumb: true,
                //     real: false,
                //     show: false,
                //     child: [
                //       {
                //         id: 17,
                //         pid: 7,
                //         title: "优惠卷规则",
                //         index: "business/couponRule",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       },
                //       {
                //         id: 18,
                //         pid: 7,
                //         title: "优惠卷",
                //         index: "business/coupon",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       },
                //       {
                //         id: 19,
                //         pid: 7,
                //         title: "秒杀",
                //         index: "business/activity",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       },
                //       {
                //         id: 20,
                //         pid: 7,
                //         title: "积分兑换",
                //         index: "business/integral",
                //         icon: "",
                //         breadcrumb: true,
                //         real: false,
                //         show: false
                //       }
                //     ]
                //   }
                // ],
                //配置
                defaultProps: {
                    children: "child",
                    label: "title"
                },

                //增加菜单的数据
                addMenuData: {
                    // dialogVisible:false,
                    title: "",
                    iconVal: "",
                    // isFirstOpen:true,
                    isEdit: "add",
                    selectId: "",
                    editData: "",
                    deleteKeepData: [],
                    // isMenuShow:true,
                    // menuVal:'',
                    menuPath: "",
                    insertParData: "",
                    addAndEditTips: "增 加"
                }
            };
        },
        mounted: function () {},
        computed: {},
        init: function (query, search) {},
        methods: {
            // getLists: function () {
            //   var data = { page: this.ml_page, pagesize: this.ml_pagesize };
            //   if (this.ml_searchKey) {
            //     data['key'] = this.ml_searchKey;
            //   }
            //   $this.ml_listsLoading = true;
            //   this.$api(apis.sysMenu, data)
            //   .then(function (v) {
            //     console.log(v);
            //     // todo test 接口地址undefined是不会真实发起请求所以这里 v 是不会有数据
            //     v = {
            //       data: {
            //         items: $this.$store.getters.menus,
            //         page: { total: 40 }
            //       }
            //     };
            //     // test end
            //     var data = v.data.items || [];
            //     data.forEach(function (e, index, values) {
            //       values[index]._isEdit = false;
            //       values[index]._isPopover = false;
            //       return e;
            //     });
            //     var page = v.data.page;
            //     $this.ml_getLists(data, page);
            //   })
            //   .catch(function (e) {
            //     $this.$warMsg(e);
            //   })
            //   .finally(function () {
            //     $this.ml_listsLoading = false;
            //   });
            // }

            /**
             * 添加菜单事件
             */
            addMenu: function () {
                // $this.addMenuData.dialogVisible = true;
                $this.clearData();
                $this.addMenuData.isEdit = "add";
                $this.$refs.selectMenu.isDisabled = false;
                $this.addMenuData.addAndEditTips = "增 加";
                // if($this.addMenuData.isFirstOpen){
                //   $this.addMenuData.isFirstOpen = false;
                //   $this.setData();
                // }

                //目录结构
                // $this.$nextTick(function () {
                //   var newTreeData = [];
                //   var treeData = $this.treeData;
                //   for(var index in treeData){
                //     var obj = {value:treeData[index].id,label:treeData[index].title};
                //     newTreeData.push(obj);
                //   }
                //   $this.$refs.selectMenu.options = newTreeData;
                // });
            },

            /**
             * 插入
             * @param data
             */
            append: function (data) {
                $this.clearData();
                $this.addMenuData.addAndEditTips = "增 加";
                $this.$refs.selectMenu.isDisabled = true;
                $this.addMenuData.isEdit = "insert";
                // $this.addMenuData.dialogVisible = true;

                // if($this.addMenuData.isFirstOpen){
                //   $this.addMenuData.isFirstOpen = false;
                //   $this.setData();
                // }
                $this.addMenuData.insertParData = data;
            },

            /**
             * 编辑按钮
             * @param node 节点
             * @param data 数据
             */
            edit: function (node, data) {
                console.log(node, data);
                $this.addMenuData.addAndEditTips = "编 辑";
                $this.addMenuData.isEdit = "edit";
                // $this.addMenuData.dialogVisible = true;
                // if($this.addMenuData.isFirstOpen){
                //   $this.addMenuData.isFirstOpen = false;
                //   $this.setData();
                // }
                var title = data.title;
                var iconVal = data.icon.substring(5, data.icon.length);
                var breadcrumb = data.breadcrumb;
                var real = data.real;
                var show = data.show;
                var path = data.index;

                $this.addMenuData.title = title;
                $this.addMenuData.iconVal = iconVal;
                $this.addMenuData.selectId = data.id;
                $this.addMenuData.menuPath = path;
                $this.$nextTick(function () {
                    $this.$refs.breadShowSelect.value = breadcrumb;
                    $this.$refs.breadClickSelect.value = real;
                    $this.$refs.navigationSelect.value = show;
                    $this.$refs.selectMenu.isDisabled = true;
                });
            },

            /**
             * @description: 新增菜单
             * @param {string} title 标题
             * @param {string} index 路由地址
             * @param {string} icon 路由地址
             * @param {int} breadcrumb 面包屑显示
             * @param {int} real 面包屑可点击
             * @param {int} show 导航栏显示
             * @param {int} pid 父级id
             * @return:
             */
            addMenuResult: function (title, index, icon, breadcrumb, real, show, pid) {
                $this
                    .$api(apis.sysMenuCreate, {
                        title: title,
                        index: index,
                        icon: icon,
                        breadcrumb: breadcrumb,
                        real: real,
                        show: show,
                        pid: pid
                    })
                    .then(function (data) {
                        $this.$sucMsg("新增菜单成功");
                    })
                    .catch(function (e) {
                        $this.$warMsg(e);
                    })
                    .finally(function () {
                        $this.getMenuList();
                    });
            },

            /**
             * @description: 编辑菜单
             * @param {string} id 编辑id
             * @param {string} title 标题
             * @param {string} index 路由地址
             * @param {string} icon 路由地址
             * @param {int} breadcrumb 面包屑显示
             * @param {int} real 面包屑可点击
             * @param {int} show 导航栏显示
             * @return:
             */
            editMenu: function (id, title, index, icon, breadcrumb, real, show) {
                $this
                    .$api(apis.sysMenuUpdate, {
                        id: id,
                        title: title,
                        index: index,
                        icon: icon,
                        breadcrumb: breadcrumb,
                        real: real,
                        show: show
                    })
                    .then(function (data) {
                        $this.$sucMsg("更新完成");
                    })
                    .catch(function (e) {
                        $this.$warMsg(e);
                    })
                    .finally(function () {
                        $this.getMenuList();
                    });
            },

            /**
             * 弹出框确认按钮
             */
            confirmBtn: function () {
                var isEdit = $this.addMenuData.isEdit;
                var title = $this.addMenuData.title;
                var menuPath = $this.addMenuData.menuPath;
                var iconVal = "icon-" + $this.addMenuData.iconVal;
                var breadcrumb = $this.$refs.breadShowSelect.value;
                var real = $this.$refs.breadClickSelect.value;
                var show = $this.$refs.navigationSelect.value;
                if (title.trim() === "") {
                    $this.$warMsg("请输入菜单名称");
                    return;
                } else if (menuPath === "") {
                    $this.$warMsg("请输入菜单路径");
                    return;
                } else if (breadcrumb === "") {
                    $this.$warMsg("请选择面包屑是否显示");
                    return;
                } else if (real === "") {
                    $this.$warMsg("请选择面包屑可否点击");
                    return;
                } else if (show === "") {
                    $this.$warMsg("请选择导航栏是否显示");
                    return;
                }

                if (isEdit === "edit") {
                    //编辑
                    var editId = $this.addMenuData.selectId;
                    $this.editMenu(
                        editId,
                        title,
                        menuPath,
                        iconVal,
                        breadcrumb,
                        real,
                        show
                    );
                } else if (isEdit === "add") {
                    var selectMenu = $this.$refs.selectMenu.value;
                    if (selectMenu.length === 0) {
                        $this.addMenuResult(
                            title,
                            menuPath,
                            iconVal,
                            breadcrumb,
                            real,
                            show,
                            0
                        );
                    } else {
                        //添加到次级目录
                        var selectData = $this.comebackItem($this.treeData, selectMenu);
                        var newPath = selectData.index + "/" + menuPath;
                        $this.addMenuResult(
                            title,
                            newPath,
                            iconVal,
                            breadcrumb,
                            real,
                            show,
                            selectMenu
                        );
                    }
                } else {
                    var insertParData = $this.addMenuData.insertParData;
                    //插入目录
                    var selectMenu = insertParData.id;
                    var newPath = insertParData.index + "/" + menuPath;
                    $this.addMenuResult(
                        title,
                        newPath,
                        iconVal,
                        breadcrumb,
                        real,
                        show,
                        selectMenu
                    );
                }
                $this.clearData();
            },

            /**
             * 根据id返回对应选项数据
             * @param data 数据
             * @param id 选项id
             */
            comebackItem: function (data, id) {
                for (var i in data) {
                    if (data[i].id === id) {
                        return data[i];
                    }
                    var isChildren = data[i].children;
                    if (isChildren) {
                        var come = $this.comebackItem(isChildren, id);
                        if (come !== undefined) {
                            return come;
                        }
                    }
                }
            },

            /**
             * 编辑操作
             * @param data 数据
             * @param id 修改的数据id
             */
            findItem: function (data, id) {
                var length = data.length;
                for (var i = 0; i < length; i++) {
                    if (data[i].id === id) {
                        data[i].title = $this.addMenuData.title;
                        data[i].icon = "icon-" + $this.addMenuData.iconVal;
                        data[i].index = $this.addMenuData.menuPath;
                        data[i].breadcrumb = $this.$refs.breadShowSelect.value;
                        data[i].real = $this.$refs.breadClickSelect.value;
                        data[i].show = $this.$refs.navigationSelect.value;
                        $this.$sucMsg("编辑完成");
                        // $this.addMenuData.dialogVisible = false;
                        $this.clearData();
                    } else {
                        var child = data[i].child;
                        if (child) {
                            $this.findItem(child, id);
                        }
                    }
                }
            },

            /**
             * 关闭窗口
             */
            cancelW: function () {
                $this.clearData();
                // $this.addMenuData.dialogVisible = false;
            },

            /**
             * 清除填写的数据
             */
            clearData: function () {
                var isEdit = $this.addMenuData.isEdit;
                $this.addMenuData.title = $this.addMenuData.iconVal = $this.addMenuData.menuPath = $this.$refs.breadShowSelect.value = $this.$refs.breadClickSelect.value = $this.$refs.navigationSelect.value =
                    "";
                if (isEdit !== "edit" && isEdit !== "insert") {
                    $this.$refs.selectMenu.value = "";
                } else {
                    $this.addMenuData.selectId = $this.addMenuData.insertParData = "";
                }
            },

            /**
             * 初始化选项框数据
             */
            setData: function () {
                $this.$nextTick(function () {
                    //带有启用和禁用的选项
                    $this.$refs.breadShowSelect.options = $this.$refs.breadClickSelect.options = $this.$refs.navigationSelect.options = [{
                            value: 1,
                            label: "启用"
                        },
                        {
                            value: 0,
                            label: "禁用"
                        }
                    ];
                });
            },

            /**
             * 删除按钮
             * @param node
             * @param data
             */
            remove: function (node, data) {
                console.log(node, data);
                var str = '是否删除 "' + data.title + '" 菜单';
                this.$confirm(str, "提示", {
                        confirmButtonText: "确定",
                        cancelButtonText: "取消",
                        type: "warning"
                    })
                    .then(function () {
                        // var parent = node.parent;
                        // var children = parent.data.child || parent.data;
                        // var curIndex;
                        // for (var index in children) {
                        //   if (children[index].id === data.id) {
                        //     curIndex = index;
                        //   }
                        // }
                        // children.splice(curIndex, 1);
                        $this.rundeleteMenu(data.id);
                    })
                    .catch(function () {});
            },

            /**
             * 删除菜单接口
             */
            rundeleteMenu: function (id) {
                $this
                    .$api(apis.sysMenuDelete, {
                        id: id
                    })
                    .then(function (data) {
                        $this.$sucMsg("删除菜单成功");
                    })
                    .catch(function (e) {
                        $this.$warMsg(e);
                    })
                    .finally(function () {
                        $this.getMenuList();
                    });
            },

            // /**
            //  * 获取删除节点的路径
            //  * @param data
            //  * @param id
            //  */
            // deleteItem:function(data,id){
            //   if (data) {
            //     var length = data.length;
            //     for (var i = 0; i < length; i++) {
            //       if (data[i].id === id) {
            //         if (data[i].pid) {
            //           $this.deleteItem($this.treeData, data[i].pid);
            //         }
            //         $this.addMenuData.deleteKeepData.push(i);
            //      } else {
            //         if (data[i].children) {
            //           $this.deleteItem(data[i].children,id);
            //         }
            //       }
            //     }
            //   }
            // },

            //以下拖拽事件
            handleDragStart: function (node, ev) {
                // console.log('drag start', node);
            },
            handleDragEnter: function (draggingNode, dropNode, ev) {
                // console.log('tree drag enter: ', dropNode.title);
            },
            handleDragLeave: function (draggingNode, dropNode, ev) {
                // console.log('tree drag leave: ', dropNode.title);
            },
            handleDragOver: function (draggingNode, dropNode, ev) {
                // console.log('tree drag over: ', dropNode.title);
            },
            handleDragEnd: function (draggingNode, dropNode, dropType, ev) {
                // console.log('tree drag end: ', dropNode && dropNode.title, dropType);
                if (dropType === "inner") {
                    //获取到自己的路径
                    var str = draggingNode.data.index;
                    var index = str.lastIndexOf("/");
                    str = str.substring(index + 1, str.length);
                    //新的路径
                    var newPath = dropNode.data.index + "/" + str;
                    draggingNode.data.index = newPath;
                    $this.newList = [];
                    $this.setMenuSort($this.treeData, false);
                    $this.sortMenu(JSON.stringify($this.newList));
                } else if (dropType === "before" || dropType === "after") {
                    //获取到拖拽到位置的路径
                    var str = dropNode.data.index;
                    var index = str.lastIndexOf("/");
                    str = str.substring(0, index + 1);
                    //获取到自己的路径
                    var myStr = draggingNode.data.index;
                    var myIndex = myStr.lastIndexOf("/");
                    myStr = myStr.substring(myIndex + 1, myStr.length);
                    //新的路径
                    var newPath = str + myStr;
                    draggingNode.data.index = newPath;
                    $this.newList = [];
                    $this.setMenuSort($this.treeData, false);
                    $this.sortMenu(JSON.stringify($this.newList));
                }

                // if(draggingNode.data.child.length > 0){
                //   //子元素信息
                //   var children = draggingNode.data.child;
                //   //父级路径
                //   var parentPath = draggingNode.data.index;
                //   for(var i in children){
                //     //获取到自己的路径
                //     var childStr = children[i].index;
                //     var childIndex = childStr.lastIndexOf("\/");
                //     childStr  = childStr .substring(childIndex + 1, childStr .length);
                //     children[i].index = parentPath + '/'+ childStr;
                //   }
                // }
            },
            handleDrop: function (draggingNode, dropNode, dropType, ev, data) {
                // console.log('tree drop: ', dropNode.title, dropType);
            },
            allowDrop: function (draggingNode, dropNode, type) {
                if (draggingNode.label === '首页') {
                    return;
                }
                if (
                    draggingNode.data.child !== undefined &&
                    draggingNode.data.child.length > 0
                ) {
                    //顶层禁止拖拽到二级层限制
                    if (type === "inner") {
                        return;
                    }
                    if (dropNode.data.child === undefined) {
                        return;
                    }
                } else {
                    //二级层禁止拖拽到三级层限制
                    if (type === "inner") {
                        if (dropNode.data.child === undefined) {
                            return;
                        }
                    }
                }
                if (dropNode.data.title === "二级 3-1") {
                    return type !== "inner";
                } else {
                    return true;
                }
            },
            allowDrag: function (draggingNode) {
                if (draggingNode.label === '首页') {
                    $this.$warMsg('首页禁止执行任何操作');
                    return;
                }
                return draggingNode.data.title.indexOf("三级 3-2-2") === -1;
            },

            /**
             * @description:
             * @param {string} data 列表数据id
             * @return:
             */
            sortMenu: function (data) {
                $this
                    .$api(apis.sort, {
                        menu: data
                    })
                    .then(function (data) {
                        $this.$sucMsg("拖拽成功");
                    })
                    .catch(function (e) {
                        $this.$warMsg(e);
                    });
            },

            /**
             * 设置排序
             */
            setMenuSort: function (treeData, isChild, isNum) {
                for (var i in treeData) {
                    if (treeData[i].child && treeData[i].child.length > 0) {
                        var child = treeData[i].child;
                        $this.newList.push({
                            id: treeData[i].id,
                            child: []
                        });
                        $this.setMenuSort(treeData[i].child, true, i);
                    } else {
                        if (isChild) {
                            $this.newList[isNum].child.push({
                                id: treeData[i].id
                            })
                        } else {
                            $this.newList.push({
                                id: treeData[i].id
                            });
                        }

                    }
                }
            },

            /**
             * 获取icon列表
             */
            getIcons: function () {
                Spa.get(Spa.baseUrl + "main/demo/iconfont.json").then(function (data) {
                    $this.iconList = data.glyphs;
                });
            },

            /**
             * 获取全部菜单
             */
            getMenuList: function () {
                $this
                    .$api(apis.sysUserMenu)
                    .then(function (data) {
                        var useData = data.data;
                        $this.treeData = data.data;

                        //设计上级菜单选项框数据
                        $this.$nextTick(function () {
                            var newTreeData = [];
                            var treeData = useData;
                            for (var index in treeData) {
                                if (treeData[index].title !== '首页') {
                                    var obj = {
                                        value: treeData[index].id,
                                        label: treeData[index].title
                                    };
                                    newTreeData.push(obj);
                                }
                            }
                            $this.$refs.selectMenu.options = newTreeData;
                        });
                    })
                    .catch(function (e) {
                        $this.$warMsg(e);
                    });
            }
        },
        created: function () {
            // console.log=function(){};//禁用所有控制台输出
            $this.getMenuList();
            $this.setData();
            $this.getIcons();
        }
    },
    ["/components/el-select"],
    "/index"
);
</script>
