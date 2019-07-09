<template>
    <el-container>
        <el-header>
          <el-col :xl="10" :lg="10" :md="10" :sm="24" :xs="24">
            <h1 class="text-blue" style="font-size:30px;">
              <img v-if="user.avatar" :src="user.avatar" class="sm-avatar" />
              <strong>{{ user.fullname }}</strong>
            </h1>
          </el-col>
          <el-col :xl="14" :lg="14" :md="14" :sm="24" :xs="24">
            <el-breadcrumb separator="/">
              <el-breadcrumb-item :to="{name: 'admin.dashboard.index'}">{{ trans('core.breadcrumb.home') }}
              </el-breadcrumb-item>
              <el-breadcrumb-item :to="{name: 'admin.user.traders.index'}">{{ trans('users.breadcrumb.users') }}
              </el-breadcrumb-item>
              <el-breadcrumb-item>{{ user.fullname }}
              </el-breadcrumb-item>
            </el-breadcrumb>
          </el-col>
          <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24" class="text-right" v-if="user">
            <el-button type="warning" size="mini" @click="markApproval(user.urls.suspend_url, 'block')" v-if="user.status.value != 5">{{ trans('users.block') }}</el-button>
            <el-button type="warning" size="mini" @click="markApproval(user.urls.unsuspend_url, 'unblock')" v-if="user.status.value == 5">{{ trans('users.unblock') }}</el-button>
            <el-button type="success" size="mini" @click="markApproval(user.urls.accept_approval_url, 'accept')" v-if="user.status.value == 0  || user.status.value == 2">{{ trans('users.accept approval') }}</el-button>
            <el-button type="danger" size="mini" @click="markApproval(user.urls.reject_approval_url, 'reject')" v-if="user.status.value == 0  || user.status.value == 1">{{ trans('users.reject approval') }}</el-button>
            <el-button type="info" size="mini" v-if="user.status.value == 1">{{ trans('users.approved') }}</el-button>
            <el-button type="info" size="mini" v-if="user.status.value == 2">{{ trans('users.rejected') }}</el-button>
            <el-button type="info" size="mini" v-if="user.status.value == 5">{{ trans('users.suspended') }}</el-button>
          </el-col>
        </el-header>
        <el-main>
            <el-row class="view-more">
              <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                <el-tabs>
                  <el-tab-pane>
                    <span slot="label"class="tab-label">
                        {{ trans('users.tabs.details') }}
                    </span>
                    <el-card class="user-detail-card" v-loading="loading">
                        <div class="card-header"></div>
                        <div class="card-details" >
                            <el-row :gutter="32" v-if="!loading">
                                <el-col :xl="7" :lg="5" :md="23" :sm="23" :xs="23" :offset="1">
                                    <img :src="user.avatar" class="avatar-image"/>
                                </el-col>
                                <el-col :xl="8" :lg="8" :md="23" :sm="23" :xs="23" :offset="1">
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.name') }}</label>
                                        <span class="label-text">{{ user.fullname }}</span>
                                    </div>
                                    <div class="detail-block" v-if="user.activation">
                                      <label>{{ trans('users.form.status') }}</label>
                                      <el-tag :type="user.status.class" close-transition> {{ user.status.name }} </el-tag></p>
                                    </div>
                                    <div class="detail-block">
                                      <label>{{ trans('users.form.postcode') }}</label>
                                      <span class="label-text">{{ user.postcode }}</span>
                                    </div>
                                    <div class="detail-block" v-if="user.timezone">
                                      <label>{{ trans('users.form.timezone') }}</label>
                                      <span class="label-text">{{ user.timezone }}</span>
                                    </div>
                                    <div class="detail-block" v-if="user.last_login">
                                        <label>{{ trans('users.form.last-active') }}</label>
                                        <span class="label-text">{{ user.last_login }}</span>
                                    </div>
                                </el-col>
                                <el-col :xl="7" :lg="7" :md="24" :sm="24" :xs="24">
                                    <div class="detail-block">
                                      <label>{{ trans('users.form.email') }}</label>
                                      <span class="label-text">{{ user.email }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.vat number') }}</label>
                                        <span class="label-text">{{ user.vat_number }}</span>
                                    </div>
                                    <div class="detail-block">
                                      <label>{{ trans('users.form.address') }}</label>
                                      <span class="label-text">{{ user.address }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.region or state') }}</label>
                                        <span class="label-text">{{ user.region }}</span>
                                    </div>
                                    <div class="detail-block" v-if="user.team">
                                      <label>{{ trans('users.form.team') }}</label>
                                      <span class="label-text">{{ user.team.name }}</span>
                                    </div>
                                </el-col>
                            </el-row>
                        </div>
                        <div class="company-detail">
                            <el-row :gutter="32" v-if="!loading">
                              <el-col :xl="24" :lg="24">
                                <h4 class="form-title">{{ trans('users.form.company_detail') }}</h4>
                                <el-col :xl="8" :lg="8" :md="8" :sm="24" :xs="24">
                                  <div class="detail-block">
                                    <label>{{ trans('users.form.company name') }}</label>
                                    <span class="text" v-if="user.company && user.company.name">{{ user.company.name }}</span>
                                  </div>
                                  <div class="detail-block">
                                    <label>{{ trans('users.form.company_street') }}</label>
                                    <span class="text">{{ user.company_street }}</span>
                                  </div>
                                </el-col>
                                <el-col :xl="8" :lg="8" :md="8" :sm="24" :xs="24">
                                  <div class="detail-block">
                                    <label>{{ trans('users.form.parent_company') }}</label>
                                    <span class="text">{{ user.parent_company_name }}</span>
                                  </div>
                                  <div class="detail-block">
                                    <label>{{ trans('users.form.company_contact') }}</label>
                                    <span class="text">{{ user.company_phone_contact }}</span>
                                  </div>
                                </el-col>
                                <el-col :xl="8" :lg="8" :md="8" :sm="24" :xs="24">
                                  <div class="detail-block">
                                    <label>{{ trans('users.form.company_number') }}</label>
                                    <span class="text">{{ user.company_number }}</span>
                                  </div>
                                  <div class="detail-block">
                                    <label>{{ trans('users.form.company_town') }}</label>
                                    <span class="text">{{ user.company_town }}</span>
                                  </div>
                                </el-col>
                              </el-col>
                              <el-col :xl="24" :lg="24" v-if="user.group">
                              <h4 class="form-title">{{ trans('users.form.group') }}</h4>
                              <span class="text">{{ user.group.name }}</span>
                              </el-col>
                          </el-row>
                        </div>
                        <div class="card-interests" v-if="user.tags">
                            <h4 class="form-title">{{ trans('users.subtitle.expertise') }}</h4>
                            <el-col :xl="24" :lg="24" class="col-interests">
                                <span class="interest" v-for="tag in user.tags.data">{{ tag.name }}</span>
                            </el-col>
                        </div>
                    </el-card>
                  </el-tab-pane>

                  <el-tab-pane>
                    <span slot="label" class="tab-label">
                        {{ trans('users.tabs.transactions') }} <span class="count el-button el-button--small is-round el-button--danger"><strong>{{!transactiontTableIsLoading && transactions.length ? bookingMeta.total : 0}}</strong></span>
                    </span>
                    <el-card class="user-detail-card" style="min-height:300px;padding30px;text-align:center;">
                      <el-table
                              v-if="!transactiontTableIsLoading && transactions.length"
                              :data="transactions"
                              stripe
                              ref="bookingsTable"
                              v-loading.body="transactiontTableIsLoading"
                              @sort-change="handleSortChange">
                          <!-- <el-table-column prop="id" label="Id" width="75" sortable="custom"></el-table-column> -->
                          <el-table-column prop="expert" :label="trans('bookings.table.expert')" width="150">
                              <template slot-scope="scope">
                                {{scope.row.expert.full_name}}
                              </template>
                          </el-table-column>
                          <el-table-column prop="number_of_hours" :label="trans('bookings.table.duration')"  width="150" sortable="custom">
                              <template slot-scope="scope">
                                {{`${scope.row.number_of_hours} hr(s)`}}
                              </template>
                          </el-table-column>

                          <el-table-column prop="start_time" :label="trans('bookings.table.booked for')" width="150" sortable="custom"></el-table-column>

                          <el-table-column prop="trader" :label="trans('bookings.table.trader name')" width="150">
                              <template slot-scope="scope">
                                {{scope.row.trader.full_name}}
                              </template>
                          </el-table-column>
                          <el-table-column prop="cost" :label="trans('bookings.table.cost')" width="100" sortable="custom">
                            <template slot-scope="scope">
                                <strong>{{`£${scope.row.cost}`}}</strong>
                            </template>
                          </el-table-column>
                          <el-table-column prop="status" :label="trans('bookings.table.status')" width="120">
                              <template slot-scope="scope">
                                  <el-button class="el-button--small" :type="statusButton[scope.row.status]">
                                      {{ scope.row.status }}
                                  </el-button>
                              </template>
                          </el-table-column>
                          <el-table-column prop="type" :label="trans('bookings.table.type')" width="120">
                              <template slot-scope="scope">
                                  <el-button class="el-button--small" type="default">
                                      {{ scope.row.type }}
                                  </el-button>
                              </template>
                          </el-table-column>

                          <el-table-column prop="view" :label="trans('core.table.actions')" width="120">
                              <template slot-scope="scope">
                                <router-link :to="{name: 'admin.booking.bookings.view', params: {id: scope.row.id}}">
                                    <el-button type="basic" class="el-button--small">
                                        {{ trans('bookings.button.view') }}
                                    </el-button>
                                </router-link>
                              </template>
                          </el-table-column>
                      </el-table>
                      <el-pagination
                              v-if="!transactiontTableIsLoading && transactions.length"
                              @size-change="handleSizeChange"
                              @current-change="handleCurrentChange"
                              :current-page.sync="bookingMeta.current_page"
                              :page-sizes="[10, 20, 50, 100]"
                              :page-size="parseInt(bookingMeta.per_page)"
                              layout="total, sizes, prev, pager, next, jumper"
                              :total="bookingMeta.total">
                      </el-pagination>
                      <h2 v-if="!transactiontTableIsLoading && !transactions.length" style="margin-top:90px;">{{ trans('users.no completed transactions found') }}</h2>
                    </el-card>
                  </el-tab-pane>
                </el-tabs>
              </el-col>
            </el-row>
        </el-main>
    </el-container>
</template>

<script>
    import axios from 'axios';
    import _ from 'lodash';
    import ShortcutHelper from '../../../../../Core/Assets/js/mixins/ShortcutHelper';

    export default {
        mixins: [ShortcutHelper],
        data() {
            return {
                user: {
                    id: this.$route.params.id,
                    first_name: '',
                    last_name: '',
                    fullname: '',
                    email: '',
                    avatar: '',
                    created_at: '',
                    group_label: '',
                    status: {
                        name: '',
                        class: ''
                    }
                },
                statusButton:{
                  Pending: 'primary',
                  Confirmed: 'success',
                  Complete: 'info',
                  Archived: 'warning',
                  Cancelled: 'danger'
                },
                booking: {
                  meta: {
                      current_page: 1,
                      per_page: 10,
                      status: 'Complete'
                  },
                  order_meta: {
                      order_by: 'updated_at',
                      order: 'desc',
                  }
                },
                transactions: [],
                bookingMeta: null,
                bookingMetaLinks: null,
                transactiontTableIsLoading: false,
                loading: true,
                togglerLeaderLoading: false,
                approvalStatus: 0
            };
        },
        computed: {},
        methods: {
            fetchUser() {
                const vm = this;
                vm.transactiontTableIsLoading = true;
                axios.get(route('api.user.admin.traders.show', this.$route.params.id))
                    .then((response) => {
                        vm.user = response.data;
                        vm.loading = false;

                        vm.approvalStatus = vm.user.status.value;
                        vm.fetchAllBookings({ status: 'Complete' });

                    });
            },
            handleSizeChange(event) {
                this.fetchAllBookings({ per_page: event });
            },
            handleSortChange(event) {
                this.fetchAllBookings({ order_by: event.prop, order: event.order });
            },
            handleCurrentChange(event) {
                this.fetchAllBookings({ page: event });
            },
            fetchAllBookings(customProperties) {
              const vm = this;

              if(customProperties){
                vm.booking.meta.current_page = 1;
              }

              const properties = {
                  page: vm.booking.meta.current_page,
                  per_page: vm.booking.meta.per_page,
                  status: vm.booking.meta.status,
                  order_by: vm.booking.order_meta.order_by,
                  order: vm.booking.order_meta.order,
                  is_trader: 1,
                  trader: vm.user.id
              };

              axios.get(route('api.booking.admin.bookings.index',  _.merge(properties, customProperties)))
                   .then((response) => {
                      vm.tableIsLoading   = false;
                      vm.transactions     = response.data.data;
                      vm.bookingMeta      = response.data.meta.pagination;
                      vm.bookingMetaLinks = response.data.meta.pagination.links;
                      vm.transactiontTableIsLoading = false;
                   });

            },
            markApproval(url, type) {
                this.title = this.trans('core.modal.title')

                let message = this.trans('core.modal.accept-approval-confirmation-message');

                if(type == 'reject') {
                  message = this.trans('core.modal.reject-approval-confirmation-message');
                }

                if(type == 'block') {
                  message = this.trans('core.modal.block-confirmation-message');
                }

                if(type == 'unblock') {
                  message = this.trans('core.modal.unblock-confirmation-message');
                }

                this.message = message;

                let confirmButtonText = this.trans('core.button.accept-approval');

                if(type == 'reject') {
                  confirmButtonText = this.trans('core.button.reject-approval');
                }

                if(type == 'block') {
                  confirmButtonText = this.trans('core.button.block-user');
                }

                if(type == 'unblock') {
                  confirmButtonText = this.trans('core.button.unblock-user');
                }

                this.$confirm(this.message, this.title, {
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: this.trans('core.button.cancel'),
                    type: 'warning',
                    confirmButtonClass: 'el-button--success',
                }).then(() => {
                    const vm = this
                    axios.get(url)
                        .then((response) => {
                            vm.$message({
                                type: 'success',
                                message: response.data.message,
                            })
                            vm.user.pending_approval = response.data.pending_approval;
                            vm.user.status           = response.data.status;
                        })
                        .catch((error) => {
                            vm.$message({
                                type: 'error',
                                message: error.response.data.error.message,
                            })
                        })
                }).catch(() => {

                    let message = this.trans('core.accept-approval cancelled');

                    if(type == 'reject') {
                      message = this.trans('core.reject-approval cancelled');
                    }

                    if(type == 'block') {
                      message = this.trans('core.suspend cancelled');
                    }

                    if(type == 'unblock') {
                      message = this.trans('core.unsuspend cancelled');
                    }

                    this.$message({
                        type: 'info',
                        message: message,
                    })
                })
            }
        },
        mounted() {
            this.fetchUser();
        }
    };
</script>
<style lang="scss">
  span.label-text{
    font-weight: 700;
    font-size: 14px;
  }
  span.tab-label{
    text-transform: uppercase;
    font-weight: 700;
  }
  .el-main{
    width: 100%;
  }
  .user-detail-card{
    min-height: 300px;
  }
  .company-detail {
    padding: 0px 20px;
  }
</style>
