<template>
  <v-container fluid>
    <v-btn :color="themeBgColor" @click="createLinkDialog = true" dark
      >{{ langMap.links.add_link }}
    </v-btn>
    <div class="d-flex flex-wrap" style="padding: 15px 0">
      <div
        style="width: 350px; margin: 0 15px 15px 0"
        v-for="item in links"
        :key="item.id"
      >
        <v-expansion-panels>
          <v-expansion-panel>
            <v-expansion-panel-header>
              {{ item.name }}
            </v-expansion-panel-header>
            <v-expansion-panel-content>
              <div class="d-flex flex-column" style="color: #00000099">
                <div>
                  <span style="color: #00000099"
                    >{{ langMap.links.link }}:
                  </span>
                  <a :href="item.link" target="_blank">{{ item.link }}</a>
                </div>
                <div class="mb-5">
                  <span style="color: #00000099"
                    >{{ langMap.links.description }}:</span
                  >
                  <span style="font-weight: 600">{{ item.description }}</span>
                </div>
                <div>
                  <v-text-field
                    :color="themeBgColor"
                    :label="langMap.links.login"
                    :value="item.login"
                    readonly
                    hide-details
                  ></v-text-field>
                </div>
                <div>
                  <v-text-field
                    :color="themeBgColor"
                    :type="item.isPasswordShown ? 'text' : 'password'"
                    :append-icon="
                      item.isPasswordShown ? 'mdi-eye' : 'mdi-eye-off'
                    "
                    :label="langMap.links.password"
                    :value="item.password"
                    @click:append="item.isPasswordShown = !item.isPasswordShown"
                    hide-details
                  ></v-text-field>
                </div>
              </div>

              <div
                class="d-flex justify-lg-end align-items-center"
                style="width: 100%; margin-top: 40px; gap: 12px"
              >
                <v-btn
                  :color="themeBgColor"
                  @click="
                    createLinkDialog = true;
                    addLinkForm = item;
                  "
                  :style="`color: ${themeFgColor};`"
                  v-text="langMap.main.edit"
                />
                <v-btn
                  color="red"
                  @click="handleDeleteLink(item.id)"
                  :style="`color: ${themeFgColor};`"
                  v-text="langMap.main.delete"
                />
              </div>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
      </div>
    </div>

    <v-dialog v-model="createLinkDialog" max-width="400px">
      <v-card dense outlined>
        <v-card-title
          :style="`color: ${themeFgColor}; background-color: ${themeBgColor}; font-size: 20px;`"
          class="mb-5"
        >
          {{ langMap.links.new_link }}
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="addLinkForm.name"
                  :color="themeBgColor"
                  :item-color="themeBgColor"
                  :label="langMap.links.name"
                  hide-details
                  dense
                />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="addLinkForm.link"
                  :color="themeBgColor"
                  :item-color="themeBgColor"
                  :label="langMap.links.link"
                  hide-details
                  dense
                />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="addLinkForm.description"
                  :color="themeBgColor"
                  :item-color="themeBgColor"
                  :label="langMap.links.description"
                  hide-details
                  dense
                />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="addLinkForm.login"
                  :color="themeBgColor"
                  :item-color="themeBgColor"
                  :label="langMap.links.login"
                  hide-details
                  dense
                />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="addLinkForm.password"
                  :color="themeBgColor"
                  :type="addLinkForm.isPasswordShown ? 'text' : 'password'"
                  :append-icon="
                    addLinkForm.isPasswordShown ? 'mdi-eye' : 'mdi-eye-off'
                  "
                  :label="langMap.links.password"
                  @click:append="
                    addLinkForm.isPasswordShown = !addLinkForm.isPasswordShown
                  "
                  hide-details
                  dense
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn
            color="red"
            @click="createLinkDialog = false"
            :style="`color: ${themeFgColor};`"
            v-text="langMap.main.cancel"
          />
          <v-btn
            :color="themeBgColor"
            :style="`color: ${themeFgColor};`"
            v-text="langMap.main.create"
            @click="
              createLinkDialog = false;
              addLink();
            "
          />
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      themeBgColor: this.$store.state.themeBgColor,
      themeFgColor: this.$store.state.themeFgColor,
      langMap: this.$store.state.lang.lang_map,
      links: [
        {
          id: 1,
          name: 'Google',
          link: 'https://google.com',
          description: 'company user access',
          login: 'company_user@gmail.com',
          password: 'qwer1234',
          isPasswordShown: false,
        },
        {
          id: 2,
          name: 'Facebook',
          link: 'https://facebook.com',
          description: 'Manager',
          login: 'company_user@bing.com',
          password: 'qwerty',
          isPasswordShown: false,
        },
        {
          id: 3,
          name: 'Bing',
          link: 'https://bing.com',
          description: 'Manager',
          login: 'company_user@bing.com',
          password: 'qwerty',
          isPasswordShown: false,
        },
        {
          id: 4,
          name: 'Facebook',
          link: 'https://facebook.com',
          description: 'Manager',
          login: 'company_user@bing.com',
          password: 'qwerty',
          isPasswordShown: false,
        },
      ],
      createLinkDialog: false,
      addLinkForm: {
        name: '',
        link: '',
        description: '',
        login: '',
        password: '',
        isPasswordShown: false,
      },
    };
  },
  mounted() {},
  methods: {
    addLink() {
      console.log(this.addLinkForm);
    },
    handleDeleteLink(itemId) {
      this.links = this.links.filter(({ id }) => id !== itemId);
    },
  },
  watch: {
    createLinkDialog(value) {
      if (!value) {
        this.addLinkForm = {};
      }
    },
  },
  computed: {
    fieldType(isShown) {
      return isShown ? 'text' : 'password';
    },
  },
};
</script>
