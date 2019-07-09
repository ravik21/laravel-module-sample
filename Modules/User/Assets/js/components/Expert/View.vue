<template>
    <el-container>
        <el-header>
            <el-col :xl="10" :lg="10" :md="10" :sm="24" :xs="24">
              <h1 class="text-blue" style="font-size:30px;">
                <img v-if="expert.avatar" :src="expert.avatar" class="sm-avatar" />
                <strong>{{ expert.fullname }}</strong>
              </h1>
            </el-col>
            <el-col :xl="14" :lg="14" :md="14" :sm="24" :xs="24">
              <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{name: 'admin.dashboard.index'}">{{ trans('core.breadcrumb.home') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.experts.index'}">{{ trans('users.breadcrumb.users') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item>{{ expert.fullname }}
                </el-breadcrumb-item>
              </el-breadcrumb>
            </el-col>
            <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24" class="text-right" v-if="expert">
              <el-button type="warning" size="mini" @click="markApproval(expert.urls.suspend_url, 'block')" v-if="expert.status.value != 5">{{ trans('users.block') }}</el-button>
              <el-button type="warning" size="mini" @click="markApproval(expert.urls.unsuspend_url, 'unblock')" v-if="expert.status.value == 5">{{ trans('users.unblock') }}</el-button>
              <el-button type="success" size="mini" @click="markApproval(expert.urls.accept_approval_url, 'accept')" v-if="expert.status.value == 0  || expert.status.value == 2">{{ trans('users.accept approval') }}</el-button>
              <el-button type="danger" size="mini" @click="markApproval(expert.urls.reject_approval_url, 'reject')" v-if="expert.status.value == 0  || expert.status.value == 1">{{ trans('users.reject approval') }}</el-button>
              <el-button type="info" size="mini" v-if="expert.status.value == 1">{{ trans('users.approved') }}</el-button>
              <el-button type="info" size="mini" v-if="expert.status.value == 2">{{ trans('users.rejected') }}</el-button>
            </el-col>
        </el-header>
        <el-main>
          <el-row class="view-more">
            <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
              <el-tabs>
                <el-tab-pane>
                    <span slot="label"class="tab-label">
                        {{ trans('users.tabs.details') }}</span>
                    </span>
                    <el-card class="user-detail-card" v-loading="loading">
                        <div class="card-header"></div>
                        <div class="card-details" v-if="expert" >
                            <el-row :gutter="32">
                                <el-col :xl="7" :lg="5" :md="23" :sm="23" :xs="23" :offset="1">
                                  <img :src="expert.avatar" class="avatar-image"/>
                                </el-col>
                                <el-col :xl="7" :lg="7" :md="23" :sm="23" :xs="23" :offset="1">
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.name') }}</label>
                                        <span class="label-text">{{ expert.fullname }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.status') }}</label>
                                        <el-tag :type="expert.status.class" close-transition> {{ expert.status.name }} </el-tag>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.last-active') }}</label>
                                        <span class="label-text">{{ expert.last_login }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.email') }}</label>
                                        <span class="label-text">{{ expert.email }}</span>
                                    </div>
                                    <div class="detail-block" v-if="expert.activation">
                                        <label>{{ trans('users.form.joined') }}</label>
                                        <span class="label-text">{{ expert.activation.completed_at }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.id no') }}</label>
                                        <span class="label-text">{{ expert.id }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('bookings.view.review submitted by trader') }}</label>
                                        <span class="label-text">
                                          <div class="star-rating"
                                            v-if="expert.average_rating">
                                            <span v-for="n in max">&star;</span>
                                            <div class="star-rating__current" :style="{width: getStars(expert.average_rating)}">
                                              <span v-for="n in max">&starf;</span>
                                            </div>
                                          </div>
                                          <h4 v-else>{{trans('bookings.view.not submitted yet')}}</h4>
                                        </span>
                                    </div>
                                </el-col>
                                <el-col :xl="7" :lg="7" :md="23" :sm="23" :xs="23" :offset="1">
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.company name') }}</label>
                                        <span class="label-text" v-if="expert.company && expert.company.name">{{ expert.company.name }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.vat number') }}</label>
                                        <span class="label-text">{{ expert.vat_number }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.position') }}</label>
                                        <span class="label-text">{{ expert.company_position }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.address') }}</label>
                                        <span class="label-text">{{ expert.address }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.postcode') }}</label>
                                        <span class="label-text">{{ expert.postcode }}</span>
                                    </div>
                                    <div class="detail-block">
                                        <label>{{ trans('users.form.region or state') }}</label>
                                        <span class="label-text">{{ expert.region }}</span>
                                    </div>
                                </el-col>
                                <el-col :xl="10" :lg="10" :md="10" :sm="22" :xs="22" :offset="1" v-if="expert.hour_rate">
                                  <h4 class="form-title">{{ trans('users.form.hourly_rate') }}</h4>
                                  <p><h3>{{`£${expert.hour_rate}`}}</h3></p>
                                </el-col>
                                <el-col :xl="10" :lg="10" :md="10" :sm="22" :xs="22" :offset="1" v-if="expert.availability">
                                  <h4 class="form-title">{{ trans('users.form.availability') }}</h4>
                                  <span class="text" v-text="expert.availability"></span>
                                </el-col>
                                <el-col :xl="22" :lg="22" :md="22" :sm="22" :xs="22" :offset="1" v-if="expert.tags && expert.tags.data.length">
                                  <h4 class="form-title">{{ trans('users.form.expertise') }}</h4>
                                </el-col>
                                <el-col :xl="22" :lg="22" :md="22" :sm="22" :xs="22" :offset="1" class="expertise" v-if="expert.tags && expert.tags.data.length">
                                  <el-col :xl="6" :lg="6" :md="8" :sm="24" :xs="24"  v-for="(tags , i) in expert.tags.data" :key="i" style="min-height:30px;padding:0;"><a href="#" v-text="tags.name"></a></el-col>
                                </el-col>
                                <el-col :xl="10" :lg="10" :md="10" :sm="22" :xs="22" :offset="1" v-if="expert.experience">
                                  <h4 class="form-title">{{ trans('users.form.experience') }}</h4>
                                  <p v-text="expert.experience"></p>
                                </el-col>
                                <el-col :xl="10" :lg="10" :md="10" :sm="22" :xs="22" :offset="1" v-if="expert.past_projects">
                                  <h4 class="form-title">{{ trans('users.form.past_projects') }}</h4>
                                  <p class="text" v-text="expert.past_projects"></p>
                                </el-col>
                                <el-col :xl="10" :lg="10" :md="10" :sm="22" :xs="22" :offset="1" v-if="expert.languages">
                                  <h4 class="form-title">{{ trans('users.form.languages') }}</h4>
                                  <p v-text="expert.languages"></p>
                                </el-col>
                                <el-col :xl="10" :lg="10" :md="10" :sm="22" :xs="22" :offset="1" v-if="expert.education">
                                  <h4 class="form-title">{{ trans('users.form.education') }}</h4>
                                  <p v-text="expert.education"></p>
                                </el-col>
                                <el-col :xl="10" :lg="10" :md="10" :sm="22" :xs="22" :offset="1" v-if="expert.memberships">
                                  <h4 class="form-title">{{ trans('users.form.memberships') }}</h4>
                                  <p v-text="expert.memberships"></p>
                                </el-col>
                                <el-col :xl="10" :lg="10" :md="10" :sm="22" :xs="22" :offset="1" v-if="expert.education">
                                  <h4 class="form-title">{{ trans('users.form.references') }}</h4>
                                  <p v-text="expert.references"></p>
                                </el-col>
                                <hr>
                                <el-col :xl="22" :lg="22" :md="22" :sm="22" :xs="22" :offset="1" v-if="expert.groups && expert.groups.data.length">
                                  <h4 class="form-title">{{ trans('users.subtitle.expertise') }}</h4>
                                </el-col>
                                <el-col :xl="22" :lg="22" :md="22" :sm="22" :xs="22" :offset="1" class="expertise card-interests" v-if="expert.groups && expert.groups.data.length">
                                  <el-col class="col-interests" style="padding:0;">
                                    <span class="interest" v-for="(group , i) in expert.groups.data" :key="i">{{ group.name }}</span>
                                  </el-col>
                                </el-col>
                              </el-row>
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
                            <template slot-scope="scope" v-if="scope.row.expert">
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
                            <template slot-scope="scope" v-if="scope.row.trader">
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
    import _ from 'lodash'
    import axios from 'axios'
    import AcceptApprovalTableItemComponent from './../AcceptApprovalTableItemComponent';
    import RejectApprovalTableItemComponent from './../RejectApprovalTableItemComponent';

    export default {
        data() {
            return {
                expert: {
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
                loading: false,
                togglerLeaderLoading: false,
                message: '',
                title: '',
                rated: 3,
                max: 5
            }
        },
        components: {
            'accept-approval-table-item' : AcceptApprovalTableItemComponent,
            'reject-approval-table-item' : RejectApprovalTableItemComponent,
        },
        methods: {
            fetchExpert () {
                const vm   = this;
                vm.loading = true
                vm.transactiontTableIsLoading = true;
                axios.get(route('api.user.admin.experts.show', vm.$route.params.id))
                     .then((response) => {
                        vm.expert = response.data
                        vm.loading = false
                        vm.fetchAllBookings({ schedule_status: 'previous' });
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
                  order_by: vm.booking.order_meta.order_by,
                  order: vm.booking.order_meta.order,
                  is_expert: 1,
                  expert: vm.expert.id
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
                          vm.expert.pending_approval = response.data.pending_approval;
                          vm.expert.status           = response.data.status;
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
            },
            getStars(rating) {
                var val = parseFloat(rating);
                // Turn value into number/100
                var size = val/5*100;
                return size + '%';
            }
        },
        created () {
            this.fetchExpert();
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
  .el-icon-star-on{
    color: #f2bb78 !important;
    font-size: 15px;
  }
  .star-rating {
    display: inline-block;
    font-size: 1.4em;
    position: relative;
    transform: translate(-6px);
  }
  .star-rating span{
    color: rgb(198, 209, 222) !important;
    padding: 5px;
  }
  .star-rating__max,
  .star-rating__current {
    position: absolute;
    top: 0;
  }
  .star-rating__current span {
    color: #f2bb78!important;
    padding: 5px;
  }

  .star-rating__current {
    overflow: hidden;
    white-space: nowrap;
  }
</style>
