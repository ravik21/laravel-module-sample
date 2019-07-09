<template>
    <el-container>
        <el-header>
            <h1>{{ trans(`users.${pageTitle}`) }} <small>{{ user.first_name }} {{ user.last_name }}</small></h1>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{name: 'admin.dashboard.index'}">{{ trans('core.breadcrumb.home') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.all-users'}">{{ trans('users.breadcrumb.all users') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.all-users.create'}">{{ trans(`users.${pageTitle}`) }}
                </el-breadcrumb-item>
            </el-breadcrumb>
        </el-header>
        <el-main>
            <el-row :gutter="20">
                <el-col :xl="18" :lg="18" :md="24" :sm="24" :xs="24" class="user-form">
                    <el-card>
                        <el-form ref="form"
                                 :model="user"
                                 label-width="120px"
                                 label-position="top"
                                 v-loading.body="loading"
                                 @keydown="form.errors.clear($event.target.name);">
                                <el-tabs>
                                    <el-tab-pane :label="trans('users.tabs.data')">
                                        <span slot="label"
                                              :class="{'error' : form.errors.any()}">
                                            {{ trans('users.tabs.data') }}
                                        </span>
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
                                        <el-form-item :label="trans('users.form.is activated')"
                                                      :class="{'el-form-item is-error': form.errors.has('activated') }">
                                            <el-checkbox v-model="user.activated">Activated</el-checkbox>
                                            <div class="el-form-item__error" v-if="form.errors.has('activated')"
                                                 v-text="form.errors.first('activated')"></div>
                                        </el-form-item>
                                        <div v-if="user.is_new">
                                            <el-form-item :label="trans('users.form.password')"
                                                          :class="{'el-form-item is-error': form.errors.has('password') }">
                                                <el-input v-model="user.password"
                                                          type="password"></el-input>
                                                <div class="el-form-item__error" v-if="form.errors.has('password')"
                                                     v-text="form.errors.first('password')"></div>
                                            </el-form-item>
                                            <el-form-item :label="trans('users.form.password-confirmation')"
                                                          :class="{'el-form-item is-error': form.errors.has('password_confirmation') }">
                                                <el-input v-model="user.password_confirmation"
                                                          type="password"></el-input>
                                                <div class="el-form-item__error" v-if="form.errors.has('password_confirmation')"
                                                     v-text="form.errors.first('password_confirmation')"></div>
                                            </el-form-item>
                                        </div>
                                    </el-tab-pane>
                                    <el-tab-pane :label="trans('users.tabs.roles')">
                                        <el-form-item :label="trans('users.form.roles')"
                                                      :class="{'el-form-item is-error': form.errors.has('roles') }">
                                            <el-select v-model="user.roles" multiple placeholder="Select">
                                                <el-option
                                                        v-for="role in roles"
                                                        :key="role.id"
                                                        :label="role.name"
                                                        :value="role.id">
                                                </el-option>
                                            </el-select>
                                            <div class="el-form-item__error" v-if="form.errors.has('roles')"
                                                 v-text="form.errors.first('roles')"></div>
                                        </el-form-item>
                                    </el-tab-pane>
                                    <el-tab-pane :label="trans('users.tabs.permissions')">
                                        <permissions v-model="user.permissions"
                                                            :current-permissions="user.permissions"></permissions>
                                    </el-tab-pane>
                                    <el-tab-pane :label="trans('users.tabs.new password')" v-if="! user.is_new">
                                        <div v-if="! user.is_new">
                                            <div class="col-md-6">
                                                <el-form-item :label="trans('users.form.password')"
                                                              :class="{'el-form-item is-error': form.errors.has('password') }">
                                                    <el-input v-model="user.password"
                                                              type="password"></el-input>
                                                    <div class="el-form-item__error" v-if="form.errors.has('password')"
                                                         v-text="form.errors.first('password')"></div>
                                                </el-form-item>
                                                <el-form-item :label="trans('users.form.password-confirmation')"
                                                              :class="{'el-form-item is-error': form.errors.has('password_confirmation') }">
                                                    <el-input v-model="user.password_confirmation"
                                                              type="password"></el-input>
                                                    <div class="el-form-item__error" v-if="form.errors.has('password_confirmation')"
                                                         v-text="form.errors.first('password_confirmation')"></div>
                                                </el-form-item>
                                            </div>
                                            <div class="col-md-6">
                                                <h4>{{ trans('users.tabs.or send reset password mail') }}</h4>
                                                <el-button type="info"
                                                           :loading="resetEmailIsLoading"
                                                           @click="sendResetEmail">
                                                    {{ trans('users.send reset password email') }}
                                                </el-button>
                                            </div>
                                        </div>
                                    </el-tab-pane>
                                </el-tabs>
                            <el-form-item class="el-form-item-buttons">
                                <el-button type="success" @click="onSubmit()" :loading="loading" class="pull-right">
                                    {{ $route.params.id ? trans('core.button.update') : trans('core.button.create')  }}
                                </el-button>
                                <el-button @click="onCancel()" class="pull-right">
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
<style>
@media (max-width: 385px) {
  .el-main {
    width:100%;
  }
}
</style>
<script>
    import axios from 'axios';
    import Form from 'form-backend-validation';
    import ShortcutHelper from '../../../../../Core/Assets/js/mixins/ShortcutHelper';
    import Permissions from '../Role/Permissions.vue';

    export default {
        mixins: [ShortcutHelper],
        components: {
            'permissions': Permissions,
        },
        props: {
            locales: { default: null },
            pageTitle: { default: null, String },
        },
        data() {
            return {
                user: {
                    first_name: '',
                    last_name: '',
                    permissions: {},
                    roles: {},
                    is_new: false,
                },
                roles: {},
                form: new Form(),
                loading: false,
                resetEmailIsLoading: false,
            };
        },
        methods: {
            onSubmit() {
                this.form = new Form(this.user);
                this.loading = true;

                this.form.post(this.getRoute())
                    .then((response) => {
                        this.loading = false;
                        this.$message({
                            type: 'success',
                            message: response.message,
                        });
                        this.$router.push({ name: 'admin.user.all-users' });
                    })
                    .catch((error) => {
                        console.log(error);
                        this.loading = false;
                        this.$notify.error({
                            title: 'Error',
                            message: 'There are some errors in the form.',
                        });
                    });
            },
            onCancel() {
                this.$router.push({ name: 'admin.user.all-users' });
            },
            fetchUser() {
                this.loading = true;

                axios.get(route('api.user.admin.all-users.show', this.$route.params.id))
                    .then((response) => {
                        this.loading = false;
                        this.user = response.data;
                    });
            },
            getRoute() {
                if (this.$route.params.id !== undefined) {
                    return route('api.user.admin.all-users.update', this.$route.params.id);
                }

                return route('api.user.admin.all-users.store');
            },
            fetchRoles() {
                axios.get(route('api.user.admin.roles'))
                    .then((response) => {
                        this.roles = response.data.data;
                    });
            },
            sendResetEmail() {
                this.resetEmailIsLoading = true;
                axios.get(route('api.user.admin.users.resetPassword', this.$route.params.id))
                    .then((response) => {
                        this.resetEmailIsLoading = false;
                        this.$notify.success({
                            title: 'Success',
                            message: response.data.message,
                        });
                    });
            },
        },
        mounted() {
            if (this.$route.params.id !== undefined) {
                this.fetchUser();
            }

            this.fetchRoles();
        },
    };
</script>

<style lang="scss">
  .user-form{
    @media(min-width: 1025px){
      padding: 0 0px 0 180px !important;
    }
  }
</style>
