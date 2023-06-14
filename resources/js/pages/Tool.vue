<template>
    <div class="md:w-3/5">
        <div class="mb-2 text-lg text-center">Ask ChatGPT</div>
        <div class="mb-2 text-md">Question</div>
        <form @submit.prevent="submitForm" ref="form" method="post" class="space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex flex-col items-center justify-center py-4 md:py-6">
                    <input type="text" class="form-control form-input form-input-bordered w-4/5 text-center"
                           v-model="question" placeholder="What are the top five tech companies"
                           @input="enableSubmit">
                </div>
                <div class="flex justify-center">
                    <button type="button"
                            class="appearance-none bg-transparent font-bold text-gray-400 hover:text-gray-300 active:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 dark:active:text-gray-600 dark:hover:bg-gray-800 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 appearance-none bg-transparent font-bold text-gray-400 hover:text-gray-300 active:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 dark:active:text-gray-600 dark:hover:bg-gray-800 mr-1"
                            @click="cancel()">
                        Clear
                    </button>
                    <button type="submit" :disabled='this.submitIsDisabled'
                            class="bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 h-8 px-4 shadow">
                        <span>Send</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="md:w-3/5">
        <div class="mb-2 text-md">Answer
            <button @click="copy(this.answer)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-4" fill="none" viewBox="0 0 20 20"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </button>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 min-h-[90px] flex items-center justify-start">
            <p class="justify-start text-md whitespace-pre-wrap pl-2"> {{ answer.trim() }} </p>
        </div>

    </div>
    <div class="text-xs mt-2" v-if="total_tokens">Tokens: {{ total_tokens }}</div>

</template>

<script>
import useClipboard from 'vue-clipboard3'

export default {
    components: {useClipboard},
    setup() {
        const {toClipboard} = useClipboard()
        const copy = async (answer) => {
            try {
                if (answer) {
                    await toClipboard(answer)
                }
            } catch (e) {
                console.log(e.message);
            }
        }
        return {copy}
    },
    data() {
        return {
            question: '',
            answer: '',
            total_tokens: '',
            submitIsDisabled: true,
            formSubmitted: false,
        };
    },
    mounted() {

    },
    methods: {
        async ask(table) {
            if (table.target.value === '') {
                this.columns = [];
            } else {
                this.database_table = table.target.value;
                Nova.request().get('/nova-vendor/chatgpt-seeder/columns/' + table.target.value, {}).then(({data}) => {
                    this.columns = data.columns
                })
            }
        },
        cancel() {
            this.$refs.form.reset();
            this.question = '';
            this.answer = '';
            this.total_tokens = '';
        },
        submitForm() {
            Nova.request().post('/nova-vendor/chatgpt/ask', {question: this.question})
                .then(({data}) => {
                    // this.answer = this.formatStringAsList(data.answer);
                    this.answer = data.answer;
                    this.total_tokens = data.total_tokens;
                })
        },
        enableSubmit() {
            this.submitIsDisabled = false;
        }
    },
}
</script>
