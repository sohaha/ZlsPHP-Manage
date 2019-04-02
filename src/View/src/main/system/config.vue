<style>
</style>
<template>
  <div>
    <div class="view-title float-clear">
      <span v-once>{{title}}</span>
    </div>
    <div class="panel">
      <el-form
        class="form-verticalRow"
        label-position="top"
        :rules="rules"
        ref="form"
        :model="form"
        label-width="90px"
      >
        <el-form-item label="微信接口域名" prop="scale">
          <el-input v-model.number="form.wxjs" size="mini" placeholder></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submit">提 交</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>
<script>
var title = "系统设置",
  form = { wxjs: "" },
  that;
Spa.define(
  {
    mixins: [mixinLists],
    data: function() {
      return {
        title: title,
        SpaTitle: title + " - %s",
        form: Object.assign({}, form),
        rules: {}
      };
    },
    created: function() {
      that = this;
    },
    mounted: function() {
      this.getConfig();
    },
    computed: {},
    init: function(query, search) {},
    methods: {
      getConfig: function() {
        that.$api("sysGetSystemConfig").then(function(e) {
          var data = Object.assign(form, e.data);
          data["scale"] = +data["scale"];
          that.form = data;
        });
      },
      submit: function() {
        that.$refs["ruleForm"].validate(function(valid) {
          if (valid) {
            that
              .$api("sysSetSystemConfig", that.form)
              .then(function(e) {
                console.log(e);
                that.$tipMsg("更新成功");
              })
              .catch(function(err) {
                that.$warMsg(err);
              });
          } else {
            return false;
          }
        });
      }
    }
  },
  [],
  "/index"
);
</script>
