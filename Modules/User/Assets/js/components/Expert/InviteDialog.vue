<template>
    <div class="invite-dialog">
        <el-button
            type="primary"
            class="trigger"
            @click="dialogVisible = true">
            Invite Experts
        </el-button>
        <el-dialog
            :visible.sync="dialogVisible"
            width="60%"
            :before-close="handleClose">
            <span slot="title">
                <h2 class="form-title">
                    Send invite <br/>
                    <small>Add recipients and an additional message to be appended to an invitation email.</small>
                </h2>
            </span>
            <el-form
                ref="inviteForm"
                :model="invite"
                v-loading="loading"
                label-position="top"
                @submit.native.prevent>
                <el-form-item label="Recipients" required :error="form.errors.first('recipients')">
                    <el-tag
                      :key="recipient"
                      v-for="recipient in invite.recipients"
                      closable
                      :disable-transitions="false"
                      @close="removeRecipient(recipient)">
                      {{ recipient }}
                    </el-tag>
                    <el-input
                      class="input-new-recipient"
                      v-if="recipientInputVisible"
                      v-model="invite.recipient"
                      ref="saveRecipientInput"
                      size="mini"
                      @keyup.enter.native="handleRecipientInputConfirm">
                    </el-input>
                    <el-button v-else class="button-new-recipient" size="small" @click="activateRecipientInput">+ New Recipient</el-button>
                    <i v-if="recipientInputVisible" @click="addRecipient" style="cursor:pointer;" class="el-icon-circle-plus"></i>
                </el-form-item>
                <div class="info">
                    <i class="el-icon-info" /> Please add email addresses as your recipients and press enter to add to the list.
                </div>
                <el-form-item label="Message" :error="form.errors.first('message')">
                    <el-input
                        type="textarea"
                        :rows="4"
                        v-model="invite.message"
                        :maxlength="maxCharactersCount"
                        placeholder="Add your message to the recipients"
                        name="message" />
                </el-form-item>
                <p class="remaining pull-right">
                    <strong>{{ charactersRemaining }}</strong> characters remaining
                </p>
            </el-form>
            <!-- End Main body -->

            <span slot="footer" class="dialog-footer">
                <el-button @click="handleClose">Cancel</el-button>
                <el-button type="primary" native-type="submit" @click="handleSubmit" :disabled="loading">{{ this.invite.recipients.length > 1 ? 'Send Invites' : 'Send Invite' }}</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<style lang="scss">
    .invite-dialog {
        .trigger {
            float: right;
            margin-bottom: 30px;
        }

        .el-tag + .el-tag {
            margin-left: 10px;
        }

        .button-new-recipient {
            margin-left: 10px;
            height: 32px;
            line-height: 30px;
            padding-top: 0;
            padding-bottom: 0;
        }

        .input-new-recipient {
            width: 150px;
            margin-left: 10px;
        }

        .el-dialog__body {
            padding-top: 0px;
        }

        .info {
            margin-top: 10px;
            margin-bottom: 10px;
            .el-icon-info {
                color: #286BA2;
            }
        }
    }
</style>

<script>
    import Form from 'form-backend-validation'

    export default {
        data() {
            return {
                dialogVisible: false,
                recipientInputVisible: false,
                form: new Form(),
                invite: {
                    recipient: null,
                    recipients: [],
                    message: ''
                },
                maxCharactersCount: 500,
                loading: false
            }
        },
        computed: {
            charactersRemaining() {
                if (this.invite.message) {
                    return this.maxCharactersCount - this.invite.message.length
                }
                return this.maxCharactersCount
            }
        },
        methods: {
            handleClose(done) {
                this.$confirm('Are you sure to close this dialog?')
                .then(_ => {
                    this.dialogVisible = false
                    this.resetForm()
                })
                .catch(_ => {})
            },
            activateRecipientInput () {
                this.recipientInputVisible = true
                this.$nextTick(_ => {
                  this.$refs.saveRecipientInput.$refs.input.focus()
                });
            },
            handleRecipientInputBlur () {
                if (!this.invite.recipient || this.invite.recipient == "") {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Please enter a recipient.',
                    })
                }
            },
            handleRecipientInputConfirm () {
                if (!this.invite.recipient || this.invite.recipient == "") {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Please enter a recipient.',
                    })
                } else if (/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/.test(this.invite.recipient) == false) {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Please enter a valid email address.',
                    })
                } else if (this.invite.recipients.includes(this.invite.recipient)) {
                    this.$notify.error({
                        title: 'Error',
                        message: 'Recipient already exists.',
                    })
                } else {
                    this.invite.recipients.push(this.invite.recipient)
                    this.recipientInputVisible = false
                    this.invite.recipient = null
                }
            },
            addRecipient(){
              this.handleRecipientInputConfirm () ;
              this.recipientInputVisible = true
            },
            removeRecipient (recipient) {
                this.invite.recipients.splice(this.invite.recipients.indexOf(recipient), 1);
            },
            handleSubmit() {
                this.loading = true
                this.form.withData(this.invite).post(route('api.user.admin.experts.invite'))
                    .then((response) => {
                        this.loading = false
                        if (response.errors) {
                            this.$notify.error({
                                title: 'Error',
                                message: response.message,
                            })
                        } else {
                            this.dialogVisible = false
                            this.$notify.success({
                                title: 'Success',
                                message: response.message,
                            })
                            this.resetForm()
                        }
                    })
                    .catch((error) => {
                        this.loading = false
                        this.$notify.error({
                            title: 'Error',
                            message: 'There are some errors in the form.',
                        })
                    })
            },
            resetForm() {
                this.form.reset()
                this.recipientInputVisible = false
                this.invite = {
                    recipient: null,
                    recipients: [],
                    message: ''
                }
            }
        }
    }
</script>
