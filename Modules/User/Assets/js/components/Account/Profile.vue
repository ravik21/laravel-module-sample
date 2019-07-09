<template>
    <el-container>
        <el-header>
            <h1>{{ trans('users.title.edit-profile') }}</h1>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{name: 'admin.dashboard.index'}">{{ trans('core.breadcrumb.home') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.users.account'}">{{ trans('users.breadcrumb.edit-profile') }}
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
                            <label class="el-form-item__label">Avatar</label>
                            <el-upload
                              drag
                              action=""
                              class="avatar-uploader"
                              :http-request="uploadAvatar"
                              :show-file-list="false"
                              name="avatar"
                              :before-upload="beforeAvatarUpload"
                              v-loading="avatarLoading">
                              <img v-if="user.avatar" :src="user.avatar" class="avatar">
                              <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                            <el-form-item :label="trans('users.form.first-name')" :class="{'el-form-item is-error': form.errors.has('first_name') }">
                                <el-input v-model="user.first_name"></el-input>
                                <div class="el-form-item__error" v-if="form.errors.has('first_name')" v-text="form.errors.first('first_name')"></div>
                            </el-form-item>
                            <el-form-item :label="trans('users.form.last-name')" :class="{'el-form-item is-error': form.errors.has('last_name') }">
                                <el-input v-model="user.last_name"></el-input>
                                <div class="el-form-item__error" v-if="form.errors.has('last_name')" v-text="form.errors.first('last_name')"></div>
                            </el-form-item>
                            <el-form-item :label="trans('users.form.email')" :class="{'el-form-item is-error': form.errors.has('email') }">
                                <el-input v-model="user.email"></el-input>
                                <div class="el-form-item__error" v-if="form.errors.has('email')" v-text="form.errors.first('email')"></div>
                            </el-form-item>
                            <el-form-item :label="trans('users.form.new password')" :class="{'el-form-item is-error': form.errors.has('password') }">
                                <el-input v-model="user.password" type="password"></el-input>
                                <div class="el-form-item__error" v-if="form.errors.has('password')" v-text="form.errors.first('password')"></div>
                            </el-form-item>
                            <el-form-item :label="trans('users.form.new password confirmation')" :class="{'el-form-item is-error': form.errors.has('password_confirmation') }">
                                <el-input v-model="user.password_confirmation" type="password"></el-input>
                                <div class="el-form-item__error" v-if="form.errors.has('password_confirmation')" v-text="form.errors.first('password_confirmation')"></div>
                            </el-form-item>
                            <el-form-item class="el-form-item-buttons">
                                <el-button type="success" class="pull-right" @click="onSubmit()" :loading="loading">
                                    {{ trans('core.button.update') }}
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

<style>
   .avatar-uploader {
    text-align: center;
  }

  .avatar-uploader .el-upload-dragger {
    width: 140px;
    height: 140px;
  }

  .avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }

  .avatar-uploader .el-upload .el-upload__input {
    display: none;
  }

  .avatar-uploader .el-upload:hover {
    border-color: #4F307E;
  }

  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 140px;
    height: 140px;
    line-height: 140px;
    text-align: center;
  }

  .avatar {
    width: 120px;
    height: 120px;
    border-radius: 60px;
    display: block;
    margin: 0 auto;
    margin: 10px auto;
  }
</style>

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
                    password: '',
                    password_confirmation: ''
                },
                form: new Form(),
                loading: false,
                avatarLoading: false
            };
        },
        methods: {
            onSubmit() {
                this.form = new Form(this.user);
                this.loading = true;

                this.form.post(route('api.account.admin.profile.update'))
                    .then((response) => {
                        this.loading = false;
                        this.$message({
                            type: 'success',
                            message: response.message,
                        });
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
                this.$router.push({ name: 'admin.dashboard.index' })
            },
            fetchUserProfile() {
                this.loading = true;
                axios.get(route('api.account.admin.profile.show'))
                    .then((response) => {
                        this.loading = false;
                        this.user = response.data;
                    });
            },
            uploadAvatar(avatar) {
                this.avatarLoading = true;
                var data = new FormData(); data.append(avatar.filename, avatar.file);
                var config = { headers: { 'content-type': 'multipart/form-data' } };
                axios.post(route('api.account.admin.profile.avatar'), data, config)
                    .then((response) => {
                        this.avatarLoading = false;
                        this.user.avatar = response.data.avatar;
                        this.$message({
                            type: 'success',
                            message: response.data.message,
                        });
                        let avatar = $('.logout').find('.avatar');
                        if (avatar) {
                            avatar.attr("src", response.data.avatar);
                        }
                    });
            },
            beforeAvatarUpload(file) {
                const isJPGorPNG = file.type === 'image/jpeg' || file.type === 'image/png';
                const isLt3M     = file.size / 1024 / 1024 < 3;

                if (!isJPGorPNG) {
                  this.$message.error('Avatar must be in JPG or PNG format!');
                }
                if (!isLt3M) {
                  this.$message.error('Avatar picture size can not exceed 3MB!');
                }

                return isJPGorPNG && isLt3M;
            }
        },
        mounted() {
            this.fetchUserProfile();
        },
    };
</script>
