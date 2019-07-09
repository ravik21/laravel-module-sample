<template>
    <el-container>
        <el-header>
            <h1>{{ trans(`roles.${pageTitle}`) }} <small>{{ role.name }}</small></h1>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <a href="/admin">{{ trans('core.breadcrumb.home') }}</a>
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.roles.index'}">{{ trans('roles.title.roles') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.roles.create'}">{{ trans(`roles.${pageTitle}`) }}
                </el-breadcrumb-item>
            </el-breadcrumb>
        </el-header>
        <el-main>
            <el-row :gutter="20">
                <el-col :span="18" :offset="3">
                    <el-card>
                        <el-form ref="form"
                                 :model="role"
                                 label-width="120px"
                                 label-position="top"
                                 v-loading.body="loading"
                                 @keydown.native="form.errors.clear($event.target.name);">
                            <el-tabs>
                                <el-tab-pane :label="trans('roles.tabs.data')">
                                    <el-form-item :label="trans('roles.form.name')" :error="form.errors.first('name')">
                                        <el-input v-model="role.name" name="name"></el-input>
                                    </el-form-item>

                                    <el-form-item :label="trans('roles.form.slug')" :error="form.errors.first('slug')">
                                        <el-input v-model="role.slug" name="slug">
                                            <el-button slot-scope="prepend" @click="generateSlug">Generate</el-button>
                                        </el-input>
                                    </el-form-item>
                                </el-tab-pane>
                                <el-tab-pane :label="trans('roles.tabs.permissions')">
                                    <permissions v-model="role.permissions"
                                                        is-role
                                                        :current-permissions="role.permissions"></permissions>
                                </el-tab-pane>
                                <el-tab-pane :label="trans('users.title.users')">
                                    <h3>{{ trans('roles.title.users-with-roles') }}</h3>
                                    <ul>
                                        <li v-for="user in role.users" :key="user.id">{{ user.fullname }} ({{ user.email }})</li>
                                    </ul>
                                </el-tab-pane>
                            </el-tabs>
                            <el-form-item class="el-form-item-buttons">
                                <el-button type="success" class="pull-right" @click="onSubmit()" :loading="loading">
                                    {{ trans(isNew ? 'core.button.create' : 'core.button.update') }}
                                </el-button>
                                <el-button class="pull-right" @click="onCancel()">
                                    {{ trans('core.button.cancel') }}
                                </el-button>
                            </el-form-item>
                        </el-form>
                    </el-card>
                </el-col>
            </el-row>
        </el-main>
    </el-container>
</template>

<script>
    import axios from 'axios';
    import Form from 'form-backend-validation';
    import Slugify from '../../../../../Core/Assets/js/mixins/Slugify';
    import ShortcutHelper from '../../../../../Core/Assets/js/mixins/ShortcutHelper';
    import Permissions from './Permissions.vue';

    export default {
        mixins: [Slugify, ShortcutHelper],
        components: {
            'permissions': Permissions,
        },
        props: {
            locales: { default: null },
            pageTitle: { default: null, String },
            isNew: { default: true, Boolean },
        },
        data() {
            return {
                role: {
                    name: '',
                    slug: '',
                    permissions: {},
                    users: {}
                },
                form: new Form(),
                loading: false,
            };
        },
        methods: {
            onSubmit() {
                this.loading = true;

                this.form.withData(this.role).post(this.getRoute())
                    .then((response) => {
                        this.loading = false;
                        this.$message({
                            type: 'success',
                            message: response.message,
                        });
                        this.$router.push({ name: 'admin.user.roles.index' });
                    })
                    .catch((error) => {
                        this.loading = false;
                        this.$notify.error({
                            title: 'Error',
                            message: 'There are some errors in the form.',
                        });
                    });
            },
            onCancel() {
                this.$router.push({ name: 'admin.user.roles.index' });
            },
            generateSlug() {
                this.role.slug = this.slugify(this.role.name);
            },
            fetchRole() {
                this.loading = true;
                axios.post(route('api.user.admin.roles.show', { id: this.$route.params.id }))
                    .then((response) => {
                        this.loading = false;
                        this.role = response.data.data;
                    });
            },
            getRoute() {
                if (this.$route.params.id !== undefined) {
                    return route('api.user.admin.roles.update', { id: this.$route.params.id });
                }
                return route('api.user.admin.roles.store');
            },
        },
        mounted() {
            if (this.$route.params.id !== undefined) {
                this.fetchRole();
            }
        },
    };
</script>
