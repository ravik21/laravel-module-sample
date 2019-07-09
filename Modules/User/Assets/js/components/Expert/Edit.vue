<template>
    <el-container>
        <el-header>
            <h1>{{ trans('users.title.edit-expert') }}</h1>
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{name: 'admin.dashboard.index'}">{{ trans('core.breadcrumb.home') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item :to="{name: 'admin.user.experts.index'}">{{ trans('users.breadcrumb.experts') }}
                </el-breadcrumb-item>
                <el-breadcrumb-item to="#">{{ trans('users.breadcrumb.edit-expert') }}
                </el-breadcrumb-item>
            </el-breadcrumb>
        </el-header>
        <el-main>
          <el-row :gutter="20">
              <el-col :xl="16" :lg="18" :md="18" :sm="18" :xs="24" class="user-form" :offset="3">
                    <el-card
                     v-loading="loading">
                        <el-form  :model="expertForm" ref="expertForm"
                        :rules="rules"
                        label-width="240px"
                        label-position="top"
                        id="expertForm">
                           <el-row>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                   <h4>YOUR DETAILS</h4>
                               </el-col>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                <el-form-item required :label="trans('users.form.first-name')"  prop ="first_name">
                                    <el-input v-model="expertForm.first_name"></el-input>
                                </el-form-item>
                              </el-col>
                              <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                <el-form-item required :label="trans('users.form.last-name')" prop ="last_name">
                                    <el-input v-model="expertForm.last_name"></el-input>
                                </el-form-item>
                              </el-col>
                              <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24" class="edit-expert-email">
                                <el-form-item required :label="trans('users.form.email')" prop ="email">
                                    <el-input v-model="expertForm.email"></el-input>
                                </el-form-item>
                              </el-col>
                              <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                <el-form-item required label="Timezone">
                                    <el-select v-model="expertForm.timezone" filterable placeholder="Select timezone">
                                        <el-option v-for="(timezone, key) in timezones" :value="timezone.timezone" :label="`${timezone.timezone} ${timezone.pretty_offset}`" :key="key"></el-option>
                                    </el-select>
                                </el-form-item>
                              </el-col>
                              <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                <el-form-item required label="Country">
                                    <el-select v-model="expertForm.country" filterable placeholder="Select country">
                                        <el-option v-for="(companyCountry, key) in companyCountries" :value="companyCountry.id" :label="companyCountry.name"  :key="key"></el-option>
                                    </el-select>
                                </el-form-item>
                              </el-col>
                              <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24" class="group">
                                <el-form-item label="Industry sector(s)" v-if="groups.length > 0">
                                  <el-select v-model="expertForm.group_ids" multiple placeholder="Select industry sectors">
                                    <el-option  v-for="group in groups" :value="group.id" :label="group.name" :key="group.id" />
                                  </el-select>
                                </el-form-item>
                              </el-col>
                              <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24" class="edit-profile-tags">
                                <el-form-item label="Areas of expertise(s)" v-if="tags.length > 0">
                                    <el-select v-model="expertForm.tag_ids" multiple placeholder="Select area of expertises">
                                        <el-option v-for="tag in tags" :value="tag.id" :label="tag.name" :key="tag.id" />
                                    </el-select>
                                </el-form-item>
                              </el-col>
                              <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                <el-form-item label="Are you a part of a company?">
                                  <div class="el-input">
                                    <el-radio v-model="expertForm.has_company" :label="true">Yes</el-radio>
                                    <el-radio v-model="expertForm.has_company" :label="false" @click.native="resetCompanyDetails">No</el-radio>
                                  </div>
                                </el-form-item>
                              </el-col>
                           </el-row>
                           <el-row v-if="expertForm.has_company">
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                   <h4>COMPANY DETAILS</h4>
                               </el-col>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                   <el-form-item required label="Name" prop="company_name">
                                       <el-input v-model="expertForm.company_name" placeholder="Company name" />
                                   </el-form-item>
                               </el-col>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                   <el-form-item label="Position" prop="company_position">
                                       <el-input v-model="expertForm.company_position" placeholder="Company position" />
                                   </el-form-item>
                               </el-col>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                   <el-form-item label="Street number or name" prop="company_number">
                                       <el-input v-model="expertForm.company_number" placeholder="Company street number or name" />
                                   </el-form-item>
                               </el-col>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                   <el-form-item label="Town" prop="company_town">
                                       <el-input v-model="expertForm.company_town" placeholder="Company town" />
                                   </el-form-item>
                               </el-col>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                   <el-form-item label="Region/State" prop="company_region">
                                       <el-input v-model="expertForm.company_region" placeholder="Company region/state" />
                                   </el-form-item>
                               </el-col>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                   <el-form-item label="Postcode" prop="company_postcode">
                                       <el-input v-model="expertForm.company_postcode" placeholder="Company postcode" />
                                   </el-form-item>
                               </el-col>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                   <el-form-item label="Vat Registered?" prop="company_vat_registered">
                                       <div class="el-input">
                                             <el-radio v-model="expertForm.company_vat_registered" :label="true">Yes</el-radio>
                                           <el-radio v-model="expertForm.company_vat_registered" :label="false" @click.native="resetVatNumber()">No</el-radio>
                                       </div>
                                   </el-form-item>
                               </el-col>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24" v-show="expertForm.company_vat_registered">
                                   <el-form-item label="Vat Number" prop="company_vat_no">
                                       <el-input v-model="expertForm.company_vat_no" placeholder="Company VAT Number" />
                                   </el-form-item>
                               </el-col>
                           </el-row>
                           <el-row>
                             <h4>OTHER DETAILS</h4>
                             <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                               <el-form-item required label="Hourly rate (in GBP)" prop="hour_rate">
                                 <div class="el-input">
                                   <vue-numeric currency="£" separator="," :precision="2" placeholder="Your hourly rate" v-model="expertForm.hour_rate" class="el-input__inner" />
                                 </div>
                               </el-form-item>
                             </el-col>
                             <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                               <el-form-item required label="Day rate (in GBP)" prop="day_rate">
                                   <div class="el-input">
                                       <vue-numeric currency="£" separator="," :precision="2" placeholder="Your day rate" v-model="expertForm.day_rate" class="el-input__inner" />
                                   </div>
                               </el-form-item>
                             </el-col>
                           </el-row>
                           <el-row>
                             <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                               <el-form-item :label="'Languages(s)'" prop="languages">
                                   <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 5 }" placeholder="Your language(s)" v-model="expertForm.languages" />
                               </el-form-item>
                             </el-col>
                           </el-row>
                           <el-row>
                             <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                               <el-form-item required label="Availability" prop="availability">
                                   <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 5 }" placeholder="Your availability" v-model="expertForm.availability" />
                               </el-form-item>
                             </el-col>
                           </el-row>
                           <el-row>
                               <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                                   <el-form-item required label="Education(s)" prop="education">
                                       <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 5 }" placeholder="Your education(s)" v-model="expertForm.education" />
                                   </el-form-item>
                               </el-col>
                           </el-row>
                           <el-row>
                             <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                               <el-form-item required label="Membership(s)" prop="memberships">
                                   <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 5 }" placeholder="Your membership(s)" v-model="expertForm.memberships" />
                               </el-form-item>
                             </el-col>
                           </el-row>
                           <el-row>
                             <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                               <el-form-item required label="Professional experience(s)" prop="professional_experience">
                                   <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 5 }" placeholder="Your professional experience(s)" v-model="expertForm.professional_experience" />
                               </el-form-item>
                             </el-col>
                           </el-row>
                           <el-row>
                             <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
                               <el-form-item required label="Past project(s)">
                                   <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 5 }" placeholder="Your past project(s)" v-model="expertForm.past_projects" />
                               </el-form-item>
                             </el-col>
                           </el-row>
                           <el-row class="submit text-center">
                             <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24" class="text-center">
                               <el-button id="submit" @click.native="submitForm('expertForm')" :loading="loading" class="text-white" type="success">Update</el-button>
                             </el-col>
                           </el-row>
                        </el-form>
                    </el-card>
                </el-col>
            </el-row>
        </el-main>
    </el-container>
