/*
 * @Author: seekwe
 * @Date:   2019-06-28 13:07:10
 * @Last Modified by:   seekwe
 * @Last Modified time: 2019-06-28 15:49:41
 */

<style>
</style>
<template>
  <div>
    <div class="view-title float-clear">
      <!--占位-->
    </div>
    <fieldset>
      <legend>{{title}}</legend>
      <aside :aria-label="title">
        <form-create
          class="form-create-mini"
          v-model="formApi"
          :rule="formRule"
          :option="formOption"
        ></form-create>
      </aside>
    </fieldset>
    <div class="panel">
      更多自定义配置请参考
      <a
        href="http://form-create.com/v2/element-ui/global.html#row"
        target="_blank"
      >form-create 文档</a>。
    </div>
  </div>
</template>
<script>
var loadJs = [
    "//cdn.jsdelivr.net/npm/zls-manage/form-create/form-create.min.js" // 异步引入
  ],
  ruleForm = { title: "", select: "", status: "1" }; // 表单初始数据
Spa.define(
  {
    mixins: [initTitle],
    data: function() {
      return {
        formApi: {},
        formRule: [
          {
            type: "input",
            field: "text",
            title: "文本",
            validate: [
              { required: true, message: "请输入文本", trigger: "blur" }
            ]
          },
          {
            type: "datePicker",
            field: "created_at",
            title: "时间"
          },
          {
            type: "select",
            field: "cate_id",
            title: "分类",
            value: ["104", "105"],
            options: [
              { value: "104", label: "生态", disabled: false },
              { value: "105", label: "天然", disabled: false }
            ],
            validate: [{ required: true, message: "请选择", trigger: "blur" }],
            props: {
              // multiple: true
            }
          },
          {
            type: "switch",
            title: "开启",
            field: "status",
            className: "className",
            value: 1,
            props: {
              activeValue: 1,
              inactiveValue: 0
            }
          }
        ],
        formOption: {
          form: {
            className: "hi",
            hideRequiredAsterisk: true,
            labelWidth: "110px",
            showMessage: true,
            size: "mini"
          },
          submitBtn: {
            size: "mini",
            loading: false,
            disabled: false,
            icon: "",
            innerText: "提 交",
            width: "auto"
          },
          resetBtn: {
            icon: "",
            innerText: "重 置",
            size: "mini",
            width: "auto",
            show: true
          },
          onSubmit: function(formData) {
            console.log(JSON.stringify(formData));
          }
        }
      };
    },
    beforeCreate: function() {
      that = this;
    },
    mounted: function() {},
    computed: {
      rules: function() {
        var rules = {
          title: [{ required: true, message: "请输入标题", trigger: "change" }],
          select: [{ required: true, message: "请选择", trigger: "change" }]
        };
        return rules;
      }
    },
    init: function(query, search) {},
    methods: {
      submitForm: function() {
        this.$refs["ruleForm"].validate(function(valid) {
          if (valid) {
            that.formState = true;
            // 这里请求接口
            setTimeout(function() {
              console.log("submitForm ok");
              that.formState = false;
            }, 3000);
          } else {
            return false;
          }
        });
      },
      resetForm: function() {
        this.$nextTick(function() {
          that.$refs["ruleForm"].clearValidate();
        });
        this.ruleForm = JSON.parse(JSON.stringify(ruleForm));
      }
    }
  },
  [],
  "/index"
);
</script>
