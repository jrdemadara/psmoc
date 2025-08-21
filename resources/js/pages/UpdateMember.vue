<script setup lang="ts">
import { PropType, ref } from 'vue';
import UpdateAddress from './profile/UpdateAddress.vue';
import UpdateApplicationDetails from './profile/UpdateApplicationDetails.vue';
import UpdateFirearms from './profile/UpdateFirearms.vue';
import UpdateGunClubs from './profile/UpdateGunClubs.vue';
import UpdatePersonalDetails from './profile/UpdatePersonalDetails.vue';
import UpdatePhotoSignature from './profile/UpdatePhotoSignature.vue';
import UpdateWorkDetails from './profile/UpdateWorkDetails.vue';

const isOpen = ref(false);

const currentNav = ref(0);

interface Gunclub {
    id: number;
    name: string;
}

interface GunclubMember {
    id: number;
    gunclub: Gunclub;
    years_no: number;
    is_main: boolean;
}

interface Firearm {
    id: number;
    type: string;
    make: string;
    model: string;
    caliber: string;
    serial_no: string;
}

interface Profile {
    id: number;
    qrcode: string;
    reg_type: string;
    licensed_shooter: string;
    application_venue: string;
    ltopf_no: string;
    license_type: string;

    last_name: string;
    first_name: string;
    middle_name: string | null;
    extension: string | null;

    birth_date: string;
    birth_place: string;
    age: number;
    gender: string;
    civil_status: string;
    blood_type: string;
    email: string;
    phone: string;

    region: string;
    province: string;
    city_municipality: string;
    barangay: string;
    street: string;
    purok: string;

    occupation: string;
    company_organization: string | null;
    position: string | null;
    office_business_address: string | null;
    office_landline: string | null;
    office_email: string | null;

    photo: string;
    signature: string;

    gunclub_members: GunclubMember[];
    firearms: Firearm[];
}

