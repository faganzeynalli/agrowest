<template>
    <profile-header
        v-bind="props"
        :station="profileInfo.station"
    />

    <div
        id="profile"
        class="row row-of-cards"
    >
        <div class="col-lg-7">
            <template v-if="hasStarted">
                <profile-now-playing
                    v-bind="props"
                />

                <profile-schedule
                    :schedule-items="profileInfo.schedule"
                />

                <profile-streams
                    :station="profileInfo.station"
                />
            </template>
            <template v-else>
                <now-playing-not-started-panel />
            </template>
            <!-- fagan edit
            <profile-public-pages
                v-bind="props"
            />
        -->
        </div>

        <div class="col-lg-5">
            <!-- fagan edit
            <profile-requests
                v-if="stationSupportsRequests"
                v-bind="props"
            />

            <profile-streamers
                v-if="stationSupportsStreamers"
                v-bind="props"
            />
            -->

            <template v-if="hasActiveFrontend">
                <profile-frontend
                    v-bind="props"
                    :frontend-running="profileInfo.services.frontend_running"
                />
            </template>

            <template v-if="hasActiveBackend">
                <profile-backend
                    v-bind="props"
                    :backend-running="profileInfo.services.backend_running"
                />
            </template>
            <template v-else>
                <profile-backend-none />
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import ProfileStreams from "~/components/Stations/Profile/StreamsPanel.vue";
import ProfileHeader, {ProfileHeaderPanelParentProps} from "~/components/Stations/Profile/HeaderPanel.vue";
import ProfileNowPlaying, {ProfileNowPlayingPanelProps} from "~/components/Stations/Profile/NowPlayingPanel.vue";
import ProfileSchedule from "~/components/Stations/Profile/SchedulePanel.vue";
import ProfileRequests, {ProfileRequestPanelProps} from "~/components/Stations/Profile/RequestsPanel.vue";
import ProfileStreamers, {ProfileStreamersPanelProps} from "~/components/Stations/Profile/StreamersPanel.vue";
import ProfilePublicPages, {ProfilePublicPagesPanelProps} from "~/components/Stations/Profile/PublicPagesPanel.vue";
import ProfileFrontend, {ProfileFrontendPanelParentProps} from "~/components/Stations/Profile/FrontendPanel.vue";
import ProfileBackendNone from "~/components/Stations/Profile/BackendNonePanel.vue";
import ProfileBackend, {ProfileBackendPanelParentProps} from "~/components/Stations/Profile/BackendPanel.vue";
import NowPlayingNotStartedPanel from "~/components/Stations/Profile/NowPlayingNotStartedPanel.vue";
import {BackendAdapters, FrontendAdapters} from "~/entities/RadioAdapters";
import NowPlaying from "~/entities/NowPlaying";
import {computed} from "vue";
import {useAxios} from "~/vendor/axios";
import useAutoRefreshingAsyncState from "~/functions/useAutoRefreshingAsyncState.ts";

export interface EnabledProfileProps extends ProfileBackendPanelParentProps,
    ProfileFrontendPanelParentProps,
    ProfileHeaderPanelParentProps,
    ProfileNowPlayingPanelProps,
    ProfileRequestPanelProps,
    ProfilePublicPagesPanelProps,
    ProfileStreamersPanelProps {
    profileApiUri: string,
    stationSupportsRequests: boolean,
    stationSupportsStreamers: boolean
}

const props = defineProps<EnabledProfileProps>();

const hasActiveFrontend = computed(() => {
    return props.frontendType !== FrontendAdapters.Remote;
});

const hasActiveBackend = computed(() => {
    return props.backendType !== BackendAdapters.None;
});

const {axiosSilent} = useAxios();

const {state: profileInfo} = useAutoRefreshingAsyncState(
    () => axiosSilent.get(props.profileApiUri).then((r) => r.data),
    {
        station: {
            ...NowPlaying.station
        },
        services: {
            backend_running: false,
            frontend_running: false,
            has_started: false,
            needs_restart: false
        },
        schedule: []
    },
    {
        timeout: 15000
    }
);
</script>
