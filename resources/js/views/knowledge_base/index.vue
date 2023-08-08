<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>

        <v-row>
            <v-col cols="12" class="pt-2 pb-1">
                <v-card outlined>
                    <v-card-text style="padding: 5px 15px 0 15px">
                        <v-row>
                            <v-col cols="4" class="pb-0">
                                <v-text-field v-model="search" :color="themeBgColor" :label="langMap.main.search"
                                              append-icon="mdi-magnify" hide-details
                                              v-on:keyup="debounceOpenCategory"/>
                            </v-col>
                            <v-col cols="4" class="pb-0">
                                <v-select v-model="searchWhere" :color="themeBgColor" :items="searchOptions"
                                          item-text="name" item-value="id" label="Search in" multiple
                                          v-on:change="debounceOpenCategory"/>
                            </v-col>
                            <v-col cols="3" class="pb-0">
                                <v-select v-model="activeTags" :color="themeBgColor"
                                          :items="$store.getters['Tags/getTags']"
                                          :label="langMap.kb.tags" append-icon="mdi-tag-multiple-outline" hide-selected
                                          item-text="name" item-value="id"
                                          multiple small-chips
                                          v-on:change="getArticles();">
                                    <template v-slot:selection="{ attrs, item, parent, selected }">
                                        <v-chip :color="item.color" :text-color="invertColor(item.color)" class="ml-2"
                                                close label small v-bind="attrs"
                                                @click:close="syncTags(item)">
                                            {{ item.name }}
                                        </v-chip>
                                    </template>
                                    <template v-slot:item="{ attrs, item, parent, selected }">
                                        <v-chip :color="item.color" :text-color="invertColor(item.color)" class="ml-2"
                                                label v-bind="attrs">
                                            {{ item.name }}
                                        </v-chip>
                                    </template>
                                </v-select>
                            </v-col>
                            <v-col class="text-right pb-0" cols="1">
                                <v-menu
                                    v-if="$helpers.auth.checkKbPermissionsByType(
                                        getRouteAlias,
                                        kbPermissionsTypes.create)"
                                    bottom
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-btn icon v-on="on">
                                            <v-icon>mdi-dots-vertical</v-icon>
                                        </v-btn>
                                    </template>

                                    <v-list>
                                        <v-list-item link @click.prevent="updateCategoryDlg = true">
                                            <v-list-item-title>
                                                {{ langMap.kb.create_category }}
                                            </v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-folder-plus-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                        <v-list-item link @click.prevent="createArticle">
                                            <v-list-item-title>
                                                {{
                                                    langMap.kb['add_' + $route.params.alias] ?
                                                        langMap.kb['add_' + $route.params.alias] :
                                                        langMap.kb['add_knowledge_base']
                                                }}
                                            </v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-file-plus-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-row>
            <v-col class="col-md-4">
                <v-row class="flex-row justify-center align-items-center">
                    <v-progress-circular class="mt-4" indeterminate :value="20" color="#40613e"
                                         v-if="isCategoriesLoading"></v-progress-circular>
                    <perfect-scrollbar v-else style="height: 100vh;" options="scrollOptions">
                    <v-col v-for="category in categories" :key="'c'+category.id" cols="12" class="pb-1 pt-1">
                        <v-hover v-slot="{ hover }">
                            <v-card
                                outlined
                                :color="getCategoryBackgroundColor(category.id)"
                                v-on:click.native="openCategory(category.id, category.parent_id, category.has_sub_categories)"
                                :style="hover ? `outline: 2px solid ${themeBgColor}` : ''"
                                style="cursor: pointer"
                            >
                                <p v-if="getParentCategoryName &&
                                category.parent_id === parseInt($route.query.parent_category)"
                                   class="parent-category-title">
                                    {{ getParentCategoryName }} >
                                </p>
                                <div class="d-flex justify-space-between">
                                    <v-avatar size="60">
                                        <v-icon :color="category.icon_color" size="45"
                                                v-text="category.icon ? category.icon : 'mdi-help'"/>
                                    </v-avatar>
                                    <div class="flex-grow-1">
                                        <v-card-title class="category-card-title">
                                            {{ category.name }}
                                        </v-card-title>
                                        <v-card-text class="category-card-description">
                                            <p class="category-description"
                                               v-if="$helpers.i18n.localized(category, 'description')">
                                                {{ $helpers.i18n.localized(category, 'description') }}
                                            </p>
                                        </v-card-text>
                                    </div>
                                    <div style="min-width: 60px">
                                        <v-btn icon height="30" width="30"
                                               v-if="category.id == $route.query.parent_category"
                                               @click.prevent.stop="openCategory(category.parent_id)">
                                            <v-icon :color="themeBgColor">
                                                mdi-arrow-u-left-top
                                            </v-icon>
                                        </v-btn>
                                        <v-menu
                                            bottom
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-btn icon v-on="on" height="30" width="30" style="float: right">
                                                    <v-icon>mdi-dots-vertical</v-icon>
                                                </v-btn>
                                            </template>

                                            <v-list>
                                                <v-list-item
                                                    v-if="$helpers.auth.checkKbPermissionsByType(
                                                getRouteAlias,
                                                kbPermissionsTypes.edit)"
                                                    link @click="editCategory(category)">
                                                    <v-list-item-title>
                                                        {{
                                                            langMap.kb.edit
                                                        }}
                                                    </v-list-item-title>
                                                    <v-list-item-action>
                                                        <v-icon :color="themeBgColor">
                                                            mdi-folder-edit-outline
                                                        </v-icon>
                                                    </v-list-item-action>
                                                </v-list-item>
                                                <v-list-item
                                                    v-if="$helpers.auth.checkKbPermissionsByType(
                                                getRouteAlias,
                                                kbPermissionsTypes.delete)"
                                                    link
                                                    @click="deleteCategory(category)"
                                                >
                                                    <v-list-item-title>
                                                        {{
                                                            langMap.kb.delete
                                                        }}
                                                    </v-list-item-title>
                                                    <v-list-item-action>
                                                        <v-icon :color="themeBgColor">mdi-folder-remove-outline
                                                        </v-icon>
                                                    </v-list-item-action>
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>
                                    </div>
                                    <!--                            <v-card-actions>-->
                                    <!--                                <v-btn v-if="category.id == $route.query.category" :color="themeBgColor" text-->
                                    <!--                                       @click="openCategory(category.parent_id)" v-text="langMap.kb.return_to_parent"/>-->
                                    <!--                                <v-btn v-else :color="themeBgColor" text @click="openCategory(category.id)"-->
                                    <!--                                       v-text="langMap.kb.open_category"/>-->
                                    <!--                            </v-card-actions>-->
                                </div>
                            </v-card>
                        </v-hover>
                    </v-col>
                    </perfect-scrollbar>
                </v-row>
            </v-col>
            <v-col class="col-md-8">
                <v-row class="flex-row justify-center align-items-center">
                    <v-progress-circular class="mt-4" indeterminate :value="20" color="#40613e"
                                         v-if="isArticlesLoading"></v-progress-circular>
                    <perfect-scrollbar v-else style="height: 100vh;">
                    <v-col v-for="article in sortedArticles()" :key="'a'+article.id" cols="12" class="pb-1 pt-1">
                        <v-card :style="`background-color: ${article.featured_color};`" outlined
                                style="cursor: pointer"
                                v-on:click.native="readArticle(article.id)"
                        >
                            <div class="d-flex justify-space-between">
                                <div class="flex-grow-1 pl-3">
                                    <v-card-title class="article-card-title">
                                        {{ $helpers.i18n.localized(article) }}
                                    </v-card-title>
                                    <v-card-text class="article-card-description">
                                        <p class="mb-2 mt-2">
                                            {{
                                                $helpers.i18n.localized(article, 'summary') !== 'summary' ?
                                                    $helpers.i18n.localized(article, 'summary') : '&nbsp;'
                                            }}
                                        </p>
                                    </v-card-text>
                                </div>
                                <v-menu
                                    bottom
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-btn icon v-on="on">
                                            <v-icon>mdi-dots-vertical</v-icon>
                                        </v-btn>
                                    </template>

                                    <v-list>
                                        <v-list-item
                                            v-if="$helpers.auth.checkKbPermissionsByType(
                                                getRouteAlias,
                                                kbPermissionsTypes.edit)"
                                            link
                                            @click="editArticle(article.id)"
                                        >
                                            <v-list-item-title>
                                                {{
                                                    langMap.kb.edit
                                                }}
                                            </v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-file-edit-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                        <v-list-item
                                            v-if="$helpers.auth.checkKbPermissionsByType(
                                                getRouteAlias,
                                                kbPermissionsTypes.delete)"
                                            link
                                            @click="deleteArticle(article)"
                                        >
                                            <v-list-item-title>
                                                {{
                                                    langMap.kb.delete
                                                }}
                                            </v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-file-remove-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </div>
                            <!--                            <v-card-text style="height: 6em;">-->
                            <!--                                <v-chip-group v-if="article.tags">-->
                            <!--                                    <v-chip v-for="tag in article.tags" :key="tag.id" label small class="mr-2" v-text="tag.name" :color="tag.color" :text-color="invertColor(tag.color)"/>-->
                            <!--                                </v-chip-group>-->
                            <!--                                <p v-else>{{ langMap.kb.no_tags}}</p>-->
                            <!--                                <v-spacer>&nbsp;</v-spacer>-->
                            <!--                                <p>{{ $helpers.i18n.localized(article, 'summary') }}</p>-->
                            <!--                            </v-card-text>-->
                            <!--                            <v-card-actions>-->
                            <!--                                <v-btn text :color="themeBgColor" v-text="langMap.kb.read_article" @click="readArticle(article.id)" />-->
                            <!--                            </v-card-actions>-->
                        </v-card>
                    </v-col>
                    </perfect-scrollbar>
                </v-row>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-dialog v-model="updateCategoryDlg" max-width="600px" persistent>
                <v-card outlined>
                    <v-card-title>{{ langMap.kb.category_details }}</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="6">
                                <label>{{ langMap.kb.parent_category }}</label>
                                <perfect-scrollbar>
                                    <v-treeview :active="categoryForm._active" :color="themeBgColor"
                                                :items="categoriesTree"
                                                activatable item-key="id" open-all
                                                @update:active="refreshCategoryForm">
                                        <template v-slot:prepend="{ item }">
                                            <v-icon>mdi-folder</v-icon>
                                        </template>
                                        <template v-slot:label="{ item }">
                                            {{ $helpers.i18n.localized(item) }}
                                        </template>
                                    </v-treeview>
                                </perfect-scrollbar>
                            </v-col>
                            <v-col cols="6">
                                <label>{{ langMap.main.icon }}</label>
                                <v-select v-model="categoryForm.icon"
                                          :color="themeBgColor"
                                          :items="categoryIcons"
                                          :label="langMap.main.icon"
                                          append-icon="mdi-tag-multiple-outline"
                                          hide-selected
                                          item-text="name"
                                          item-value="id"
                                          v-on:change="getArticles();">
                                    <template v-slot:selection="{ attrs, item, parent, selected }">
                                        <i :class="item"></i>&nbsp;
                                        {{ item.replace(/mdi/g, '').replace(/-/g, ' ') }}
                                    </template>
                                    <template v-slot:item="{ attrs, item, parent, selected }">
                                        <i :class="item"></i>&nbsp;
                                        {{ item.replace(/mdi/g, '').replace(/-/g, ' ') }}
                                    </template>
                                </v-select>

                                <label>{{ langMap.kb.icon_color }}</label>
                                <v-color-picker :model="categoryForm.icon_color" dot-size="25" mode="hexa"
                                                @update:color="updateCategoryColor"/>

                                <v-expansion-panels>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>English</v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-text-field v-model="categoryForm.name" :color="themeBgColor"
                                                          :label="langMap.main.name" hide-details single-line/>
                                            <v-text-field v-model="categoryForm.description"
                                                          :color="themeBgColor" :label="langMap.main.description"
                                                          hide-details
                                                          single-line/>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Deutsch</v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-text-field v-model="categoryForm.name_de" :color="themeBgColor"
                                                          :label="langMap.main.name" hide-details single-line/>
                                            <v-text-field v-model="categoryForm.description_de"
                                                          :color="themeBgColor" :label="langMap.main.description"
                                                          hide-details
                                                          single-line/>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </v-col>
                        </v-row>
                        <v-switch v-model="categoryForm.is_internal" :color="themeBgColor"
                                  :label="langMap.kb.is_internal" :value="1"/>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn left text @click="updateCategoryDlg=false; clearCategoryForm();"
                               v-text="langMap.main.cancel"/>
                        <v-btn :color="themeBgColor" text
                               @click="updateCategory"
                               v-text="categoryForm.id ? langMap.main.update : langMap.main.create"/>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="deleteCategoryDlg" max-width="600px" persistent>
                <v-card outlined>
                    <v-card-title>{{ langMap.main.delete }}</v-card-title>
                    <v-card-text>
                        <p>
                            {{ langMap.kb.delete_category }}<br/>
                            <b>{{ $helpers.i18n.localized(categoryForm) }}</b>
                        </p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn left text @click="deleteCategoryDlg=false; clearCategoryForm();"
                               v-text="langMap.main.cancel"/>
                        <v-btn :color="themeBgColor" text @click="removeCategory" v-text="langMap.main.delete"/>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="deleteArticleDlg" max-width="600px" persistent>
                <v-card outlined>
                    <v-card-title>{{ langMap.main.delete }}</v-card-title>
                    <v-card-text>
                        <p>
                            {{ langMap.kb.delete_article }}<br/>
                            <b>{{ $helpers.i18n.localized(selectedArticle) }}</b>
                        </p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn left text @click="deleteArticleDlg=false; clearSelectedArticle();"
                               v-text="langMap.main.cancel"/>
                        <v-btn :color="themeBgColor" text @click="removeArticle" v-text="langMap.main.delete"/>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
    </v-container>
