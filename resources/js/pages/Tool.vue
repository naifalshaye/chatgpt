<template>
    <div class="container mx-auto">
        <div class="w-full flex flex-col items-center justify-center">
            <div class="mb-2 text-xl">Ask ChatGPT</div>
            <div>
                <div class="text-xs mt-6 mb-8 md:min-h-40">
                    <a :href="'/nova/chatgpt/view-questions-history'"
                       class="bg-transparent hover:text-gray-500 hover:bg-gray-200 text-blue-700 font-semibold  py-1 px-2 border  rounded shadow">
                        View History
                    </a>
                    <a :href="'#'" @click="clearHistory"
                       class="ml-3 bg-transparent hover:bg-gray-200  hover:bg-blue-500 text-blue-700 font-semibold h py-1 px-2 border border-blue-500 hover:border-transparent rounded">
                        Clear History
                    </a>
                    <a href="   https://platform.openai.com/account/api-keys" target="_blank"
                       class="ml-3 bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-1 px-2 border border-blue-500 hover:border-transparent rounded">
                        Get API Key
                    </a>
                    <a href="https://github.com/naifalshaye/chatgpt-nova4" target="_blank"
                       class="ml-3 bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-1 px-2 border border-blue-500 hover:border-transparent rounded">
                        Doc
                    </a>
                </div>
            </div>
            <div v-if="this.error" class="text-red-500 font-bold mt-4 mb-4 flex justify-center text-center">
                {{ this.error }}
            </div>
            <div class="w-full md:w-3/5 mb-8">
                <form @submit.prevent="submitForm" ref="form" method="post" class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="py-4 md:py-6">
                            <div class="text-left text-md mb-2">Question</div>
                            <input type="text" class="w-full form-control form-input form-input-bordered text-center"
                                   v-model="question" placeholder="Type in your question.."
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
            <div class="w-full md:w-3/5 mb-8">
                <div class="min-h-[90px] bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="text-left text-md mb-2">Answer</div>
                    <p class="justify-start text-md whitespace-pre-wrap pl-2"> {{ answer.trim() }} </p>
                </div>
                <div class="flex mt-2">
                    <div class="w-1/2">
                        Tokens: {{ total_tokens ? total_tokens : 0 }}
                    </div>
                    <div class="w-4/5">
                        <div class="flex justify-end">
                            <div>
                                <button @click="copy(this.answer)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import useClipboard from 'vue-clipboard3'
import GithubButton from 'vue-github-button'

export default {
    components: {useClipboard, GithubButton},
    setup() {
        const {toClipboard} = useClipboard()
        const copy = async (answer) => {
            try {
                if (answer) {
                    await toClipboard(answer)
                }
            } catch (e) {
                alert(e.message);
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
            error: ''
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
                    if (data.api_response_error) {
                        this.error = data.api_response_error;
                    } else if (data.exception) {
                        this.error = data.exception_message;
                    } else {
                        this.answer = data.answer;
                        this.total_tokens = data.total_tokens;
                    }
                })
        },
        clearHistory() {
            Nova.request().post('/nova-vendor/chatgpt/history/clear', {question: this.question})
                .then(({data}) => {
                })
        },
        enableSubmit() {
            this.submitIsDisabled = false;
        }
    },
}
</script>
