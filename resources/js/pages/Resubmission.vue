<script setup lang="ts">
import { PropType } from 'vue';

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
    fields: {
        type: Object as PropType<Record<string, any>>,
        required: true,
    },
});

console.log(props);

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
                    <h4 class="text-lg font-medium">Resubmission</h4>
                </div>
            </div>
        </header>

        <!-- Scrollable content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Example long content -->
            <div class="space-y-4 p-6">
                <div>
                    Resubmission
                    <!-- <UpdateApplicationDetails :token="props.token" :data="applicationDetailsData" /> -->
                </div>
            </div>
        </div>
    </div>
</template>
