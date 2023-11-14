<template>
    <div>
        <form @submit.prevent="submitForm">
            <div>
                <label for="name">File Name:</label>
                <input type="text" id="name" v-model="fileData.name">
            </div>
            <div>
                <label for="file">File:</label>
                <input type="file" id="file" @change="handleFileUpload">
            </div>
            <button type="submit">Save</button>
            <button @click="confirmDelete">Delete</button>
        </form>
        <modal v-if="showModal" @close="showModal = false">
            <div slot="body">
                Confirm Deletion?
            </div>
            <div slot="footer">
                <button @click="deleteFile">Yes</button>
                <button @click="showModal = false">No</button>
            </div>
        </modal>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            fileData: { name: '', file: null },
            showModal: false,
            uploadProgress: 0,
        };
    },
    mounted() {
        if (this.$route.params.id) {
            this.fetchFile(this.$route.params.id);
        }
    },
    methods: {
        async fetchFile(id) {
            const response = await axios.get('/api/files/' + id);
            this.fileData.name = response.data.name;
            // Обратите внимание: файл загрузить нельзя, но мы загружаем другие данные.
        },
        handleFileUpload(event) {
            this.fileData.file = event.target.files[0];
        },
        async submitForm() {
            const formData = new FormData();
            formData.append('name', this.fileData.name);
            if (this.fileData.file) {
                formData.append('file', this.fileData.file);
            }

            try {
                const response = this.$route.params.id
                    ? await axios.post('/api/files/' + this.$route.params.id, formData, {
                        onUploadProgress: progressEvent => {
                            this.uploadProgress = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100));
                        }
                    })
                    : await axios.post('/api/files', formData, {
                        onUploadProgress: progressEvent => {
                            this.uploadProgress = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100));
                        }
                    });

                this.$router.push({ path: '/files' });
            } catch (error) {
                console.error(error);
                // Обработка ошибок
            }
        },
        confirmDelete() {
            this.showModal = true;
        },
        async deleteFile() {
            try {
                await axios.delete('/api/files/' + this.$route.params.id);
                this.$router.push({ path: '/files' });
            } catch (error) {
                console.error(error);
                // Обработка ошибок
            } finally {
                this.showModal = false;
            }
        },
    },
};
</script>
