<template>
    <el-container>
        <el-header>
            <h1>{{ trans('users.title.new-admin') }}</h1>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{name: 'admin.dashboard.index'}">{{ trans('core.breadcrumb.home') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.admin-users'}">{{ trans('users.title.admin users') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.admin-users.create'}">{{ trans(`users.breadcrumb.new-admin`) }}
                </el-breadcrumb-item>
            </el-breadcrumb>
        </el-header>
        <el-main>
            <el-row :gutter="20">
                <el-col :span="12" :offset="6">
                    <el-card>
                        <el-form ref="form"
                                 :model="user"
                                 label-width="120px"
                                 label-position="top"
                                 v-loading="loading"
                                 @keydown="form.errors.clear($event.target.name);">
                            <el-form-item :label="trans('users.form.first-name')"
                                          :class="{'el-form-item is-error': form.errors.has('first_name') }">
                                <el-input v-model="user.first_name"></el-input>
                                <div class="el-form-item__error" v-if="form.errors.has('first_name')"
                                 v-text="form.errors.first('first_name')"></div>
                            </el-form-item>
                            <el-form-item :label="trans('users.form.last-name')"
                                          :class="{'el-form-item is-error': form.errors.has('last_name') }">
                                <el-input v-model="user.last_name"></el-input>
                                <div class="el-form-item__error" v-if="form.errors.has('last_name')"
                                         v-text="form.errors.first('last_name')"></div>
                            </el-form-item>
                            <el-form-item :label="trans('users.form.email')"
                                          :class="{'el-form-item is-error': form.errors.has('email') }">
                                <el-input v-model="user.email"></el-input>
                                <div class="el-form-item__error" v-if="form.errors.has('email')"
                                         v-text="form.errors.first('email')"></div>
                            </el-form-item>
                            <el-form-item :label="trans('users.form.role')"
                                          :class="{'el-form-item is-error': form.errors.has('role') }">
                                <el-select v-model="user.role" placeholder="Select">
                                    <el-option v-for="role in roles"
                                        :key="role.id"
                                        :label="role.name"
                                        :value="role.id">
                                    </el-option>
                                </el-select>
                                <div class="el-form-item__error" v-if="form.errors.has('role')"
                                         v-text="form.errors.first('role')"></div>
                            </el-form-item>
                            <el-form-item :label="trans('users.form.add-type')">
                                <el-radio-group v-model="user.add_type">
                                    <el-radio label="invite">Send Invite</el-radio>
                                    <el-radio label="password">Specify Password</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <div class="password-fields" v-show="user.add_type == 'password'">
                                <el-form-item :label="trans('users.form.password')"
                                          :class="{'el-form-item is-error': form.errors.has('password') }">
                                    <el-input v-model="user.password" type="password"></el-input>
                                    <div class="el-form-item__error" v-if="form.errors.has('password')"
                                         v-text="form.errors.first('password')"></div>
                                </el-form-item>
                                <el-form-item :label="trans('users.form.password-confirmation')"
                                          :class="{'el-form-item is-error': form.errors.has('password_confirmation') }">
                                    <el-input v-model="user.password_confirmation" type="password"></el-input>
                                    <div class="el-form-item__error" v-if="form.errors.has('password_confirmation')"
                                         v-text="form.errors.first('password_confirmation')"></div>
                                </el-form-item>
                            </div>
                            <el-form-item class="el-form-item-buttons">
                                <el-button type="success" class="pull-right" @click="onSubmit()" :loading="loading">
                                    {{ trans('core.button.create') }}
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
    import ShortcutHelper from '../../../../../Core/Assets/js/mixins/ShortcutHelper';

    export default {
        mixins: [ShortcutHelper],
        props: {
            locales: { default: null },
        },
        data() {
            return {
                user: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    role: null,
                    add_type: 'invite',
                    client_id: 1
                },
                roles: {},
                form: new Form(),
                loading: false
            };
        },
        methods: {
            onSubmit() {
                this.form = new Form(this.user);
                this.loading = true;

                this.form.post(route('api.user.admin.admin-users.store'))
                    .then((response) => {
                        this.loading = false;
                        this.$message({
                            type: 'success',
                            message: response.message,
                        });
                        this.$router.push({ name: 'admin.user.admin-users' });
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
                this.$router.push({ name: 'admin.user.admin-users' });
            },
            fetchRoles() {
                this.loading = true;
                axios.get(route('api.user.admin.admin-users.roles'))
                    .then((response) => {
                        this.loading = false;
                        this.roles = response.data.data;
                    });
            },
        },
        mounted() {
            this.fetchRoles();
        },
    };
</script>
