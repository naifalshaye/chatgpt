<template>
    <div class="md:w-full">
        <div class="mb-2 text-xl text-center">Ask ChatGPT</div>
        <div class="mb-2 text-md flex justify-center" v-if="this.history.length > 0">
            <table class="table-auto mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <thead>
                <tr class="text-xs">
                    <th class="border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 px-4 py-3">
                        ID
                    </th>
                    <th class="border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 px-4 py-3">
                        Question
                    </th>
                    <th class="border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 px-4 py-3">
                        Date
                    </th>
                    <th class="border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 px-4 py-3">
                        Tokens
                    </th>
                    <th class="border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 px-4 py-3">
                        View
                    </th>
                    <th class="border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 px-4 py-3">
                        Delete
                    </th>
                </tr>
                </thead>
                <tbody class="text-center">
                <tr v-for="record in this.history"
                    class="hover:bg-gray-100 dark:hover:bg-gray-600">
                    <td class="border border-gray-300 dark:border-gray-700 px-4 py-1">
                        {{ record.id }}
                    </td>
                    <td class="border border-gray-300 dark:border-gray-700 px-4 py-1">
                        {{ record.question.substring(0, 90) }}...
                    </td>
                    <td class="border border-gray-300 dark:border-gray-700 px-4 py-1">
                        {{ this.formatDate(record.created_at) }}
                    </td>
                    <td class="border border-gray-300 dark:border-gray-700 px-4 py-1">
                        {{ record.total_tokens }}
                    </td>
                    <td class="border border-gray-300 dark:border-gray-700 px-4 py-1">
                        <a :href="'/nova/chatgpt/view-questions-history/view/'+record.id">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </a>
                    </td>
                    <td class="border border-gray-300 dark:border-gray-700 px-4 py-1 flex justify-center items-center">
                        <a :href="'#'" @click="deleteRecord(record.id)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-show="this.history.length === 0" class="text-center mt-3 text-md text-red-600">
            No history records found!
        </div>
    </div>
</template>

<script>

export default {
    components: {},

    data() {
        return {
            history: [],
        };
    },
    mounted() {
        this.getHistory();
    },
    methods: {
        async getHistory() {
            Nova.request().get('/nova-vendor/chatgpt/history/get-questions', {}).then(({data}) => {
                this.history = data.history.data
            })
        },
        formatDate(date) {
            var now = new Date(date);

            var year = now.getFullYear();
            var month = (now.getMonth() + 1).toString().padStart(2, '0'); // Adding 1 to the month, as it is zero-based
            var day = now.getDate().toString().padStart(2, '0');

            return year + '-' + month + '-' + day;
        },
        async deleteRecord(id) {
            Nova.request().post('/nova-vendor/chatgpt/history/delete-question', {id:id}).then(({data}) => {
                if (data){
                    this.getHistory();
                }
            })
        },
    }

}
</script>
