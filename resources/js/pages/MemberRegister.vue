<script setup lang="ts">
import AppLogoSecondary from '@/components/AppLogoSecondary.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Link, useForm } from '@inertiajs/vue3';
import { VueSignaturePad } from '@selemondev/vue3-signature-pad';
import axios from 'axios';
import * as icons from 'lucide-vue-next';
import {
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectItemIndicator,
    SelectItemText,
    SelectLabel,
    SelectPortal,
    SelectRoot,
    SelectScrollDownButton,
    SelectScrollUpButton,
    SelectTrigger,
    SelectValue,
    SelectViewport,
    ToastAction,
    ToastDescription,
    ToastProvider,
    ToastRoot,
    ToastTitle,
    ToastViewport,
} from 'reka-ui';
import type { FunctionalComponent } from 'vue';
import { computed, nextTick, onMounted, onUnmounted, PropType, reactive, ref, Ref, watch } from 'vue';
import cameraSound from '../../assets/audio/camera.mp3';
import bulletsCover from '../../assets/images/bullets-cover.svg';
const props = defineProps({
    gunClubs: {
        type: Array as PropType<{ id: number; name: string }[]>,
        required: true,
    },
});

const successToastOpen = ref(false);
const errorToastOpen = ref(false);
const eventDateRef = ref(new Date());
const timerRef = ref(0);

function oneWeekAway() {
    const now = new Date();
    const inOneWeek = now.setDate(now.getDate() + 7);
    return new Date(inOneWeek);
}

function popSuccessToast() {
    successToastOpen.value = false;
    window.clearTimeout(timerRef.value);
    timerRef.value = window.setTimeout(() => {
        eventDateRef.value = oneWeekAway();
        successToastOpen.value = true;
    }, 100);
}

function popErrorToast() {
    errorToastOpen.value = false;
    window.clearTimeout(timerRef.value);
    timerRef.value = window.setTimeout(() => {
        eventDateRef.value = oneWeekAway();
        errorToastOpen.value = true;
    }, 100);
}

type StepInfo = {
    title: string;
    tag: string;
    icon: string;
};

const stepData: StepInfo[] = [
    { title: 'Application Type', tag: 'Select application type', icon: 'FilePlus' },
    { title: 'Personal Information', tag: 'Enter personal details', icon: 'UserRound' },
    { title: 'Home Address', tag: 'Confirm your address', icon: 'MapPinHouse' },
    { title: 'Work Details', tag: 'Provide work information', icon: 'BriefcaseBusiness' },
    { title: 'Photo & Signature', tag: 'Upload photo and signature', icon: 'Signature' },
    { title: 'Gun Club', tag: 'Choose your gun club', icon: 'Goal' },
    { title: 'Firearms Details', tag: 'List your firearms', icon: 'Crosshair' },
    { title: 'Review & Submit', tag: 'Confirm all your details', icon: 'CheckCircle' },
];

const step = ref<number>(0);

const stepTitle = computed(() => stepData[step.value]?.title ?? '');
const stepTag = computed(() => stepData[step.value]?.tag ?? '');
const stepIcon = computed(() => stepData[step.value]?.icon ?? '');

const totalSteps = stepData.length;
function nextStep(): void {
    if (validateStep(step.value)) {
        if (step.value < totalSteps - 1) {
            step.value++;
        }
    } else {
        alert('Please complete all required fields before proceeding.');
    }
}

function prevStep(): void {
    if (step.value > 0) {
        step.value--;
    }
}

const IconComponent = computed<FunctionalComponent>(() => {
    const iconName = stepIcon.value;
    return icons[iconName as keyof typeof icons] as FunctionalComponent;
});

const form = useForm({
    //step 0 - Application Details
    application_venue: '',
    licensed_shooter: null,
    ltopf_no: '',
    license_type: '',

    // step 1 - Personal Details
    last_name: '',
    first_name: '',
    middle_name: '',
    extension: '',
    email: '',
    phone: '',
    birth_date: '',
    birth_place: '',
    age: '',
    gender: '',
    civil_status: '',
    blood_type: '',

    //step 2 - Address Details
    street: '',
    purok: '',
    barangay: '',
    city_municipality: '',
    province: '',
    region: '',

    //step 3 - Work Details
    occupation: '',
    company_organization: '',
    position: '',
    office_business_address: '',
    office_landline: '',
    office_email: '',

    //step 4 - Photo & Signature
    photo: null as File | null,
    signature: null as File | null,

    //step 5 - Gun Clubs
    gunclubs: [] as {
        gunclub_id: number;
        years_no: number;
        is_main: boolean;
    }[],

    // step 6 - Firearms
    firearms: [] as {
        type: string;
        make: string;
        model: string;
        caliber: string;
        serial_no: string;
    }[],
});

const isStep0Valid = ref(false);
const isStep1Valid = ref(false);
const isStep2Valid = ref(false);
const isStep3Valid = ref(false);
const isStep4Valid = ref(false);
const isStep5Valid = ref(false);
const isStep6Valid = ref(false);

function validateStep(currentStep: number): boolean {
    let valid = false;

    switch (currentStep) {
        case 0:
            valid = !!form.application_venue && form.licensed_shooter !== null && !!form.ltopf_no && !!form.license_type;
            isStep0Valid.value = valid;
            return valid;
        case 1:
            valid =
                !!form.last_name &&
                !!form.first_name &&
                !!form.middle_name &&
                !!form.birth_date &&
                !!form.birth_place &&
                !!form.age &&
                !!form.gender &&
                !!form.civil_status &&
                !!form.blood_type &&
                !!form.email &&
                !!form.phone;
            isStep1Valid.value = valid;
            return valid;
        case 2:
            valid = !!form.street && !!form.purok && !!form.barangay && !!form.city_municipality && !!form.province && !!form.region;
            isStep2Valid.value = valid;
            return valid;
        case 3:
            valid =
                !!form.occupation &&
                !!form.company_organization &&
                !!form.position &&
                !!form.office_business_address &&
                !!form.office_landline &&
                !!form.office_email;
            isStep3Valid.value = valid;
            return valid;
        case 4:
            valid = form.photo !== null && form.signature !== null;
            isStep4Valid.value = valid;
            return valid;
        case 5:
            valid = form.gunclubs.length > 0;
            isStep5Valid.value = valid;
            return valid;
        case 6:
            valid = form.firearms.length > 0;
            isStep6Valid.value = valid;
            return valid;
        default:
            return true;
    }
}