</template>

<style scoped>
.category-description {
    font-size: 12px;
    line-height: 14px;
    color: #969696;
    max-height: 28px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    margin: 2px 0 2px 0;
}

.category-card-title, .article-card-title {
    font-size: 1rem;
    line-height: 1.2rem;
    padding: 8px 0 0 0;
}

.category-card-description, .article-card-description {
    padding: 0;
}

.article-card-description {
    font-size: 12px;
    line-height: 14px;
}

.parent-category-title {
    position: absolute;
    margin: 0;
    font-size: 8px;
    color: #969696;
    left: 5px;
    max-width: 70%;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 10px;
    white-space: nowrap;
}

>>> .ps {
    max-height: calc(95vh - 109px - 64px - 8px);
}

>>> .ps__rail-x {
    display: none!important;
}
</style>

<script>
import EventBus from '../../components/EventBus';
import * as _ from 'lodash';

export default {
    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            errors: [],
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            search: '',
            searchWhere: [1, 2, 3],
            searchOptions: [
                {id: 1, name: this.$store.state.lang.lang_map.kb.search_in_category_names},
                {id: 2, name: this.$store.state.lang.lang_map.kb.search_in_article_names},
                {id: 3, name: this.$store.state.lang.lang_map.kb.search_in_article_names_and_contents}
            ],
            categories: [],
            categoriesTree: [],
            articles: [],
            activeTags: [],
            updateCategoryDlg: false,
            deleteCategoryDlg: false,
            categoryForm: {
                type: this.$route.params.alias,
                id: null,
                parent_id: this.$route.query.category ?
                    this.$route.query.category :
                    (this.$route.query.parent_category ? this.$route.query.parent_category : null),
                name: '',
                name_de: '',
                description: '',
                description_de: '',
                icon: '',
                icon_color: this.$store.state.themeBgColor,
                _active: [],
                is_internal: 0
            },
            categoryIcons: [
                // menu icons
                'mdi mdi-view-dashboard',
                'mdi mdi-monitor',
                'mdi mdi-gesture-double-tap',
                'mdi mdi-launch',
                'mdi mdi-table-alert',
                'mdi mdi-bullhorn',
                'mdi mdi-bell-alert',
                'mdi mdi-account-plus',
                'mdi mdi-map-marker',
                'mdi mdi-tune',
                'mdi mdi-refresh',
                'mdi mdi-apps',
                'mdi mdi-account-switch',
                'mdi mdi-language-javascript',
                'mdi mdi-text-box-check-outline',
                'mdi mdi-paperclip',
                'mdi mdi-account-cog',
                'mdi mdi-account',
                'mdi mdi-account-voice',
                'mdi mdi-account-group',
                'mdi mdi-phone-log',
                'mdi mdi-card-account-phone',
                'mdi mdi-card-account-phone-outline',
                'mdi mdi-beach',
                'mdi mdi-shield-account',
                'mdi mdi-shield-lock',
                'mdi mdi-factory',
                'mdi mdi-poll',
                'mdi mdi-clipboard-edit-outline',
                'mdi mdi-clipboard-edit',
                'mdi mdi-cog',
                'mdi mdi-server',
                'mdi mdi-leak',
                'mdi mdi-apps',
                'mdi mdi-swap-vertical',
                'mdi mdi-shield-alert',


                'mdi mdi-alert-outline',
                'mdi mdi-bell-ring-outline',
                'mdi mdi-alarm',
                'mdi mdi-alert-rhombus-outline',
                'mdi mdi-bell-alert-outline',
                'mdi mdi-car-brake-alert',
                'mdi mdi-alert-circle-outline',
                'mdi mdi-alert-octagon-outline',
                'mdi mdi-alert-decagram-outline',
                'mdi mdi-alert-box-outline',
                'mdi mdi-weather-cloudy-alert',
                'mdi mdi-alarm-multiple',
                'mdi mdi-bell-circle-outline',
                'mdi mdi-exit-run',
                'mdi mdi-exit-to-app',
                'mdi mdi-fire',
                'mdi mdi-fire-extinguisher',
                'mdi mdi-air-horn',
                'mdi mdi-fire-hydrant',
                'mdi mdi-water-alert-outline',
                'mdi mdi-water-remove-outline',
                'mdi mdi-pipe-leak',
                'mdi mdi-ambulance',
                'mdi mdi-medical-bag',
                'mdi mdi-hospital-box-outline',
                'mdi mdi-zodiac-aquarius',
                'mdi mdi-wrench-outline',
                'mdi mdi-server-off',
                'mdi mdi-lan-disconnect',
                'mdi mdi-alarm-light-outline',
                'mdi mdi-alarm-light-outline',
                'mdi mdi-hazard-lights',
                'mdi mdi-triforce',
                'mdi mdi-molecule-co2',
                'mdi mdi-flash-alert-outline',
                'mdi mdi-bottle-tonic-skull-outline',
                'mdi mdi-skull-crossbones-outline',
                'mdi mdi-skull',
                'mdi mdi-virus-outline',
                'mdi mdi-bacteria-outline',
                'mdi mdi-shield-alert-outline',
                'mdi mdi-shield-key-outline',
                'mdi mdi-account-alert-outline',
                'mdi mdi-security-network',
                'mdi mdi-security',
                'mdi mdi-lock-open-alert',
                'mdi mdi-lock-open-outline',
                'mdi mdi-key-outline',
                'mdi mdi-key-remove',
                'mdi mdi-home-alert',
                'mdi mdi-home-lock-open',
                'mdi mdi-window-shutter-alert',
                'mdi mdi-volume-off',
                'mdi mdi-broom',
                'mdi mdi-rake',
                'mdi mdi-hammer-wrench',
                'mdi mdi-wrench-outline',
                'mdi mdi-help-circle-outline',
                'mdi mdi-lan-disconnect',
                'mdi mdi-server-off',
                'mdi mdi-desktop-classic',
                'mdi mdi-content-save-alert-outline',
                'mdi mdi-disc-alert',
                'mdi mdi-timeline-alert-outline',
                'mdi mdi-folder-key-outline',
                'mdi mdi-folder-alert',
                'mdi mdi-table-alert',
                'mdi mdi-wifi-strength-alert-outline',
                'mdi mdi-restart-alert',
                'mdi mdi-information-outline',
                'mdi mdi-information-variant',
                'mdi mdi-clock-alert-outline',
                'mdi mdi-calendar-alert',
                'mdi mdi-exclamation',
                'mdi mdi-exclamation-thick',
                'mdi mdi-clipboard-alert-outline',
                'mdi mdi-sticker-alert-outline',
                'mdi mdi-coffee-outline',
                'mdi mdi-bus-alert',
                'mdi mdi-subway-alert-variant',
                'mdi mdi-traffic-light',
                'mdi mdi-coolant-temperature',
                'mdi mdi-radioactive',
                'mdi mdi-printer-3d-nozzle-alert-outline',
                'mdi mdi-tray-alert',
                'mdi mdi-beaker-alert-outline',
                'mdi mdi-water-percent-alert',
                'mdi mdi-thermometer-alert',
                'mdi mdi-thermometer-lines',
                'mdi mdi-oil-level',
                'mdi mdi-dishwasher-alert',
                'mdi mdi-battery-alert-variant-outline',
                'mdi mdi-vibrate',
                'mdi mdi-watch-vibrate',
                'mdi mdi-fuse-alert',
                'mdi mdi-engine-outline',
                'mdi mdi-fridge-alert-outline',
                'mdi mdi-state-machine',
                'mdi mdi-gas-cylinder',
                'mdi mdi-diving-scuba-tank',
                'mdi mdi-fan-alert',
                'mdi mdi-lightbulb-on-outline',
                'mdi mdi-power-plug-off-outline',
                'mdi mdi-car-tire-alert',
                'mdi mdi-lightning-bolt-outline',
                'mdi mdi-transmission-tower',
                'mdi mdi-scale-balance',
                'mdi mdi-snowflake-alert',
                'mdi mdi-snowflake-melt',
                'mdi mdi-weather-cloudy-alert',
                'mdi mdi-weather-lightning',
                'mdi mdi-weather-pouring',
            ],
            deleteArticleDlg: false,
            selectedArticle: {
                id: null,
                name: '',
                name_de: ''
            },
            kbPermissionsTypes: {
                view: 'view',
                create: 'create',
                edit: 'edit',
                delete: 'delete',
            },
            activeCategoryColor: '#ebebeb',
            isCategoriesLoading: false,
            isArticlesLoading: false,
        }
    },
    watch: {
        $route(to, from) {
            if (_.isEmpty(this.$route.query)) {
                this.getCategories();
                this.getArticles();
                this.getCategoriesTree();
                this.getTags();
            }
            this.categoryForm.parent_id = this.getCategoryIdFromQuery;
        }
    },
    created() {
        this.debounceOpenCategory = _.debounce(
            () => this.openCategory(this.$route.query.parent_category),
            1000
        );
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });

        this.getCategories();
        this.getArticles();
        this.getCategoriesTree();
        this.getTags();
    },
    methods: {
        sortedArticles() {
            return this.articles.sort((a, b) => a.name.localeCompare(b.name, undefined, {numeric: true}))
        },
        limitTo(str, count = 50) {
            if (!str) return '';
            if (String(str).length <= count) return str;
            return String(str).substring(0, count) + '...';
        },
        invertColor(hex) {
            return this.$helpers.color.invertColor(hex);
        },
        getTags() {
            this.$store.dispatch('Tags/getTagList')
        },
        getCategories() {
            this.isCategoriesLoading = true
            axios.get(`/api/kb/categories?type=${this.$route.params.alias}`, {
                params: {
                    search: this.searchWhere.includes(1) ? this.search : '',
                    category_id: this.$route.query.parent_category ? this.$route.query.parent_category : null
                }
            }).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.categories = response.data;
                    this.isCategoriesLoading = false
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                    this.isCategoriesLoading = false
                }
            }).catch(() => {
                this.isCategoriesLoading = false
            });
        },
        getCategoriesTree() {
            axios.get(`/api/kb/categories/tree?type=${this.$route.params.alias}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.categoriesTree = response.data;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },
        getArticles() {
            this.isArticlesLoading = true;
            axios.get(`/api/kb/articles?type=${this.$route.params.alias}`, {
                params: {
                    search: this.searchWhere.includes(2) || this.searchWhere.includes(3) ? this.search : '',
                    search_in_text: this.searchWhere.includes(3),
                    category_id: this.getCategoryIdFromQuery,
                    tags: this.activeTags
                }
            }).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.articles = response.data;
                    this.isArticlesLoading = false;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                    this.isArticlesLoading = false;
                }
            }).catch(() => {
                this.isArticlesLoading = false;
            });
        },
        openCategory(id, parent_id = null, subCategories = true) {
            let query = '';
            if (id && parent_id && !subCategories) {
                query = `parent_category=${parent_id}&category=${id}`
            } else if (id && subCategories) {
                query = `parent_category=${id}`;
            } else if (id) {
                query = `category=${id}`;
            }
            if (query) {
                this.$router.push(`/${this.$route.params.alias}?${query}`, () => {
                })
            } else if (this.$router.app.$route.fullPath !== this.$router.app.$route.path) {
                this.$router.push(`/${this.$route.params.alias}`, () => {
                });
            }
            if (subCategories) {
                this.getCategories();
            }
            this.getArticles();

        },
        clearCategoryForm() {
            this.categoryForm = {
                id: null,
                type: this.$route.params.alias,
                parent_id: this.getCategoryIdFromQuery,
                name: '',
                name_de: '',
                description: '',
                description_de: '',
                icon: '',
                icon_color: this.themeBgColor,
                _active: []
            };
        },
        fillCategoryForm(category) {
            this.categoryForm = {
                id: category.id,
                type: this.$route.params.alias,
                parent_id: category.parent_id,
                name: category.name,
                name_de: category.name_de,
                description: category.description,
                description_de: category.description_de,
                icon_color: category.icon_color,
                icon: category.icon,
                _active: [category.parent_id]
            };
            this.$forceUpdate();
        },
        refreshCategoryForm(parent) {
            this.categoryForm.parent_id = parent.length > 0 ? parent[0] : null;
            this.$forceUpdate();
        },
        editCategory(category) {
            this.fillCategoryForm(category);
            this.updateCategoryDlg = true;
        },
        updateCategory() {
            if (this.categoryForm.id) {
                axios.put(`/api/kb/category/${this.categoryForm.id}?type=${this.$route.params.alias}`, this.categoryForm).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.updateCategoryDlg = false;
                        this.clearCategoryForm();
                        this.getCategories();

                        this.snackbarMessage = this.langMap.kb.category_updated;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            } else {
                axios.post(`/api/kb/category?type=${this.$route.params.alias}`, this.categoryForm).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.updateCategoryDlg = false;
                        this.clearCategoryForm();
                        this.getCategories();

                        this.snackbarMessage = this.langMap.kb.category_created;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            }
        },
        deleteCategory(category) {
            this.fillCategoryForm(category);
            this.deleteCategoryDlg = true;
        },
        removeCategory() {
            this.deleteCategoryDlg = false;

            axios.delete(`/api/kb/category/${this.categoryForm.id}?type=${this.$route.params.alias}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.clearCategoryForm();
                    this.getCategories();

                    this.snackbarMessage = this.langMap.kb.category_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });

        },
        readArticle(id) {
            this.$router.push(`/${this.$route.params.alias}/${id}`);
        },
        createArticle() {
            location.href = `/${this.$route.params.alias}/create`;
        },
        editArticle(id) {
            this.$router.push(`/${this.$route.params.alias}/${id}/edit`);
        },
        clearSelectedArticle() {
            this.selectedArticle = {
                id: null,
                name: '',
                name_de: ''
            }
        },
        deleteArticle(article) {
            this.selectedArticle = article;
            this.deleteArticleDlg = true;
        },
        removeArticle() {
            this.deleteArticleDlg = false;

            axios.delete(`/api/kb/article/${this.selectedArticle.id}?type=${this.$route.params.alias}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.clearSelectedArticle();
                    this.getArticles();

                    this.snackbarMessage = this.langMap.kb.article_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },
        updateCategoryColor(color) {
            this.categoryForm.icon_color = color.hex;
        },
        syncTags(item) {
            let index = this.activeTags.indexOf(item.id);
            if (index !== -1) {
                this.activeTags.splice(index, 1);
                this.getArticles();
            }
        },
        getCategoryBackgroundColor(id) {
            switch (id) {
                case parseInt(this.$route.query.parent_category):
                    return this.$helpers.color.lightenColor(this.themeBgColor, 50);
                case parseInt(this.$route.query.category):
                    return this.activeCategoryColor;
                default:
                    return '';
            }
        },
    },
    computed: {
        getRouteAlias() {
            return this.$route.params.alias;
        },
        getParentCategoryName() {
            const parentCategory = this.$route.query.parent_category;
            return parentCategory ? this.categories.find((item) => item.id === parseInt(parentCategory)).name : '';
        },
        getCategoryIdFromQuery() {
            return this.$route.query.category ?
                this.$route.query.category :
                (this.$route.query.parent_category ? this.$route.query.parent_category : null)
        }
    }
}
</script>
