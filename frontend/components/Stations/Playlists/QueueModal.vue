<template>
    <modal
        id="queue_modal"
        ref="$modal"
        size="lg"
        :title="$gettext('Playback Queue')"
        :busy="loading"
        @hidden="onHidden"
    >
        <p>
            {{
                $gettext('This queue contains the remaining tracks in the order they will be queued by the Aplus AutoDJ (if the tracks are eligible to be played).')
            }}
        </p>

        <table class="table table-striped sortable mb-0">
            <thead>
                <tr>
                    <th style="width: 50%;">
                        {{ $gettext('Title') }}
                    </th>
                    <th style="width: 50%;">
                        {{ $gettext('Artist') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in media"
                    :key="row.id"
                    class="align-middle"
                >
                    <td>
                        <span class="typography-subheading">{{ row.title }}</span>
                    </td>
                    <td>{{ row.artist }}</td>
                </tr>
            </tbody>
        </table>

        <template #modal-footer>
            <button
                class="btn btn-secondary"
                type="button"
                @click="hide"
            >
                {{ $gettext('Close') }}
            </button>
            <button
                class="btn btn-danger"
                type="submit"
                @click="doClear"
            >
                {{ $gettext('Clear Queue') }}
            </button>
        </template>
    </modal>
</template>

<script setup lang="ts">
import {ref, useTemplateRef} from "vue";
import {useAxios} from "~/vendor/axios";
import {useNotify} from "~/functions/useNotify";
import {useTranslate} from "~/vendor/gettext";
import Modal from "~/components/Common/Modal.vue";
import {useHasModal} from "~/functions/useHasModal.ts";

const loading = ref(true);
const queueUrl = ref(null);
const media = ref([]);

const $modal = useTemplateRef('$modal');
const {show, hide} = useHasModal($modal);

const {axios} = useAxios();

const open = (newQueueUrl) => {
    queueUrl.value = newQueueUrl;
    loading.value = true;

    void axios.get(newQueueUrl).then((resp) => {
        media.value = resp.data;
        loading.value = false;
    });

    show();
};

const onHidden = () => {
    loading.value = false;
    queueUrl.value = null;
}

const {notifySuccess} = useNotify();
const {$gettext} = useTranslate();

const doClear = () => {
    void axios.delete(queueUrl.value).then(() => {
        notifySuccess($gettext('Playlist queue cleared.'));
        hide();
    });
};

defineExpose({
    open
});
</script>