const licensedShooterOptions = ['Yes', 'No'];
const licenseTypeOptions = ['Type 1', 'Type 2', 'Type 3', 'Type 4', 'Type 5'];
const genderOptions = ['Male', 'Female', 'Others'];
const civilStatusOptions = ['Single', 'Married', 'Separated', 'Divorced', 'Widowed'];
const bloodTypeOptions = ['O+', 'O−', 'A+', 'A−', 'B+', 'B−', 'AB+', 'AB−', 'A1', 'A2', 'A1B', 'A2B'];
const firearmTypeOptions = [
    'Pistol',
    'Revolver',
    'Shotgun',
    'Rifle',
    'Submachine Gun',
    'Assault Rifle',
    'Sniper Rifle',
    'Carbine',
    'Machine Gun',
    'Other',
];

const isSubmitSuccess = ref<boolean>(false);
const formErrorMessage = ref<string>('');
const submit = () => {
    form.post(route('register-member'), {
        forceFormData: true,
        onSuccess: () => {
            isSubmitSuccess.value = true;
            popSuccessToast();
            //form.reset();
        },
        onError: (errors) => {
            formErrorMessage.value = Object.values(errors)[0]?.[0] ?? 'Something went wrong';
            popErrorToast();
            console.error('Validation failed:', errors);
        },
    });
};

interface Location {
    code: string;
    name: string;
}

// Loading flags
const isRegionLoading = ref<boolean>(false);
const isProvinceLoading = ref<boolean>(false);
const isCityLoading = ref<boolean>(false);
const isBarangayLoading = ref<boolean>(false);
const isProvinceRequired = ref<boolean>(false);

// Data lists
const regionsList = ref<Location[]>([]);
const provincesList = ref<Location[]>([]);
const citiesList = ref<Location[]>([]);
const barangaysList = ref<Location[]>([]);

function getAddressCodeByName(obj: Ref<Location[]>, name: string): string {
    const match = obj.value.find((loc) => loc.name.trim() === name.trim());
    return match ? match.code : 'Not found';
}

const handleRegionChange = async (regionName: string) => {
    const code = getAddressCodeByName(regionsList, regionName);
    if (!code) return;

    if (code === '1300000000') {
        isProvinceRequired.value = false;
        isCityLoading.value = true;

        try {
            const { data } = await axios.get<Location[]>(`api/proxy/municipalities/${code}`);
            citiesList.value = data.filter((item) => item.code !== '1380600000');
        } catch (e) {
            console.error('Error loading cities:', e);
        } finally {
            isCityLoading.value = false;
        }
    } else {
        isProvinceLoading.value = true;

        try {
            const { data } = await axios.get<Location[]>(`api/proxy/provinces/${code}`);
            provincesList.value = data;
        } catch (e) {
            console.error('Error loading provinces:', e);
        } finally {
            isProvinceLoading.value = false;
        }
    }
};

const handleProvinceChange = async (provinceName: string) => {
    citiesList.value = [];
    barangaysList.value = [];
    console.log(provinceName);
    form.province = provinceName;

    const code = getAddressCodeByName(provincesList, provinceName);
    if (!code) return;

    isCityLoading.value = true;

    try {
        const { data } = await axios.get<Location[]>(`api/proxy/municipalities/${code}`);
        citiesList.value = data;
    } catch (e) {
        console.error('Error loading provinces:', e);
    } finally {
        isCityLoading.value = false;
    }
};

const handleCityChange = async (cityName: string) => {
    barangaysList.value = [];
    form.city_municipality = cityName;

    const code = getAddressCodeByName(citiesList, cityName);
    if (!code) return;

    isBarangayLoading.value = true;
    try {
        const { data } = await axios.get<Location[]>(`api/proxy/barangays/${code}`);
        barangaysList.value = data;
    } catch (e) {
        console.error('Error loading provinces:', e);
    } finally {
        isBarangayLoading.value = false;
    }
};

watch(
    () => form.region,
    (newVal: string) => {
        provincesList.value = [];
        citiesList.value = [];
        barangaysList.value = [];
        form.region = newVal;
        handleRegionChange(newVal);
    },
);

watch(
    () => form.province,
    (newVal: string) => {
        citiesList.value = [];
        barangaysList.value = [];
        form.province = newVal;
        handleProvinceChange(newVal);
    },
);

watch(
    () => form.city_municipality,
    (newVal: string) => {
        barangaysList.value = [];
        form.city_municipality = newVal;
        handleCityChange(newVal);
    },
);

const state = ref({
    options: {
        penColor: 'rgb(0, 0, 0)',
        backgroundColor: 'rgb(124, 124, 134)',
    },
    disabled: false,
});

const signature = ref();

const handleSave = (format = 'image/png') => {
    const base64 = signature.value.saveSignature(format);
    const blob = dataURLToBlob(base64);
    const file = new File([blob], 'signature.png', { type: blob.type });

    form.signature = file;
};

const handleClear = () => {
    return signature.value.clearCanvas();
};
const handleUndo = () => {
    return signature.value.undo();
};

const newGunClub = reactive({
    gunclub_id: undefined as number | undefined,
    years_no: undefined as number | undefined,
    is_main: false,
});

