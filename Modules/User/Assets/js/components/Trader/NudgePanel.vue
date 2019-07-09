<template>
    <div>
        <el-container id="nudge-panel-container" v-loading="tableIsLoading">
            <el-footer>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <p>Filter results <span class="user-count">{{ this.applicableUsers }}</span> users</p>
                    </el-col>
                    <!-- <el-col :span="6">
                        <p>Want to stimulate an action ?</p>
                    </el-col>
                    <el-col :span="10">
                        <el-tooltip class="item" effect="dark" content="Note: You can only send a nudge when filtering users by status 'Active'" placement="top-start" :disabled="canSendNudge">
                            <div>
                                <el-button class="el-button--big" @click="dialogVisible = true" :disabled="!canSendNudge">
                                    Create a Nudge
                                </el-button>
                            </div>
                        </el-tooltip>
                    </el-col> -->
                </el-row>
            </el-footer>
        </el-container>
        <el-dialog
            :visible.sync="dialogVisible"
            width="60%"
            :before-close="handleClose"
            >
            <span slot="title">
                <h2 class="form-title">Create New Nudge <br/>
                    <small>A nudge is a way of stimulating an action by users</small>
                </h2>
            </span>
            <!-- Main body -->
            <el-form
                ref="nudgeForm"
                :model="nudge"
                v-loading="loading"
                label-position="top">
                <el-form-item label="Schedule?"
                              :error="form.errors.first('scheduled_at_date') || form.errors.first('scheduled_at_time') || form.errors.first('scheduled_at')">
                    <el-date-picker v-model="nudge.scheduled_at_date"
                                    type="date"
                                    placeholder="Pick a day"
                                    format="dd/MM/yyyy"
                                    value-format="yyyy-MM-dd"
                                    name="scheduled_at_date"
                                    :editable=false
                                    :picker-options="scheduledAtDatePickerOptions"
                                    @focus="form.errors.clear('scheduled_at_date')">
                    </el-date-picker>

                    <el-time-select v-model="nudge.scheduled_at_time"
                                    :picker-options="scheduledAtTimePickerOptions"
                                    placeholder="Pick a time"
                                    name="scheduled_at_time"
                                    @focus="form.errors.clear('scheduled_at_time')">
                    </el-time-select>
                    <div class="info">
                        <i class="el-icon-info" /> Leave blank for immediate send
                    </div>
                </el-form-item>
                <el-form-item label="Nudge message" required :error="form.errors.first('message')">
                    <el-input
                        type="textarea"
                        :rows="4"
                        v-model="nudge.message"
                        :maxlength="maxCharactersCount"
                        name="message">
                    </el-input>
                </el-form-item>
                <p class="remaining pull-right">
                    <strong>{{ charactersRemaining }}</strong> characters remaining
                </p>
            </el-form>
            <div class="info">
                <i class="el-icon-info" /> Nudges will appear as notifications to the users
            </div>
            <!-- End Main body -->

            <span slot="footer" class="dialog-footer">
                <el-button @click="handleClose">Cancel</el-button>
                <el-button type="primary" @click="handleSubmit" :disabled="loading">Send nudge</el-button>
            </span>

        </el-dialog>
    </div>
</template>

<style lang="scss">
    #nudge-panel-container {
        position: fixed;
        bottom: 0px;
        width: 100%;
        background: rgb(225, 223, 223);
        color: #4A4A4A;

        p{
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;

            .user-count {
                color: #DD001D;
                font-size: 22px;
            }
        }

        .el-button {
            margin-top: 13px;
        }
    }
</style>

<script>
    import Form from 'form-backend-validation';

    export default {
        props: {
            groupId: {required: true},
            applicableUsers: {required: true},
            tableIsLoading: {required: false},
            canSendNudge: {required: true},
            search: {required: false}
        },
        data() {
            return {
                dialogVisible: false,
                form: new Form(),
                nudge: {
                    message: null,
                    scheduled_at_time: null,
                    scheduled_at_date: null,
                    group: this.groupId,
                    search: this.search
                },
                scheduledAtDatePickerOptions: {
                    disabledDate(time) {
                        return time.getTime() < moment().subtract(1, 'day');
                    },
                },
                maxCharactersCount: 140,
                loading: false,
            };
        },
        computed: {
            charactersRemaining() {
                if (this.nudge.message) {
                    return this.maxCharactersCount - this.nudge.message.length
                }
                return this.maxCharactersCount;
            },
            scheduledAtTimePickerOptions() {
                return {
                    start: this.startTime,
                    step: '00:15',
                    end: '23:45',
                }
            },
            startTime() {
                if (this.nudge.scheduled_at_date && moment(this.nudge.scheduled_at_date).isSame(moment(), 'day')) {
                    let minutes = Math.ceil(moment().minute() / 15) * 15;
                    if (minutes == 60) {
                        return moment().add(1, 'hour').format('H:') + '00';
                    }
                    return moment().format('H:') + minutes;
                }
                return '00:00';
            }
        },
        methods: {
            handleClose(done) {
                this.$confirm('Are you sure to close this dialog?')
                .then(_ => {
                    this.dialogVisible = false;
                    this.resetForm();
                })
                .catch(_ => {});
            },
            handleSubmit() {
                this.loading = true;
                this.nudge.group = this.groupId;
                this.nudge.search = this.search;

                this.form.withData(this.nudge).post(route('api.user.admin.users.nudge'))
                    .then((response) => {
                        this.loading = false;
                        if (response.errors) {
                            this.$notify.error({
                                title: 'Error',
                                message: response.message,
                            });
                        } else {
                            this.dialogVisible = false;
                            this.$notify.success({
                                title: 'Success',
                                message: response.message,
                            });
                            this.resetForm();
                        }
                    })
                    .catch((error) => {
                        this.loading = false;
                        this.$notify.error({
                            title: 'Error',
                            message: 'There are some errors in the form.',
                        });
                    });
            },
            resetForm() {
                this.nudge = {
                    message: null,
                    scheduled_at_time: null,
                    scheduled_at_date: null
                };
            }
        }
    };
</script>
