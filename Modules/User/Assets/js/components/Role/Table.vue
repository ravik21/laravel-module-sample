<template>
    <el-container>
        <el-header>
            <h1>{{ trans('roles.title.roles') }}</h1>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <a href="/backend">{{ trans('core.breadcrumb.home') }}</a>
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.roles.index'}">{{ trans('roles.title.roles') }}
                </el-breadcrumb-item>
            </el-breadcrumb>
        </el-header>
        <el-main>
            <el-form :inline="true">
                <el-row>
                    <el-col :span="24">
                        <el-form-item :label="trans('core.filter.search')">
                            <el-input prefix-icon="el-icon-search" @keyup.native="performSearch" v-model="searchQuery" placeholder="Keywords...">
                            </el-input>
                        </el-form-item>
                        <router-link :to="{name: 'admin.user.roles.create'}">
                            <el-button type="primary" class="el-button--big pull-right">
                                {{ trans('roles.button.create role') }}
                            </el-button>
                        </router-link>
                    </el-col>
                </el-row>
            </el-form>

            <el-table
                    :data="data"
                    stripe
                    style="width: 100%"
                    ref="pageTable"
                    v-loading.body="tableIsLoading"
                    @sort-change="handleSortChange">
                <el-table-column prop="id" label="Id" width="75" sortable="custom">
                </el-table-column>
                <el-table-column prop="name" :label="trans('roles.table.name')" sortable="custom">
                    <template slot-scope="scope">
                        <a @click.prevent="goToEdit(scope)" href="#">
                            {{ scope.row.name }}
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="slug" :label="trans('roles.table.slug')" sortable="custom">
                    <template slot-scope="scope">
                        <a @click.prevent="goToEdit(scope)" href="#">
                            {{ scope.row.slug }}
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" :label="trans('core.table.created at')" sortable="custom">
                </el-table-column>
                <el-table-column prop="actions" :label="trans('core.table.actions')" width="180">
                    <template slot-scope="scope">
                        <el-dropdown split-button type="primary">
                            Actions
                            <el-dropdown-menu slot="dropdown">
                                <edit-table-item :to="{name: 'admin.user.roles.edit', params: {id: scope.row.id}}">
                                </edit-table-item>
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
    import axios from 'axios';
    import _ from 'lodash';
    import ShortcutHelper from '../../../../../Core/Assets/js/mixins/ShortcutHelper';

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
                tableIsLoading: false,
            };
        },
        methods: {
            fetchRoles(customProperties) {
                this.tableIsLoading = true;

                const properties = {
                    page: this.meta.current_page,
                    per_page: this.meta.per_page,
                    order_by: this.order_meta.order_by,
                    order: this.order_meta.order,
                    search: this.searchQuery,
                };

                axios.get(route('api.user.admin.roles', _.merge(properties, customProperties)))
                    .then((response) => {
                        this.tableIsLoading = false;
                        this.data = response.data.data;
                        this.meta = response.data.meta;
                        this.links = response.data.links;

                        this.order_meta.order_by = properties.order_by;
                        this.order_meta.order = properties.order;
                    });
            },
            handleSizeChange(event) {
                this.fetchRoles({ per_page: event });
            },
            handleCurrentChange(event) {
                this.fetchRoles({ page: event });
            },
            handleSortChange(event) {
                this.fetchRoles({ order_by: event.prop, order: event.order });
            },
            performSearch: _.debounce(function (query) {
                this.fetchRoles({ search: query.target.value });
            }, 300),
            goToEdit(scope) {
                this.$router.push({ name: 'admin.user.roles.edit', params: { id: scope.row.id } });
            },
        },
        mounted() {
            this.fetchRoles();
        },
    };
</script>