const addGunClub = () => {
    const { gunclub_id, years_no, is_main } = newGunClub;

    if (gunclub_id !== undefined && years_no !== undefined) {
        form.gunclubs.push({
            gunclub_id,
            years_no,
            is_main,
        });

        // Reset
        Object.assign(newGunClub, {
            gunclub_id: null,
            years_no: null,
            is_main: false,
        });
    } else {
        alert('Complete all gun club fields with valid numbers.');
    }
};

const query = ref('');
const filteredOptions = computed(() => {
    return props.gunClubs.filter((club) => {
        const alreadySelected = form.gunclubs.some((selected) => selected.gunclub_id === club.id);
        return !alreadySelected && club.name.toLowerCase().includes(query.value.toLowerCase());
    });
});

watch(
    () => form.gunclubs,
    () => {
        query.value = '';
    },
    { deep: true },
);

type FirearmField = string | null;

const newFirearm = reactive<Record<'type' | 'make' | 'model' | 'caliber' | 'serial_no', FirearmField>>({
    type: null,
    make: null,
    model: null,
    caliber: null,
    serial_no: null,
});

const addFirearm = () => {
    const { type, make, model, caliber, serial_no } = newFirearm;

    if (type && make && model && caliber && serial_no) {
        form.firearms.push({
            type,
            make,
            model,
            caliber,
            serial_no, // you had a typo here, wrote `serial`
        });

        // Reset all fields
        Object.keys(newFirearm).forEach((key) => {
            newFirearm[key as keyof typeof newFirearm] = null;
        });
    } else {
        alert('Complete all firearm fields first.');
    }
};

const videoRef = ref<HTMLVideoElement | null>(null);
const photo = ref<string | null>(null);
const canvas = ref<HTMLCanvasElement | null>(null);
let stream: MediaStream | null = null;
const cameraState = ref<'idle' | 'active' | 'capture'>('idle');
const captureSoundSrc = cameraSound;

const startCamera = async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true });
        cameraState.value = 'active';
        await nextTick(); // Wait for DOM to render the videoRef
        if (videoRef.value) {
            videoRef.value.srcObject = stream;
            await videoRef.value.play();
        }
    } catch (err) {
        console.error('Failed to access camera:', err);
    }
};
const capturePhoto = () => {
    if (videoRef.value && canvas.value) {
        const context = canvas.value.getContext('2d');
        if (context) {
            canvas.value.width = videoRef.value.videoWidth;
            canvas.value.height = videoRef.value.videoHeight;

            // Flip the canvas horizontally
            context.translate(canvas.value.width, 0);
            context.scale(-1, 1);

            // Draw the mirrored video frame
            context.drawImage(videoRef.value, 0, 0, canvas.value.width, canvas.value.height);

            photo.value = canvas.value.toDataURL('image/jpeg');

            const blob = dataURLToBlob(photo.value);
            form.photo = new File([blob], 'photo.jpeg', {
                type: 'image/jpeg',
            });

            const audioElement = document.querySelector('audio') as HTMLAudioElement;
            if (audioElement) audioElement.play();

            cameraState.value = 'capture';

            if (stream) {
                stream.getTracks().forEach((track) => track.stop());
            }
        }
    }
};

function dataURLToBlob(dataUrl: string): Blob {
    const byteString = atob(dataUrl.split(',')[1]);
    const mimeString = dataUrl.split(',')[0].split(':')[1].split(';')[0];
    const buffer = new ArrayBuffer(byteString.length);
    const uintArray = new Uint8Array(buffer);

    for (let i = 0; i < byteString.length; i++) {
        uintArray[i] = byteString.charCodeAt(i);
    }

    return new Blob([uintArray], { type: mimeString });
}

function formatPhone(e: any) {
    let input = e.target.value;

    // Remove all non-digit characters
    input = input.replace(/\D/g, '');

    // Limit to 11 digits
    input = input.substring(0, 11);

    // Format as 4-3-4 (e.g. 0997 243 0944)
    if (input.length >= 8) {
        input = input.replace(/(\d{4})(\d{3})(\d{0,4})/, '$1 $2 $3');
    } else if (input.length >= 5) {
        input = input.replace(/(\d{4})(\d{0,3})/, '$1 $2');
    }

    form.phone = input.trim();
}

onMounted(() => {
    isRegionLoading.value = true;
    axios
        .get('api/proxy/regions')
        .then(function (response) {
            regionsList.value = response.data;
            isRegionLoading.value = false;
        })
        .catch(function () {
            isRegionLoading.value = false;
        });
});

// Cleanup when component unmounts
onUnmounted(() => {
    if (stream) {
        stream.getTracks().forEach((track) => track.stop());
    }
});
</script>

