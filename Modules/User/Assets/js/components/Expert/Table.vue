<template>
<div>
    <el-container>
        <el-header>
            <h1>{{ trans('users.title.experts') }}</h1>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{name: 'admin.dashboard.index'}">{{ trans('core.breadcrumb.home') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.experts.index'}">{{ trans('users.breadcrumb.experts') }}
                </el-breadcrumb-item>
            </el-breadcrumb>
        </el-header>
        <el-main>
            <el-row>
                <invite-dialog />
            </el-row>
            <el-form :inline="true">
                <el-form-item label="Search:">
                    <el-input prefix-icon="el-icon-search" @keyup.native="performSearch" v-model="searchQuery" placeholder="Keywords...">
                    </el-input>
                </el-form-item>
                <el-form-item label="Filter By Status:">
                    <el-select @change="performStatusFilter" v-model="statusFilter">
                        <el-option value="All">All</el-option>
                        <el-option value="Active">Active</el-option>
                        <el-option value="Not Activated">Not Activated</el-option>
                        <el-option value="Pending">Pending</el-option>
                        <el-option value="Rejected">Rejected</el-option>
                        <el-option value="Suspended">Suspended</el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="Filter By Group">
                    <el-select @change="performGroupFilter" v-model="groupFilter">
                        <el-option value="All">ALL</el-option>
                        <el-option v-for="group in filteredAvailableGroups"
                            :key="group.id"
                            :label="group.name"
                            :value="group.id">
                        </el-option>
                    </el-select>
                 </el-form-item>
            </el-form>
            <el-table
                    :data="data"
                    stripe
                    style="width: 100%"
                    ref="usersTable"
                    v-loading.body="tableIsLoading"
                    @sort-change="handleSortChange">
                <el-table-column prop="id" label="Id" width="75" sortable="custom">
                </el-table-column>
                <el-table-column prop="first_name" :label="trans('users.table.full-name')" sortable="custom">
                    <template slot-scope="scope">
                        <a @click.prevent="goToShow(scope)" href="#">
                            {{ scope.row.fullname }}
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="email" :label="trans('users.table.email')" sortable="custom">
                    <template slot-scope="scope">
                        <a @click.prevent="goToShow(scope)" href="#">
                            {{ scope.row.email }}
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" :label="trans('core.table.created at')" sortable="custom">
                </el-table-column>
                <el-table-column prop="status" :label="trans('users.table.status')" width="130">
                    <template slot-scope="scope">
                        <el-tag :type="scope.row.status.class" close-transition>
                            {{ scope.row.status.name }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="status" :label="trans('users.table.invited')" width="130">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.invited" :type="`info`" close-transition>
                            {{ 'Invited' }}
                        </el-tag>
                    </template>
                </el-table-column>

                <el-table-column prop="actions" :label="trans('core.table.actions')" width="180">
                    <template slot-scope="scope">
                        <el-dropdown split-button type="primary">
                            Actions
                            <el-dropdown-menu slot="dropdown">
                                <activate-table-item :scope="scope" :rows="data" v-if="!scope.row.activated">
                                </activate-table-item>
                                <accept-approval-table-item :scope="scope" :rows="data" v-if="scope.row.pending_approval">
                                </accept-approval-table-item>
                                <reject-approval-table-item :scope="scope" :rows="data" v-if="scope.row.pending_approval">
                                </reject-approval-table-item>
                                <view-table-item :to="{name: 'admin.user.experts.view', params: {id: scope.row.id}}">
                                </view-table-item>
                                <edit-table-item :to="{name: 'admin.user.experts.edit', params: {id: scope.row.id}}" v-if="permissions['user.experts.edit']">
                                </edit-table-item>
                                <suspend-table-item :scope="scope" :rows="data">
                                </suspend-table-item>
                                <delete-table-item :scope="scope" :rows="data" v-if="permissions['user.experts.destroy']">
                                </delete-table-item>
                            </el-dropdown-menu>
                        </el-dropdown>
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
    <nudge-panel
        v-bind:groupFilter="groupFilter"
        v-bind:searchQuery="searchQuery"
        v-bind:meta="meta"
        :canSendNudge="this.statusFilter == 'Active'"
        :groupId="this.groupFilter"
        :search="this.searchQuery"
        :tableIsLoading="this.tableIsLoading"
        :applicableUsers="this.meta.total">
    </nudge-panel>
</div>
</template>

<script>
    import axios from 'axios';
    import _ from 'lodash';
    import ShortcutHelper from '../../../../../Core/Assets/js/mixins/ShortcutHelper';
    import NudgePanel from './NudgePanel';
    import InviteDialog from './InviteDialog';
    import AcceptApprovalTableItemComponent from './../AcceptApprovalTableItemComponent';
    import RejectApprovalTableItemComponent from './../RejectApprovalTableItemComponent';

    export default {
        mixins: [ShortcutHelper],
        components: {
            'invite-dialog': InviteDialog,
            'nudge-panel': NudgePanel,
            'accept-approval-table-item' : AcceptApprovalTableItemComponent,
            'reject-approval-table-item' : RejectApprovalTableItemComponent,
        },
        computed: {
            filteredAvailableGroups() {
                return _.filter( this.availableGroups, (group) => { return !group.is_public && group.is_joinable } )
            },
            nudgePanelProperties: function() {
                return {
                    canSendNudge:    this.statusFilter == 'Active',
                    groupId:         this.groupFilter,
                    search:          this.searchQuery,
                    tableIsLoading:  this.tableIsLoading,
                    applicableUsers: this.meta.total
                }
            }
        },
        props: {
            availableGroups: { default: [], Array }
        },
        data() {
            return {
                data: [],
                permissions: [],
                meta: {
                    current_page: 1,
                    per_page: 10,
                    total: 0
                },
                order_meta: {
                    order_by: '',
                    order: '',
                },
                links: {},
                searchQuery: '',
                statusFilter: 'All',
                groupFilter: 'All',
                tableIsLoading: false,
            };
        },
        methods: {
            fetchPermissions() {
                const vm = this;
                axios.get(route('api.user.admin.permissions.index'))
                    .then((response) => {
                      vm.permissions = response.data.length ? response.data[0] : []
                    });
            },
            fetchUsers(customProperties) {
                const properties = {
                    page: this.meta.current_page,
                    per_page: this.meta.per_page,
                    order_by: this.order_meta.order_by,
                    order: this.order_meta.order,
                    search: this.searchQuery,
                    status: this.statusFilter,
                    group: this.groupFilter
                };

                this.tableIsLoading = true;

                axios.get(route('api.user.admin.experts.index', _.merge(properties, customProperties)))
                    .then((response) => {
                        this.tableIsLoading = false;
                        this.data  = response.data.data;
                        this.meta  = response.data.meta.pagination;
                        this.links = response.data.meta.pagination.links;

                        this.order_meta.order_by = properties.order_by;
                        this.order_meta.order = properties.order;
                    });
            },
            handleSizeChange(event) {
                this.tableIsLoading = true;
                this.fetchUsers({ per_page: event });
            },
            handleCurrentChange(event) {
                this.fetchUsers({ page: event });
            },
            handleSortChange(event) {
                this.fetchUsers({ order_by: event.prop, order: event.order });
            },
            performSearch: _.debounce(function (query) {
                this.fetchUsers({ search: query.target.value });
            }, 300),
            performStatusFilter(status) {
                this.fetchUsers({ status: status });
            },
            performGroupFilter(group) {
                this.fetchUsers({ group: group });
            },
            goToShow(scope) {
                this.$router.push({ name: 'admin.user.experts.view', params: { id: scope.row.id } });
            }
        },
        mounted() {
            this.fetchUsers();
            this.fetchPermissions();
        },
    };
</script>
