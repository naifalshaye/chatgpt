<template>
    <div class="w-full flex flex-col items-center justify-center">
        <div class="mb-2 text-xl">Ask ChatGPT</div>
        <div class="w-full max-w-4xl bg-white dark:bg-gray-800 dark:text-gray-400 rounded-lg shadow p-6">
            <div class="px-6 pt-4 pb-2">
                <span
                    class=" py-1 text-sm font-semibold mr-2 mb-2 ">Date: {{
                        this.formatDate(record.created_at)
                    }}</span>
            </div>
            <div class="px-6 py-4">
                <div class="text-lg mb-2">{{ record.question }}</div>
                <ul class="leading-normal">
                    <li v-for="line in record.answer.split(/\r?\n/)">{{line}}</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: {
        props: ['id'],
    },
    components: {},
    data() {
        return {
            record: {},
        };
    },
    mounted() {
        this.getRecord();
    },
    methods: {
        async getRecord() {
            Nova.request().get('/nova-vendor/chatgpt/history/get-question/' + this.$page.props.id).then(({data}) => {
                this.record = data.record
            })
        },
        formatDate(date) {
            var now = new Date(date);

            var year = now.getFullYear();
            var month = (now.getMonth() + 1).toString().padStart(2, '0'); // Adding 1 to the month, as it is zero-based
            var day = now.getDate().toString().padStart(2, '0');

            return year + '-' + month + '-' + day;
        }
    },

}
</script>
