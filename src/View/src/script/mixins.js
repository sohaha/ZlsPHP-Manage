/**
 * Created by 影浅-seekwe@gmail.com on 2018-11-16
 */
var mixinLists = {
  data: function () {
    return {
      ml_listsLoading: false,
      ml_searchKey: '',
      ml_page: 1,
      ml_data: [],
      ml_pagetotal: 0,
      ml_pagesize: 10,
      ml_pages: {},
    };
  },
  watch: {
    ml_page: {
      handler: function (val, oldVal) {
        this.$nextTick(this.getLists);
      },
      immediate: true,
    },
  },
  methods: {
    ml_currentChange: function (e) {
      this.ml_page = e;
    },
    ml_sizeChange: function (e) {
      this.ml_searchRow();
    },
    ml_reloadLists: function () {
      this.getLists();
    },
    ml_searchRow: function () {
      if (this.ml_page !== 1) {
        this.ml_page = 1;
      } else {
        this.getLists();
      }
    },
    // 设置数据
    ml_getLists: function (data, page) {
      this.ml_data = data;
      this.ml_pagetotal = page.total;
      if (!!page.count && this.ml_page > page.end) {
        this.ml_page = page.end;
      }
    },
    getLists: function () {

    },
  },
};
