<style>
</style>
<template>
  <div>
    <div class="view-title float-clear">
      <span>{{title}}</span>
    </div>
    <div class="panel">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="80px">
        <el-form-item label="场景地址" prop="scene_url">
          <el-input v-model="ruleForm.scene_url"></el-input>
        </el-form-item>
        <el-form-item label="分享跳转" prop="share_url">
          <el-input v-model="ruleForm.share_url"></el-input>
        </el-form-item>
        <el-form-item label="直接跳转" prop="jump_url">
          <el-input v-model="ruleForm.jump_url"></el-input>
        </el-form-item>
        <el-form-item label="链接备注" prop="remark">
          <el-input type="textarea" v-model="ruleForm.remark"></el-input>
        </el-form-item>
        <div class="center">
          <el-button type="primary" @click="submitForm">{{ btnText }}</el-button>
          <el-button @click="resetForm">放 弃</el-button>
        </div>
      </el-form>
    </div>
  </div>
</template>
<script>
var ruleForm = {
    scene_url: "",
    back_url: "",
    jump_url: "",
    share_url: "",
    remark: ""
  },
  that;
Spa.define(
  {
    mixins: [mixinLists],
    data: function() {
      return {
        id: 0,
        SpaTitle: "",
        ruleForm: Object.assign({}, ruleForm)
      };
    },
    created: function() {
      that = this;
    },
    computed: {
      title: function() {
        var title = that.id ? "编辑链接" : "添加链接";
        that.SpaTitle = title + " - %s";
        return title;
      },
      btnText: function() {
        return that.id ? "编 辑" : "添 加";
      },
      rules: function() {
        return {
          scene_url: [
            { required: true, message: "请输入场景地址", trigger: "input" }
          ]
        };
      }
    },
    init: function(query, search) {
      that.id = search[0] || 0;
    },
    methods: {
      submitForm: function() {
        this.$refs["ruleForm"].validate(function(valid) {
          if (valid) {
            that
              .$api("ListsAddLists", that.ruleForm)
              .then(function(e) {
                that.$go("layout-user/link");
                console.log("layout-user/link");
                that.$tipMsg(e.msg);
              })
              .catch(function(e) {
                that.$warMsg(e);
              });
          } else {
            return false;
          }
        });
      },
      resetForm: function() {
        that.$go("../");
      }
    }
  },
  [],
  "/index"
);
</script>
