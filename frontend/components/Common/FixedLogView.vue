<template>
    <loading :loading="isLoading">
        <div style="height: 300px; resize: vertical; overflow: auto !important;">
            <code-mirror
                id="log-view-contents"
                v-model="logs"
                readonly
                basic
                :dark="isDark"
            />
        </div>
    </loading>
</template>

<script setup lang="ts">
import {ref, toRef, watch} from "vue";
import {useAxios} from "~/vendor/axios";
import Loading from "~/components/Common/Loading.vue";
import CodeMirror from "vue-codemirror6";
import useTheme from "~/functions/theme";

const props = defineProps<{
    logUrl: string
}>();

const isLoading = ref(false);
const logs = ref('');

const {isDark} = useTheme();

const {axios} = useAxios();

watch(toRef(props, 'logUrl'), (newLogUrl) => {
    isLoading.value = true;
    logs.value = '';

    if (null !== newLogUrl) {
        void axios({
            method: 'GET',
            url: props.logUrl
        }).then((resp) => {
            if (resp.data.contents !== '') {
                logs.value = resp.data.contents;
            }
        }).finally(() => {
            isLoading.value = false;
        });
    }
}, {immediate: true});

const getContents = () => {
    return logs.value;
};

defineExpose({
    getContents
});
</script>