</template>
<style>
#expertForm .el-select {
  width: 100%;
}
@media (max-width: 767px) {
  .user-form {
    margin:0px;
  }
}
@media (max-width: 340px) {
  .el-main {
    width:100%;
  }
}
</style>
<script>
    import axios from 'axios'
    import Form from 'form-backend-validation';

    export default {
      data() {
        let validateEmail = (rule, value, callback) => {
          let reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/
          if (value === '') {
            callback(new Error('Please input your email'))
          } else if (reg.test(value) == false) {
            callback(new Error('The email field must be a valid email address'))
          } else {
            callback()
          }
        };

        let validateCompanyName = (rule, value, callback) => {
            if (this.expertForm.has_company && (value === false || value === '')) {
                callback(new Error('Company name is required.'))
            } else {
                callback()
            }
        };
        return {
          expertForm: {
            id: this.$route.params.id,
            first_name: "",
            last_name: "",
            email: "",
            group_ids: [],
            tag_ids: [],
            timezone: "",
            hour_rate: "" ? parseInt("") : '',
            day_rate: "" ? parseInt("") : '',
            languages: "",
            availability: "",
            experience: "",
            past_projects: "",
            education: "",
            memberships: "",
            references: "",
            title: "",
            password: null,
            checkPass:null,
            terms: "",
            has_company: false,
            company_name: '',
            company_country_id: null,
            company_number: '',
            company_street: '',
            company_town: '',
            company_region: '',
            company_postcode: '',
            company_vat_registered: false,
            company_vat_no: '',
            company_position: '',
            country: '',
          },
          errors: {},
          form: new Form(),
          timezones: [],
          tags: [],
          groups: [],
          companyCode: 'ALEGRANT2018',
          companyCountries: [],
          loading: true,
          rules: {
            first_name: [
            { required: true, message: 'First name is required' },
            ],
            last_name: [
            { required: true, message: 'Surname is required' }
            ],
            email: [
                { validator: validateEmail, required: true }
            ],
            company_name: [
                { validator: validateCompanyName, required: true }
            ],
            timezone: [
            { required: true, message: 'Timezone is required' }
            ],
            hour_rate: [
            { required: true, message: 'Hourly rate is required' }
            ],
            day_rate: [
            { required: true, message: 'Day rate is required' }
            ],
            languages: [
            { required: true, message: 'Languages is required' }
            ],
            availability: [
            { required: true, message: 'Availability is required' }
            ],
            education: [
            { required: true, message: 'Education is required' }
            ],
            memberships: [
            { required: true, message: 'Memberships is required' }
            ],
            professional_experience: [
            { required: true, message: 'Professional experience is required' }
            ],
            past_projects: [
            { required: true, message: 'Past projects is required' }
          ]
          }
        };
      },
      beforeCreated() {
        this.loading = true;
      },
      methods: {
        getGroupTagsIds(entityIds, isTag){
          let ids = [];
          _.each(entityIds, (entity) => {
            ids.push(entity.id);
          });
          return ids;
        },
        fetchExpert () {
            const vm   = this;
            axios.get(route('api.user.admin.experts.edit', vm.$route.params.id))
                 .then((response) => {
                    vm.setUser(response.data);
                    vm.loading = false;
                 });
        },
        setUser(user) {
          this.expertForm = {
            id: user.id,
            group_ids: this.getGroupTagsIds(user.groups.data),
            tag_ids: this.getGroupTagsIds(user.tags.data),
            first_name: user.first_name,
            last_name: user.last_name,
            email: user.email,
            timezone: user.timezone,
            country: user.country.id,
            has_company: this.hasCompany(user),
            education: user.education,
            languages: user.languages,
            memberships: user.memberships,
            availability: user.availability,
            company_name: user.company.name,
            references: user.references,
            company_vat_registered: (user.company_vat_no)? true : false,
            company_number: user.company_number,
            company_town: user.company_town,
            company_position: user.company_position,
            company_vat_no: user.company_vat_no,
            company_region: user.company_region,
            company_postcode: user.company_postcode,
            title: user.title,
            company_country_id: user.country.id,
            professional_experience: user.experience,
            hour_rate: user.hour_rate ? parseInt(user.hour_rate) : '',
            day_rate: user.day_rate ? parseInt(user.day_rate) : '',
            past_projects: user.past_projects,
            references: user.references,
            title: user.title,
            company_street: user.street
          };
        },
        hasCompany(user) {
          if(user.company_position || user.company_phone_contact || user.company_country_id || user.company_street || user.company_town || user.company_vat_no) {
            return true;
          }

          return false;
        },
        resetVatNumber() {
          this.expertForm.company_vat_no = null;
        },
        fetchTimezones () {
            const vm   = this;
            axios.get(route('api.core.timezones.index'))
                 .then((response) => {
                    vm.timezones = response.data
                 });
        },
        fetchCountries () {
            const vm   = this;
            axios.get(route('api.company.countries', { company_code: this.companyCode } ))
                 .then((response) => {
                    vm.companyCountries = response.data.data
                 });
        },
        fetchTags () {
            const vm = this
            let params = {
              is_generic: true,
              per_page: 9999,
              page: 1,
              order_by: 'name',
              order: 'asc'
            };
            axios.get(route('api.user.admin.tags', params)).then((response) => {
              vm.tags = response.data.data
            });
        },
        fetchGroups () {
            const vm = this
            let params = {
              is_generic: true,
              per_page: 9999,
              page: 1,
              order_by: 'name',
              order: 'asc'
            };
            axios.get(route('api.user.admin.groups', params)).then((response) => {
              vm.groups = response.data.data
            });
        },
        resetCompanyDetails() {
            const vm = this
            vm.expertForm.company_name = ''
            vm.expertForm.company_number = ''
            vm.expertForm.company_street = ''
            vm.expertForm.company_town  = ''
            vm.expertForm.company_region = ''
            vm.expertForm.company_postcode = ''
            vm.expertForm.company_vat_registered = false
            vm.expertForm.company_vat_no = ''
            vm.expertForm.company_position = ''
        },
        submitForm() {
          const vm = this;
          vm.form = new Form(vm.expertForm);
          vm.loading = true;

          vm.form.post(route('api.user.admin.experts.update', vm.$route.params.id))
          .then((response) => {
              vm.saveTagUpdate();
          });
        },
        saveTagUpdate() {
            let self = this;
            let data = [];

            for (var i = 0; i < self.expertForm.tag_ids.length; i++) {
              let tag = self.tags.filter(tag => tag.id === self.expertForm.tag_ids[i])
              if(tag.length) {
                tag[0].enabled = 1;
                data.push(tag[0]);
              }
            }

            self.form = new Form({
              tags : data,
              id   : self.$route.params.id
            });

            self.form.post(route('api.user.tags'))
            .then(function (response) {
              self.loading = false;
              self.$message({
                  type: 'success',
                  message: 'Expert succesfully updated',
              });

              self.$router.push({ name: 'admin.user.experts.index' });
            }).catch(error => {
                self.isLoading = false
            });
        }
      },
      created() {
        this.fetchExpert()
        this.fetchTimezones()
        this.fetchCountries()
        this.fetchGroups()
        this.fetchTags()
      },
    };
</script>
