<script setup lang="ts">
import AppLogoSecondary from '@/components/AppLogoSecondary.vue';
import bulletsCover from '../../assets/images/bullets-cover.svg';

interface Ranking {
    id: number;
    match: string | null;
    division: string | null;
    category: string | null;
    score: number;
}

interface Gunclub {
    id: number;
    name: string;
}

interface Profile {
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    photo: string | null;
    main_gunclub: Gunclub | null;
    rankings: Ranking[];
}

interface Props {
    profile: Profile;
    token?: string; // only if you’re also passing token from controller
}

const props = defineProps<Props>();

console.log(props);
</script>

<template>
    <div class="flex h-screen w-screen flex-col bg-zinc-900">
        <div class="flex items-center space-x-2 p-5">
            <img src="../../assets/images/logo.png" alt="Logo" class="h-14 w-auto" />
            <AppLogoSecondary />
        </div>
        <div class="relative flex h-full flex-col bg-cover bg-center" :style="`background-image: url(${bulletsCover})`">
            <div class="overflow-hidden rounded-2xl border-2 border-zinc-800">
                <img :src="props.profile.photo ?? ''" alt="Profile Photo" class="h-full w-full object-cover" />
            </div>
            <div class="justify-top mt-52 flex h-full w-full flex-col items-center bg-zinc-800/20">
                <h4 class="text-2xl capitalize">{{ props.profile.last_name }} {{ props.profile.last_name }}</h4>
            </div>
        </div>
    </div>
</template>
