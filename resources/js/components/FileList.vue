<template>
    <div>
        <input type="text" v-model="search" @input="fetchFiles">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Size (MB)</th>
                <th>Extension</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="file in files.data" :key="file.id">
                <td>{{ file.name }}</td>
                <td>{{ (file.size / 1024 / 1024).toFixed(2) }}</td>
                <td>{{ file.extension }}</td>
                <td>
                    <button @click="deleteFile(file.id)">Delete</button>
                    <router-link :to="'/files/' + file.id + '/edit'">Edit</router-link>
                </td>
            </tr>
            </tbody>
        </table>
        <div>
            Total Files: {{ files.total }}
            <br>
            Files on This Page: {{ files.per_page }}
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            search: '',
            files: { data: [], total: 0, per_page: 50 },
        };
    },
    watch: {
        search: 'fetchFiles',
    },
    mounted() {
        this.fetchFiles();
    },
    methods: {
        async fetchFiles() {
            const response = await axios.get('/api/files?search=' + this.search);
            this.files = response.data;
        },
        async deleteFile(id) {
            await axios.delete('/api/files/' + id);
            this.fetchFiles();
        },
    },
};
</script>
