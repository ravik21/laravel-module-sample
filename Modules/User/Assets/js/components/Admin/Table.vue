<template>
    <el-container>
        <el-header>
            <h1>{{ trans('users.title.admin users') }}</h1>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{name: 'admin.dashboard.index'}">{{ trans('core.breadcrumb.home') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.admin-users'}">{{ trans('users.breadcrumb.admins') }}
                </el-breadcrumb-item>
            </el-breadcrumb>
        </el-header>
        <el-main>
            <el-form :inline="true">
                <el-form-item label="Search:">
                    <el-input prefix-icon="el-icon-search" @keyup.native="performSearch" v-model="searchQuery" placeholder="Keywords...">
                    </el-input>
                </el-form-item>
                <el-form-item label="Status:">
                    <el-select @change="performStatusFilter" v-model="statusFilter">
                        <el-option value="All">All</el-option>
                        <el-option value="Active">Active</el-option>
                        <el-option value="Not Activated">Not Activated</el-option>
                        <el-option value="Suspended">Suspended</el-option>
                    </el-select>
                </el-form-item>
                <router-link :to="{name: 'admin.user.admin-users.create'}">
                    <el-button type="primary" class="el-button--big pull-right">
                        {{ trans('users.button.new-admin') }}
                    </el-button>
                </router-link>
            </el-form>
            <el-table
                    :data="data"
                    stripe
                    ref="adminTable"
                    v-loading.body="tableIsLoading"
                    @sort-change="handleSortChange">
                <el-table-column prop="id" label="Id" width="75" sortable="custom">
                </el-table-column>
                <el-table-column prop="first_name" :label="trans('users.table.full-name')" sortable="custom">
                    <template slot-scope="scope">
                        <a @click.prevent="goToEdit(scope)" href="#">
                            {{ scope.row.fullname }}
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="email" :label="trans('users.table.email')" sortable="custom">
                    <template slot-scope="scope">
                        <a @click.prevent="goToEdit(scope)" href="#">
                            {{ scope.row.email }}
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" :label="trans('core.table.created at')" sortable="custom">
                </el-table-column>
                <el-table-column prop="status" :label="trans('users.table.status')" width="130">
                    <template slot-scope="scope">
                          <el-tag :type="scope.row.status.class" size=" mini" close-transition titl>
                              {{ scope.row.status.name }}
                          </el-tag>
                          <el-tag type="danger" size=" mini" close-transition titl v-if="scope.row.suspended" style="margin-top: 10px;">
                              {{ trans('users.suspended') }}
                          </el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="actions" :label="trans('core.table.actions')" width="180">
                    <template slot-scope="scope">
                        <el-dropdown split-button type="primary">
                            Actions
                            <el-dropdown-menu slot="dropdown">
                                <edit-table-item :to="{name: 'admin.user.admin-users.edit', params: {id: scope.row.id}}">
                                </edit-table-item>
                                <activate-table-item :scope="scope" :rows="data" v-if="!scope.row.activated">
                                </activate-table-item>
                                <suspend-table-item :scope="scope" :rows="data">
                                </suspend-table-item>
                                <delete-table-item :scope="scope" :rows="data">
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
</template>

<script>
    import axios from 'axios'
    import _ from 'lodash'
    import ShortcutHelper from '../../../../../Core/Assets/js/mixins/ShortcutHelper'

    export default {
        mixins: [ShortcutHelper],
        data() {
            return {
                data: [],
                meta: {
                    current_page: 1,
                    per_page: 10,
                },
                order_meta: {
                    order_by: '',
                    order: '',
                },
                links: {},
                searchQuery: '',
                statusFilter: 'All',
                tableIsLoading: false,
            };
        },
        methods: {
            fetchAdminUsers(customProperties) {
                const properties = {
                    page: this.meta.current_page,
                    per_page: this.meta.per_page,
                    order_by: this.order_meta.order_by,
                    order: this.order_meta.order,
                    search: this.searchQuery,
                    status: this.statusFilter,
                };

                this.tableIsLoading = true

                axios.get(route('api.user.admin.admin-users', _.merge(properties, customProperties)))
                    .then((response) => {
                        this.tableIsLoading = false
                        this.data  = response.data.data
                        this.meta  = response.data.meta.pagination
                        this.links = response.data.meta.pagination.links

                        this.order_meta.order_by = properties.order_by
                        this.order_meta.order = properties.order
                    });
            },
            handleSizeChange(event) {
                this.fetchAdminUsers({ per_page: event })
            },
            handleCurrentChange(event) {
                this.fetchAdminUsers({ page: event })
            },
            handleSortChange(event) {
                this.fetchAdminUsers({ order_by: event.prop, order: event.order })
            },
            performSearch: _.debounce(function (query) {
                this.fetchAdminUsers({ search: query.target.value })
            }, 300),
            performStatusFilter(status) {
                this.fetchAdminUsers({ status: status })
            },
            goToEdit(scope) {
                this.$router.push({ name: 'admin.user.admin-users.edit', params: { id: scope.row.id } })
            },
        },
        mounted() {
            this.fetchAdminUsers()
        },
    };
</script>
