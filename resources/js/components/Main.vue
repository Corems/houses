<template>
    <el-container style="border: 1px solid #eee" v-loading.fullscreen.lock="fullscreenLoading">
        <el-aside width="200px" style="background-color: rgb(238, 241, 246)">
            <el-menu>
                <el-submenu v-for="(filter, name, index) in filters" :key="index" :index="index + ''">
                    <template slot="title"><i class="el-icon-message"></i>{{ filter.name }}</template>
                    <template v-if="filter.type === 'checkbox'">
                        <el-menu-item v-for="(optName,optNum,optIndex) in filter.values" :key="optNum" :index="optIndex + ''">
                            <el-checkbox v-model="filter.checked[optNum]" @change="refreshSearchData">{{ optName }}</el-checkbox>
                        </el-menu-item>
                    </template>
                    <template v-if="filter.type === 'range'">
                        <el-input placeholder="Min price" v-model="filter.min" @change="refreshSearchData"></el-input>
                        <el-input placeholder="Max price" v-model="filter.max" @change="refreshSearchData"></el-input>
                    </template>
                </el-submenu>
            </el-menu>
        </el-aside>

        <el-container>
            <el-main>
                <el-form ref="form" label-width="120px">
                    <el-form-item label="Search house">
                        <el-input v-model="search" @change="refreshSearchData"></el-input>
                    </el-form-item>
                </el-form>

                <el-table :data="tableData" stripe  v-if="tableData.length > 0">
                    <el-table-column prop="name" label="Name">
                    </el-table-column>
                    <el-table-column prop="price" label="Price">
                    </el-table-column>
                    <el-table-column prop="bedrooms" label="Bedrooms">
                    </el-table-column>
                    <el-table-column prop="bathrooms" label="Bathrooms">
                    </el-table-column>
                    <el-table-column prop="storeys" label="Storeys">
                    </el-table-column>
                    <el-table-column prop="garages" label="Garages">
                    </el-table-column>
                </el-table>
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
    export default {
        name: 'Main',
        data() {
            return {
                fullscreenLoading: false,
                filters: {
                    price: {
                        name: 'Price Filter',
                        type: 'range',
                        min: null,
                        max: null,
                    },
                    bedrooms: {
                        name: 'Bedrooms Filter',
                        values: [],
                        type: 'checkbox',
                        checked: []
                    },
                    bathrooms: {
                        name: 'Bathrooms Filter',
                        values: [],
                        type: 'checkbox',
                        checked: []
                    },
                    storeys: {
                        name: 'Storeys Filter',
                        values: [],
                        type: 'checkbox',
                        checked: []
                    },
                    garages: {
                        name: 'Garages Filter',
                        values: [],
                        type: 'checkbox',
                        checked: []
                    },
                },
                tableData: [],
                search: null,
            }
        },
        methods: {
            refreshSearchData() {
                this.fullscreenLoading = true
                window.axios({
                    url: "api/search",
                    method: "GET",
                    params: this.getFilters(this.search, this.filters)
                }).then(response => {
                    this.fullscreenLoading = false

                    if (response.data.foundItems.length === 0) {
                        this.$message.warning('There are no results');
                    }

                    this.tableData = response.data.foundItems
                }).catch(() => {
                    this.fullscreenLoading = false
                    this.$message.error('Oops, this is a error message.');
                })
            },
            getFilters(searchInput, filters) {
                return {
                    search: searchInput,
                    priceMin: filters.price.min,
                    priceMax: filters.price.max,
                    bedrooms: this.getSelectedValues(filters.bedrooms.checked, filters.bedrooms.values).join(),
                    bathrooms: this.getSelectedValues(filters.bathrooms.checked, filters.bathrooms.values).join(),
                    storeys: this.getSelectedValues(filters.storeys.checked, filters.storeys.values).join(),
                    garages: this.getSelectedValues(filters.garages.checked, filters.garages.values).join(),
                }
            },
            getSelectedValues(arrayChecked, arrayValues) {
                let result = []
                arrayChecked.forEach((item, number) => {
                    if (item) {
                        result.push(arrayValues[number])
                    }
                })

                return result
            }
        },
        beforeCreate() {
            window.axios({
                url: "api/filters",
                method: "GET"
            }).then(response => {
                this.filters.bedrooms.values = response.data.bedrooms
                this.filters.bathrooms.values = response.data.bathrooms
                this.filters.storeys.values = response.data.storeys
                this.filters.garages.values = response.data.garages
            })
        },
    }
</script>

<style>
    .el-table__empty-block {
        display: none!important;
    }

    .empty-block {
        justify-content: center;
        text-align: center;
    }
</style>