<template>
    <Head title="Register">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex h-screen w-screen text-[#1b1b18] dark:text-white">
        <div class="relative flex h-full w-[580px] flex-col justify-between overflow-hidden bg-zinc-200 p-10 dark:bg-zinc-900">
            <!-- Background image layer with opacity -->
            <div class="absolute inset-0 bg-cover bg-center opacity-5" :style="`background-image: url(${bulletsCover})`"></div>

            <!-- Foreground content -->
            <div class="relative z-10 flex h-full flex-col justify-between">
                <div class="flex w-fit items-center justify-start space-x-2">
                    <img src="../../assets/images/logo.png" alt="Logo" class="h-16" />
                    <AppLogoSecondary class="w-64 text-zinc-950 dark:text-zinc-50" />
                </div>

                <div>
                    <h4 class="h-fit text-xl font-medium text-zinc-950 dark:text-zinc-50">Member Registration</h4>
                    <div class="mx-5 mt-8 flex flex-col items-start justify-start space-y-2">
                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.FilePlus class="text-primary" />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h2 class="text-sm font-bold">Application Type</h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">How you'd like to proceed?</p>
                            </div>
                            <span v-if="isStep0Valid == null">
                                <icons.Loader2 class="animate-spin" :size="24" />
                            </span>
                            <span v-else-if="isStep0Valid">
                                <icons.Check class="text-green-600" :size="24" />
                            </span>
                            <span v-else>
                                <icons.X class="text-red-600" :size="24" />
                            </span>
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.UserRound />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h2 class="text-sm font-bold">Personal Information</h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Tell us about yourself</p>
                            </div>
                            <span v-if="isStep1Valid == null">
                                <icons.Loader2 class="animate-spin" :size="24" />
                            </span>
                            <span v-else-if="isStep1Valid">
                                <icons.Check class="text-green-600" :size="24" />
                            </span>
                            <span v-else>
                                <icons.X class="text-red-600" :size="24" />
                            </span>
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.MapPinHouse />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h2 class="text-sm font-bold">Home Address</h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Verify your address</p>
                            </div>
                            <span v-if="isStep2Valid == null">
                                <icons.Loader2 class="animate-spin" :size="24" />
                            </span>
                            <span v-else-if="isStep2Valid">
                                <icons.Check class="text-green-600" :size="24" />
                            </span>
                            <span v-else>
                                <icons.X class="text-red-600" :size="24" />
                            </span>
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.BriefcaseBusiness />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h2 class="text-sm font-bold">Work Details</h2>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Tell us about your work</p>
                            </div>
                            <span v-if="isStep3Valid == null">
                                <icons.Loader2 class="animate-spin" :size="24" />
                            </span>
                            <span v-else-if="isStep3Valid">
                                <icons.Check class="text-green-600" :size="24" />
                            </span>
                            <span v-else>
                                <icons.X class="text-red-600" :size="24" />
                            </span>
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.Signature />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h4 class="text-sm font-bold">Photo & Signature</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Verify your identity</p>
                            </div>
                            <span v-if="isStep4Valid == null">
                                <icons.Loader2 class="animate-spin" :size="24" />
                            </span>
                            <span v-else-if="isStep4Valid">
                                <icons.Check class="text-green-600" :size="24" />
                            </span>
                            <span v-else>
                                <icons.X class="text-red-600" :size="24" />
                            </span>
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.Goal />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h4 class="text-sm font-bold">Gun Club</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Select your gun club</p>
                            </div>
                            <span v-if="isStep5Valid == null">
                                <icons.Loader2 class="animate-spin" :size="24" />
                            </span>
                            <span v-else-if="isStep5Valid">
                                <icons.Check class="text-green-600" :size="24" />
                            </span>
                            <span v-else>
                                <icons.X class="text-red-600" :size="24" />
                            </span>
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.Crosshair />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h4 class="text-sm font-bold">Firearms Details</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Indicate your firearms</p>
                            </div>
                            <span v-if="isStep6Valid == null">
                                <icons.Loader2 class="animate-spin" :size="24" />
                            </span>
                            <span v-else-if="isStep6Valid">
                                <icons.Check class="text-green-600" :size="24" />
                            </span>
                            <span v-else>
                                <icons.X class="text-red-600" :size="24" />
                            </span>
                        </div>

                        <div class="flex w-12 items-center justify-center">
                            <div class="mr-0.5 h-8 w-0.5 rounded-full bg-zinc-800"></div>
                        </div>

                        <div class="flex w-full items-center justify-between space-x-2">
                            <div class="flex h-10 w-14 items-center justify-center rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                <icons.CheckCircle />
                            </div>
                            <div class="flex w-full flex-col justify-start">
                                <h4 class="text-sm font-bold">Review & Submit</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Confirm and finish</p>
                            </div>
                            <span v-if="form.processing">
                                <icons.Loader2 class="animate-spin" :size="24" />
                            </span>
                            <span v-else-if="form.wasSuccessful">
                                <icons.Check class="text-green-600" :size="24" />
                            </span>
                            <span v-else-if="form.hasErrors">
                                <icons.X class="text-red-600" :size="24" />
                            </span>
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('home')"
                    class="mt-10 flex h-10 w-32 items-center justify-center space-x-2 text-zinc-900 hover:text-primary/80 dark:text-zinc-50"
                >
                    <icons.ArrowLeft />
                    <span class="cursor-default">Back to main</span>
                </Link>
            </div>
        </div>

        <div class="flex h-full w-full items-center justify-center bg-zinc-100 p-5 dark:bg-zinc-900/80">
            <div>
                <div class="flex">
                    <div class="flex items-center justify-center space-x-2">
                        <div class="rounded bg-zinc-200 p-2 dark:bg-zinc-800">
                            <component :is="IconComponent" class="icon" />
                        </div>

                        <div class="flex flex-col">
                            <h4 class="text-lg font-bold">{{ stepTitle }}</h4>
                            <p class="-mt-1 text-zinc-600 dark:text-zinc-400">{{ stepTag }}</p>
                        </div>
                    </div>
                </div>
                <form @submit.prevent="submit" class="mt-10 flex flex-col">
                    <div v-if="step == 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="application_venue">Application Venue</Label>
                            <Input
                                id="application_venue"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="age"
                                v-model="form.application_venue"
                            />
                            <InputError :message="form.errors.application_venue" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="gender">Licensed Shooter</Label>
                            <SelectRoot v-model="form.licensed_shooter">
                                <SelectTrigger
                                    class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Licensed Shooter </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in licensedShooterOptions"
                                                    :key="index"
                                                    class="data-[highlighted]:text-green1 relative flex h-[25px] cursor-default items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.licensed_shooter" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="ltopf_no">LTOPF No.</Label>
                            <Input id="ltopf_no" type="text" required autofocus :tabindex="1" v-model="form.ltopf_no" />
                            <InputError :message="form.errors.ltopf_no" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="gender">Licensed Type</Label>
                            <SelectRoot v-model="form.license_type">
                                <SelectTrigger
                                    class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Licensed Type </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in licenseTypeOptions"
                                                    :key="index"
                                                    class="data-[highlighted]:text-green1 relative flex h-[25px] cursor-default items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.license_type" />
                        </div>
                    </div>

                    <div v-if="step == 1" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <div class="grid gap-2">
                            <Label for="lastname">Lastname</Label>
                            <Input id="lastname" type="text" required autofocus :tabindex="1" autocomplete="lastname" v-model="form.last_name" />
                            <InputError :message="form.errors.last_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="firstname">Firstname</Label>
                            <Input id="firstname" type="text" required autofocus :tabindex="1" autocomplete="firstname" v-model="form.first_name" />
                            <InputError :message="form.errors.first_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="middlename">Middlename</Label>
                            <Input
                                id="middlename"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="middlename"
                                v-model="form.middle_name"
                            />
                            <InputError :message="form.errors.middle_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="extension">Extension</Label>
                            <Input id="extension" type="text" required autofocus :tabindex="1" autocomplete="extension" v-model="form.extension" />
                            <InputError :message="form.errors.extension" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="birth_date">Birth Date</Label>
                            <Input id="birth_date" type="date" required autofocus :tabindex="1" autocomplete="birth_date" v-model="form.birth_date" />
                            <InputError :message="form.errors.birth_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="birth_place">Birth Place</Label>
                            <Input
                                id="birth_place"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="birth_place"
                                v-model="form.birth_place"
                            />
                            <InputError :message="form.errors.birth_place" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="age">Age</Label>
                            <Input id="age" type="number" required autofocus :tabindex="1" autocomplete="age" v-model="form.age" />
                            <InputError :message="form.errors.age" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="gender">Gender</Label>
                            <SelectRoot v-model="form.gender">
                                <SelectTrigger
                                    class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Gender </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in genderOptions"
                                                    :key="index"
                                                    class="data-[highlighted]:text-green1 relative flex h-[25px] cursor-default items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.gender" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="civil_status">Civil Status</Label>
                            <SelectRoot v-model="form.civil_status">
                                <SelectTrigger
                                    class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Civil Status </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in civilStatusOptions"
                                                    :key="index"
                                                    class="data-[highlighted]:text-green1 relative flex h-[25px] items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.civil_status" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="blood_type">Blood Type</Label>
                            <SelectRoot v-model="form.blood_type">
                                <SelectTrigger
                                    class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Blood Type </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in bloodTypeOptions"
                                                    :key="index"
                                                    class="data-[highlighted]:text-green1 relative flex h-[25px] items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.blood_type" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input id="email" type="email" required autofocus :tabindex="1" autocomplete="email" v-model="form.email" />
                            <InputError :message="form.errors.email" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="phone">Phone</Label>
                            <Input
                                id="phone"
                                @input="formatPhone"
                                type="text"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="tel"
                                v-model="form.phone"
                            />
                            <InputError :message="form.errors.phone" />
                        </div>
                    </div>

                    <div v-if="step == 2" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <div class="grid gap-2">
                            <Label for="street">Region</Label>
                            <SelectRoot v-model="form.region">
                                <SelectTrigger
                                    class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Region </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in regionsList"
                                                    :key="index"
                                                    class="data-[highlighted]:text-green1 relative flex h-[25px] cursor-default items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option.name"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option.name }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.region" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="street">Province</Label>

                            <SelectRoot v-model="form.province">
                                <SelectTrigger
                                    class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Province </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in provincesList"
                                                    :key="index"
                                                    class="data-[highlighted]:text-green1 relative flex h-[25px] cursor-default items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option.name"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option.name }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.province" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="street">City/Municipality</Label>

                            <SelectRoot v-model="form.city_municipality">
                                <SelectTrigger
                                    class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> City/Municipality </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in citiesList"
                                                    :key="index"
                                                    class="data-[highlighted]:text-green1 relative flex h-[25px] cursor-default items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option.name"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option.name }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.city_municipality" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="street">Brangay</Label>

                            <SelectRoot v-model="form.barangay">
                                <SelectTrigger
                                    class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                    aria-label="Customise options"
                                >
                                    <SelectValue placeholder="" />
                                    <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                </SelectTrigger>

                                <SelectPortal>
                                    <SelectContent
                                        class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                        :side-offset="5"
                                    >
                                        <SelectScrollUpButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowUp :size="16" />
                                        </SelectScrollUpButton>

                                        <SelectViewport class="p-[5px]">
                                            <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Barangay </SelectLabel>
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="(option, index) in barangaysList"
                                                    :key="index"
                                                    class="data-[highlighted]:text-green1 relative flex h-[25px] cursor-default items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                    :value="option.name"
                                                >
                                                    <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                        <Icon icon="radix-icons:check" />
                                                    </SelectItemIndicator>
                                                    <SelectItemText>
                                                        {{ option.name }}
                                                    </SelectItemText>
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectViewport>

                                        <SelectScrollDownButton
                                            class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                        >
                                            <icons.ArrowDown :size="16" />
                                        </SelectScrollDownButton>
                                    </SelectContent>
                                </SelectPortal>
                            </SelectRoot>
                            <InputError :message="form.errors.barangay" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="purok">Purok</Label>
                            <Input id="purok" type="text" required autofocus :tabindex="1" autocomplete="purok" v-model="form.purok" />
                            <InputError :message="form.errors.purok" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="street">Street</Label>
                            <Input id="street" type="text" required autofocus :tabindex="1" autocomplete="street" v-model="form.street" />
                            <InputError :message="form.errors.street" />
                        </div>
                    </div>

                    <div v-if="step == 3" class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        <div class="grid gap-2">
                            <Label for="occupation">Occupation</Label>
                            <Input id="occupation" type="text" required autofocus :tabindex="1" autocomplete="occupation" v-model="form.occupation" />
                            <InputError :message="form.errors.occupation" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="company_organization">Company/Organization</Label>
                            <Input
                                id="company_organization"
                                type="text"
                                autofocus
                                :tabindex="1"
                                autocomplete="purok"
                                v-model="form.company_organization"
                            />
                            <InputError :message="form.errors.company_organization" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="position">Position</Label>
                            <Input id="position" type="text" required autofocus :tabindex="1" autocomplete="position" v-model="form.position" />
                            <InputError :message="form.errors.position" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="office_business_address">Office/Business</Label>
                            <Input
                                id="office_business_address"
                                type="text"
                                autofocus
                                :tabindex="1"
                                autocomplete="office_business_address"
                                v-model="form.office_business_address"
                            />
                            <InputError :message="form.errors.office_business_address" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="office_landline">Landline</Label>
                            <Input
                                id="office_landline"
                                type="tel"
                                autofocus
                                :tabindex="1"
                                autocomplete="office_landline"
                                v-model="form.office_landline"
                            />
                            <InputError :message="form.errors.office_landline" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="office_email">Email</Label>
                            <Input id="office_email" type="email" autofocus :tabindex="1" autocomplete="office_email" v-model="form.office_email" />
                            <InputError :message="form.errors.office_email" />
                        </div>
                    </div>

                    <div v-if="step == 4" class="grid grid-cols-1 gap-6">
                        <div class="grid gap-2">
                            <Label for="signature">Selfie Photo</Label>
                            <div class="flex h-full w-full flex-col items-center justify-center space-y-2">
                                <!-- Image Display (Click to Open Camera) -->
                                <audio ref="captureSound" :src="captureSoundSrc"></audio>
                                <img
                                    v-if="cameraState === 'capture'"
                                    :src="photo ?? undefined"
                                    alt="Captured"
                                    class="w-full cursor-pointer object-cover"
                                    @click="startCamera"
                                />
                                <div
                                    v-if="cameraState === 'idle'"
                                    class="flex w-full cursor-pointer items-center justify-center space-x-2 rounded border border-zinc-400 bg-zinc-200/60 p-5 font-bold text-zinc-800 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-50"
                                    @click="startCamera"
                                >
                                    <icons.Camera />
                                    <span>Click to shoot yourself!</span>
                                </div>

                                <!-- Video Feed for Camera -->
                                <div v-if="cameraState === 'active'" class="h-full w-full">
                                    <video ref="videoRef" autoplay playsinline class="h-full w-full scale-x-[-1]"></video>
                                </div>
                                <!-- Canvas (Hidden, Used for Capture) -->
                                <canvas ref="canvas" class="hidden"></canvas>

                                <!-- Capture Button -->
                                <button
                                    v-if="cameraState === 'active'"
                                    @click="capturePhoto"
                                    type="button"
                                    class="mt-2 w-52 rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                                >
                                    Capture
                                </button>
                            </div>
                            <InputError :message="form.errors.photo" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="signature">Signature</Label>
                            <div class="flex flex-col items-start justify-start space-y-4">
                                <div>
                                    <VueSignaturePad
                                        ref="signature"
                                        height="200px"
                                        width="680px"
                                        maxWidth="3"
                                        :disabled="state.disabled"
                                        :options="{
                                            penColor: state.options.penColor,
                                            backgroundColor: state.options.backgroundColor,
                                        }"
                                        @end-stroke="handleSave('image/png')"
                                    />
                                </div>

                                <div class="flex w-full items-start justify-between">
                                    <InputError :message="form.errors.signature" />
                                    <div class="flex w-full justify-end space-x-5">
                                        <button type="button" class="w-24 rounded bg-blue-500 p-2 font-bold text-zinc-50" @click="handleUndo">
                                            Undo
                                        </button>
                                        <button
                                            type="button"
                                            class="w-24 rounded bg-zinc-950 p-2 font-bold text-zinc-50 dark:bg-zinc-600"
                                            @click="handleClear"
                                        >
                                            Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="step == 5" class="flex flex-col space-y-4">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4">
                            <div class="grid gap-2">
                                <Label for="signature">Gun Club</Label>
                                <SelectRoot v-model="newGunClub.gunclub_id">
                                    <SelectTrigger
                                        class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none uppercase shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                        aria-label="Customise options"
                                    >
                                        <SelectValue placeholder="" />
                                        <icons.ChevronDown class="h-3.5 w-3.5" />
                                    </SelectTrigger>

                                    <SelectPortal>
                                        <SelectContent
                                            class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                            :side-offset="5"
                                        >
                                            <SelectScrollUpButton
                                                class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                            >
                                                <icons.ArrowUp :size="16" />
                                            </SelectScrollUpButton>

                                            <SelectViewport class="p-[5px]">
                                                <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Gun Club </SelectLabel>
                                                <SelectGroup>
                                                    <SelectItem
                                                        v-for="(option, index) in filteredOptions"
                                                        :key="index"
                                                        class="relative flex h-[25px] cursor-default items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none uppercase select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                        :value="option.id"
                                                    >
                                                        <SelectItemIndicator
                                                            class="absolute left-0 inline-flex w-[25px] items-center justify-center uppercase"
                                                        >
                                                            <icons.Check :size="14" />
                                                        </SelectItemIndicator>
                                                        <SelectItemText class="uppercase">
                                                            {{ option.name }}
                                                        </SelectItemText>
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectViewport>

                                            <SelectScrollDownButton
                                                class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                            >
                                                <icons.ArrowDown :size="16" />
                                            </SelectScrollDownButton>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                                <InputError :message="form.errors.gunclubs" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="years_no">Years of Membership</Label>
                                <Input id="years_no" type="number" required autofocus1 :tabindex="1" v-model="newGunClub.years_no" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="model">Main Gun Club</Label>
                                <div class="flex items-center gap-2">
                                    <input
                                        id="is_main"
                                        type="checkbox"
                                        v-model="newGunClub.is_main"
                                        :tabindex="1"
                                        class="form-checkbox h-5 w-5 text-red-600"
                                    />
                                    <Label for="is_main">Check if yes</Label>
                                </div>
                            </div>
                            <div class="grid gap-2">
                                <Label for="button" class="text-transparent">Action</Label>
                                <Button type="button" @click="addGunClub" class="h-10 bg-zinc-700 text-white sm:w-52" :tabindex="4">
                                    Add
                                    <icons.Plus class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>

                        <table v-if="form.gunclubs.length > 0" class="mt-5 min-w-full table-auto border border-zinc-600 text-sm text-white">
                            <thead class="bg-zinc-800">
                                <tr>
                                    <th class="px-3 py-2 text-left">#</th>
                                    <th class="px-3 py-2 text-left">Gun Club</th>
                                    <th class="px-3 py-2 text-left">Membership Years</th>
                                    <th class="px-3 py-2 text-left">Main</th>
                                    <th class="px-3 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(gunclub, index) in form.gunclubs" :key="index" class="border-t border-zinc-600 uppercase">
                                    <td class="px-3 py-2">{{ index + 1 }}</td>
                                    <td class="px-3 py-2">
                                        {{ props.gunClubs.find((club) => club.id === gunclub.gunclub_id)?.name || 'Unknown' }}
                                    </td>
                                    <td class="px-3 py-2">{{ gunclub.years_no }}</td>
                                    <td class="px-3 py-2">{{ gunclub.is_main ? 'Yes' : 'No' }}</td>
                                    <td class="px-3 py-2">
                                        <Button
                                            type="button"
                                            @click="form.gunclubs.splice(index, 1)"
                                            class="bg-primary text-white hover:bg-primary/80"
                                        >
                                            <icons.X />
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="step == 6" class="flex flex-col space-y-4">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="Firearm Type">Firearm Type</Label>
                                <SelectRoot v-model="newFirearm.type">
                                    <SelectTrigger
                                        class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                        aria-label="Customise options"
                                    >
                                        <SelectValue placeholder="" />
                                        <icons.ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                                    </SelectTrigger>

                                    <SelectPortal>
                                        <SelectContent
                                            class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                            :side-offset="5"
                                        >
                                            <SelectScrollUpButton
                                                class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                            >
                                                <icons.ArrowUp :size="16" />
                                            </SelectScrollUpButton>

                                            <SelectViewport class="p-[5px]">
                                                <SelectLabel class="px-[25px] text-xs leading-[25px] font-medium"> Firearm Type </SelectLabel>
                                                <SelectGroup>
                                                    <SelectItem
                                                        v-for="(option, index) in firearmTypeOptions"
                                                        :key="index"
                                                        class="data-[highlighted]:text-green1 relative flex h-[25px] cursor-default items-center rounded-[3px] pr-[35px] pl-[25px] text-xs leading-none select-none hover:bg-zinc-300 data-[disabled]:pointer-events-none data-[highlighted]:outline-none hover:dark:bg-zinc-700"
                                                        :value="option"
                                                    >
                                                        <SelectItemIndicator class="absolute left-0 inline-flex w-[25px] items-center justify-center">
                                                            <Icon icon="radix-icons:check" />
                                                        </SelectItemIndicator>
                                                        <SelectItemText>
                                                            {{ option }}
                                                        </SelectItemText>
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectViewport>

                                            <SelectScrollDownButton
                                                class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                            >
                                                <icons.ArrowDown :size="16" />
                                            </SelectScrollDownButton>
                                        </SelectContent>
                                    </SelectPortal>
                                </SelectRoot>
                            </div>
                            <div class="grid gap-2">
                                <Label for="make">Make</Label>
                                <Input id="make" type="text" required autofocus :tabindex="1" v-model="newFirearm.make" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="model">Model</Label>
                                <Input id="make" type="text" required autofocus :tabindex="1" v-model="newFirearm.model" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="caliber">Caliber</Label>
                                <Input id="caliber" type="text" required autofocus :tabindex="1" v-model="newFirearm.caliber" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="serial_no">Serial No.</Label>
                                <Input id="serial_no" type="text" required autofocus :tabindex="1" v-model="newFirearm.serial_no" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="serial_no" class="text-transparent">Action</Label>
                                <Button type="button" @click="addFirearm" class="h-10 bg-zinc-700 text-white sm:w-52" :tabindex="4">
                                    Add
                                    <icons.Plus class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>

                        <table v-if="form.firearms.length > 0" class="mt-3 min-w-full table-auto border border-zinc-600 text-sm text-white">
                            <thead class="bg-zinc-800">
                                <tr>
                                    <th class="px-3 py-2 text-left">#</th>
                                    <th class="px-3 py-2 text-left">Type</th>
                                    <th class="px-3 py-2 text-left">Make</th>
                                    <th class="px-3 py-2 text-left">Model</th>
                                    <th class="px-3 py-2 text-left">Caliber</th>
                                    <th class="px-3 py-2 text-left">Serial No.</th>
                                    <th class="px-3 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(firearm, index) in form.firearms" :key="index" class="border-t border-zinc-600 uppercase">
                                    <td class="px-3 py-2">{{ index + 1 }}</td>
                                    <td class="px-3 py-2">{{ firearm.type }}</td>
                                    <td class="px-3 py-2">{{ firearm.make }}</td>
                                    <td class="px-3 py-2">{{ firearm.model }}</td>
                                    <td class="px-3 py-2">{{ firearm.caliber }}</td>
                                    <td class="px-3 py-2">{{ firearm.serial_no }}</td>
                                    <td class="px-3 py-2">
                                        <Button
                                            type="button"
                                            @click="form.firearms.splice(index, 1)"
                                            class="bg-primary text-white hover:bg-primary/80"
                                        >
                                            <icons.X />
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="step == 7" class="flex w-[680px] flex-col items-center justify-center">
                        <div v-if="form.wasSuccessful == false" class="rounded-md bg-zinc-200 p-4 text-lg dark:bg-zinc-800 dark:text-zinc-50">
                            <strong>ᡕᠵデᡁ᠊╾━💥 Double-check your details so everything hits the mark — no one likes a misfire.</strong>
                        </div>
                        <div v-if="form.wasSuccessful" class="mb-5 rounded-md bg-green-950 p-4 text-lg text-green-100">
                            <strong>Submission Successful!</strong>
                            <p class="text-basetext-justify mt-2">
                                Your information has been received and is now being processed. If you need to make any changes, a secure update link
                                has been sent to the email address you provided. Please check your inbox (and spam folder) for further instructions.
                            </p>
                        </div>
                        <ToastProvider>
                            <ToastRoot
                                v-model:open="successToastOpen"
                                class="data-[state=open]:animate-slideIn data-[state=closed]:animate-hide data-[swipe=end]:animate-swipeOut grid grid-cols-[auto_max-content] items-center gap-x-[15px] rounded-lg border bg-white p-[15px] shadow-sm [grid-template-areas:_'title_action'_'description_action'] data-[swipe=cancel]:translate-x-0 data-[swipe=cancel]:transition-[transform_200ms_ease-out] data-[swipe=move]:translate-x-[var(--reka-toast-swipe-move-x)] dark:bg-zinc-700"
                            >
                                <ToastTitle class="text-slate12 mb-[5px] text-sm font-medium [grid-area:_title]"> Submit Success!</ToastTitle>
                                <ToastDescription as-child>
                                    For any corrections or updates, please use the link that will be sent to your submitted email address.
                                </ToastDescription>
                                <ToastAction class="[grid-area:_action]" as-child alt-text="Goto schedule to undo">
                                    <button
                                        class="bg-green2 text-green11 shadow-green7 hover:shadow-green8 focus:shadow-green8 inline-flex h-[25px] items-center justify-center rounded-md px-[10px] text-xs leading-[25px] font-medium shadow-[inset_0_0_0_1px] hover:shadow-[inset_0_0_0_1px] focus:shadow-[0_0_0_2px]"
                                    >
                                        Close
                                    </button>
                                </ToastAction>
                            </ToastRoot>
                            <ToastViewport
                                class="fixed right-0 bottom-0 z-[2147483647] m-0 flex w-[390px] max-w-[100vw] list-none flex-col gap-[10px] p-[var(--viewport-padding)] outline-none [--viewport-padding:_25px]"
                            />
                        </ToastProvider>
                        <ToastProvider>
                            <ToastRoot
                                v-model:open="errorToastOpen"
                                class="data-[state=open]:animate-slideIn data-[state=closed]:animate-hide data-[swipe=end]:animate-swipeOut grid grid-cols-[auto_max-content] items-center gap-x-[15px] rounded-lg border bg-red-400 p-[15px] shadow-sm [grid-template-areas:_'title_action'_'description_action'] data-[swipe=cancel]:translate-x-0 data-[swipe=cancel]:transition-[transform_200ms_ease-out] data-[swipe=move]:translate-x-[var(--reka-toast-swipe-move-x)] dark:bg-red-400"
                            >
                                <ToastTitle class="text-slate12 mb-[5px] text-sm font-medium [grid-area:_title]"> Submit Error!</ToastTitle>
                                <ToastDescription as-child> {{ formErrorMessage }} </ToastDescription>
                                <ToastAction class="[grid-area:_action]" as-child alt-text="Goto schedule to undo">
                                    <button
                                        class="bg-green2 text-green11 shadow-green7 hover:shadow-green8 focus:shadow-green8 inline-flex h-[25px] items-center justify-center rounded-md px-[10px] text-xs leading-[25px] font-medium shadow-[inset_0_0_0_1px] hover:shadow-[inset_0_0_0_1px] focus:shadow-[0_0_0_2px]"
                                    >
                                        Close
                                    </button>
                                </ToastAction>
                            </ToastRoot>
                            <ToastViewport
                                class="fixed right-0 bottom-0 z-[2147483647] m-0 flex w-[390px] max-w-[100vw] list-none flex-col gap-[10px] p-[var(--viewport-padding)] outline-none [--viewport-padding:_25px]"
                            />
                        </ToastProvider>
                    </div>

                    <div
                        v-if="isSubmitSuccess == false"
                        class="mt-8 flex flex-col-reverse items-center justify-end space-y-4 space-x-0 sm:flex-row sm:space-y-0 sm:space-x-4"
                    >
                        <Button
                            type="button"
                            v-if="step > 0"
                            @click="prevStep()"
                            class="mt-4 h-10 w-full bg-zinc-400 text-white hover:bg-zinc-400 sm:mt-0 sm:w-52 dark:bg-zinc-700"
                            :tabindex="4"
                        >
                            <icons.ArrowLeft class="h-4 w-4" />
                            Previous
                        </Button>

                        <Button v-if="step < 7" type="button" @click="nextStep()" class="h-10 w-full text-white sm:w-52" :tabindex="4">
                            Next
                            <icons.ArrowRight class="h-4 w-4" />
                        </Button>
                        <Button v-if="step == 7" class="h-10 w-52 text-white" :tabindex="4" :disabled="form.processing">
                            <template v-if="form.processing">
                                <icons.LoaderCircle class="mr-2 h-4 w-4 animate-spin" />
                                Submitting…
                            </template>
                            <template v-else>
                                Submit
                                <icons.Check class="ml-2 h-4 w-4" />
                            </template>
                        </Button>
                    </div>
                    <Link v-if="form.wasSuccessful" :href="route('home')">
                        <Button
                            type="button"
                            @click="form.reset()"
                            class="mt-10 h-10 w-full bg-zinc-400 text-white hover:bg-zinc-400 sm:mt-0 sm:w-52 dark:bg-zinc-700"
                            :tabindex="4"
                        >
                            <icons.ArrowLeft class="h-4 w-4" />
                            Back to main
                        </Button>
                    </Link>
                </form>
            </div>
        </div>
    </div>
</template>