const props = defineProps({
    profile: {
        type: Object as PropType<Profile>,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
    gunClubs: {
        type: Array as PropType<Gunclub[]>,
        required: true,
    },
});

const applicationDetailsData = {
    reg_type: props.profile.reg_type,
    licensed_shooter: props.profile.licensed_shooter,
    application_venue: props.profile.application_venue,
    ltopf_no: props.profile.ltopf_no,
    license_type: props.profile.license_type,
};

const personalDetailsData = {
    last_name: props.profile.last_name,
    first_name: props.profile.first_name,
    middle_name: props.profile.middle_name ?? '',
    extension: props.profile.extension ?? 'None',
    email: props.profile.email,
    phone: props.profile.phone,
    birth_date: props.profile.birth_date,
    birth_place: props.profile.birth_place,
    age: props.profile.age,
    gender: props.profile.gender,
    civil_status: props.profile.civil_status,
    blood_type: props.profile.blood_type,
};

const addressData = {
    region: props.profile.region,
    province: props.profile.province,
    city_municipality: props.profile.city_municipality,
    barangay: props.profile.barangay,
    purok: props.profile.purok,
    street: props.profile.street,
};

const workDetailsData = {
    occupation: props.profile.occupation,
    company_organization: props.profile.company_organization || '',
    position: props.profile.position || '',
    office_business_address: props.profile.office_business_address || '',
    office_landline: props.profile.office_landline || '',
    office_email: props.profile.office_email || '',
};

const photoSignatureData = {
    photo: props.profile.photo,
    signature: props.profile.signature,
};

const gunclubsData = {
    gunclub: props.gunClubs,
    member_gunclubs: props.profile.gunclub_members || [],
};

const firearmsData = {
    firearms: props.profile.firearms || [],
};
</script>

<template>
    <div class="flex h-screen flex-col bg-zinc-900">
        <!-- Sticky header -->
        <header class="sticky top-0 left-0 z-20 w-full border-b border-b-zinc-800 bg-zinc-900 text-white">
            <div class="flex flex-wrap items-center justify-between px-4 py-3 lg:px-16">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <img src="../../assets/images/logo.png" alt="Logo" class="h-10 w-auto" />
                    <h4 class="text-lg font-medium">Update Profile</h4>
                </div>

                <!-- Hamburger -->
                <button @click="isOpen = !isOpen" class="text-zinc-50 focus:outline-none lg:hidden">
                    <svg v-if="!isOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Navigation -->
                <nav
                    :class="[
                        'flex w-full flex-col items-start text-center text-sm font-semibold whitespace-nowrap text-zinc-50 capitalize lg:flex lg:w-auto lg:flex-row lg:items-center lg:justify-center lg:gap-5 lg:space-y-0',
                        isOpen ? 'mt-5 flex' : 'hidden lg:flex',
                    ]"
                    class="transition-all duration-300 ease-in-out"
                >
                    <button
                        @click="currentNav = 0"
                        class="flex w-full justify-start px-5 py-1.5 capitalize hover:text-primary"
                        :class="currentNav == 0 ? 'bg-primary lg:bg-transparent lg:text-primary' : ''"
                    >
                        Application Details
                    </button>
                    <button
                        @click="currentNav = 1"
                        class="flex w-full justify-start px-5 py-1.5 capitalize hover:text-primary"
                        :class="currentNav == 1 ? 'bg-primary lg:bg-transparent lg:text-primary' : ''"
                    >
                        Personal Details
                    </button>
                    <button
                        @click="currentNav = 2"
                        class="flex w-full justify-start px-5 py-1.5 capitalize hover:text-primary"
                        :class="currentNav == 2 ? 'bg-primary lg:bg-transparent lg:text-primary' : ''"
                    >
                        Address
                    </button>
                    <button
                        @click="currentNav = 3"
                        class="flex w-full justify-start px-5 py-1.5 capitalize hover:text-primary"
                        :class="currentNav == 3 ? 'bg-primary lg:bg-transparent lg:text-primary' : ''"
                    >
                        Work
                    </button>
                    <button
                        @click="currentNav = 4"
                        class="flex w-full justify-start px-5 py-1.5 capitalize hover:text-primary"
                        :class="currentNav == 4 ? 'bg-primary lg:bg-transparent lg:text-primary' : ''"
                    >
                        Photo & Signature
                    </button>
                    <button
                        @click="currentNav = 5"
                        class="flex w-full justify-start px-5 py-1.5 capitalize hover:text-primary"
                        :class="currentNav == 5 ? 'bg-primary lg:bg-transparent lg:text-primary' : ''"
                    >
                        Gun Clubs
                    </button>
                    <button
                        @click="currentNav = 6"
                        class="flex w-full justify-start px-5 py-1.5 capitalize hover:text-primary"
                        :class="currentNav == 6 ? 'bg-primary lg:bg-transparent lg:text-primary' : ''"
                    >
                        Firearms
                    </button>
                </nav>
            </div>
        </header>

        <!-- Scrollable content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Example long content -->
            <div class="space-y-4 p-6">
                <div v-if="currentNav == 0">
                    <UpdateApplicationDetails :resubmit="false" :token="props.token" :data="applicationDetailsData" />
                </div>

                <div v-if="currentNav == 1">
                    <UpdatePersonalDetails :resubmit="false" :token="props.token" :data="personalDetailsData" />
                </div>

                <div v-if="currentNav == 2">
                    <UpdateAddress :resubmit="false" :token="props.token" :data="addressData" />
                </div>

                <div v-if="currentNav == 3">
                    <UpdateWorkDetails :resubmit="false" :token="props.token" :data="workDetailsData" />
                </div>

                <div v-if="currentNav == 4">
                    <UpdatePhotoSignature :resubmit="false" :token="props.token" :data="photoSignatureData" />
                </div>

                <div v-if="currentNav == 5">
                    <UpdateGunClubs :resubmit="false" :token="props.token" :data="gunclubsData" />
                </div>
                <div v-if="currentNav == 6">
                    <UpdateFirearms :resubmit="false" :token="props.token" :data="firearmsData" />
                </div>
            </div>
        </div>
    </div>
</template>
