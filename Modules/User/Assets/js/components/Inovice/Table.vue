<template>
  <el-container>
      <el-header>
          <h1>{{ trans('bookings.title.invoices') }}</h1>
          <el-breadcrumb separator="/">
              <el-breadcrumb-item :to="{name: 'admin.dashboard.index'}">{{ trans('core.breadcrumb.home') }}
              </el-breadcrumb-item>
              <el-breadcrumb-item :to="{name: 'admin.invoice.index'}">{{ trans('bookings.breadcrumb.invoices') }}
              </el-breadcrumb-item>
          </el-breadcrumb>
      </el-header>
      <el-main>
        <el-table
                :data="invoices"
                stripe
                style="width: 100%"
                ref="invoiceTable"
                v-loading.body="tableIsLoading"
                >
            <el-table-column prop="id" label="Id" width="40">
            </el-table-column>
            <el-table-column prop="description" :label="trans('bookings.table.description')" width="150">
                <template slot-scope="scope">
                  {{ scope.row.description }}
                </template>
            </el-table-column>
            <el-table-column prop="amount" :label="trans('bookings.table.cost')" width="50">
                <template slot-scope="scope">
                  <strong>£{{ (scope.row.amount / 100).toFixed(2) }}</strong>
                </template>
            </el-table-column>
            <el-table-column prop="status" :label="trans('bookings.table.status')" width="70">
                <template slot-scope="scope">
                  <el-tag type="success" close-transition style="text-transform:capitalize">
                    {{ scope.row.status }}
                  </el-tag>
                </template>
            </el-table-column>
          </el-table>
          <el-pagination
                  @size-change="handleSizeChange"
                  @current-change="handleCurrentChange"
                  :current-page.sync="meta.current_page"
                  :page-sizes="[10, 20, 50, 100]"
                  :page-size="parseInt(meta.per_page)"
                  layout="total, sizes, prev, pager, next, jumper"
                  :total="meta.total">
          </el-pagination>
      </el-main>
    </el-container>
</template>
<script>
import axios from 'axios'

export default {
  data() {
    return {
      invoices : [],
      tableIsLoading: false,
      meta: {
        current_page: 1,
        per_page: 10,
        total: 0
      },
      order_meta: {
        order_by: 'id',
        order: 'Desc'
      }
    };
  },
  created() {
    this.fetchInvoices()
  },
  methods: {
    fetchInvoices(customProperties) {
      const vm = this
      vm.tableIsLoading = true

      const properties = {
        page: this.meta.current_page,
        per_page: this.meta.per_page,
        order_by: this.order_meta.order_by,
        order: this.order_meta.order
      };

      axios.get(route('api.user.admin.invoices.index',  _.merge(properties, customProperties)))
       .then((response) => {
          vm.invoices = response.data.data
          vm.meta  = response.data.meta.pagination
          vm.tableIsLoading = false
       }).catch((error) => { console.log(error) })
    },
    handleSizeChange(event) {
      this.fetchInvoices({ per_page: event })
    },
    handleCurrentChange(event) {
      this.fetchInvoices({ page: event })
    },
  }
};
</script>
