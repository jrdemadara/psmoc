<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ArrowDown, ArrowUp, ChevronDown, LoaderCircle } from 'lucide-vue-next';
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
import { onMounted, PropType, Ref, ref, watch } from 'vue';

interface Data {
    region: string;
    province: string;
    city_municipality: string;
    barangay: string;
    street: string;
    purok: string;
}

const props = defineProps({
    resubmit: {
        type: Boolean,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
    data: {
        type: Object as PropType<Data>,
        required: true,
    },
});

const form = useForm({
    resubmit: props.resubmit,
    token: props.token,
    region: props.data.region,
    province: props.data.province,
    city_municipality: props.data.city_municipality,
    barangay: props.data.barangay,
    purok: props.data.purok,
    street: props.data.street,
});

const formErrorMessage = ref<string>('');

const submit = () => {
    form.patch(route('update-address'), {
        onSuccess: () => {
            popSuccessToast();
            formErrorMessage.value = '';
        },
        onError: (errors) => {
            formErrorMessage.value = Object.values(errors)[0]?.[0] ?? 'Something went wrong';
        },
    });
};

const successToastOpen = ref(false);
const timerRef = ref(0);

function popSuccessToast() {
    successToastOpen.value = false;
    window.clearTimeout(timerRef.value);
    timerRef.value = window.setTimeout(() => {
        successToastOpen.value = true;
    }, 100);
}

const isRegionLoading = ref<boolean>(false);
const isProvinceLoading = ref<boolean>(false);
const isCityLoading = ref<boolean>(false);
const isBarangayLoading = ref<boolean>(false);

const regionsList = ref<Location[]>([]);
const provincesList = ref<Location[]>([]);
const citiesList = ref<Location[]>([]);
const barangaysList = ref<Location[]>([]);

const isProvinceRequired = ref<boolean>(false);

interface Location {
    code: string;
    name: string;
}

function getAddressCodeByName(obj: Ref<Location[]>, name: string): string {
    const match = obj.value.find((loc) => loc.name.trim() === name.trim());
    return match ? match.code : '';
}

const handleRegionChange = async (regionName: string) => {
    const code = getAddressCodeByName(regionsList, regionName);
    if (!code) return;

    if (code === '1300000000') {
        isProvinceRequired.value = false;
        isCityLoading.value = true;

        try {
            const { data } = await axios.get<Location[]>(`/api/proxy/municipalities/${code}`);
            citiesList.value = data.filter((item) => item.code !== '1380600000');
        } catch (e) {
            console.error('Error loading cities:', e);
        } finally {
            isCityLoading.value = false;
        }
    } else {
        isProvinceLoading.value = true;

        try {
            const { data } = await axios.get<Location[]>(`/api/proxy/provinces/${code}`);
            provincesList.value = data;
            handleProvinceChange(props.data.province);
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
    form.province = provinceName;

    const code = getAddressCodeByName(provincesList, provinceName);
    if (!code) return;

    isCityLoading.value = true;

    try {
        const { data } = await axios.get<Location[]>(`/api/proxy/municipalities/${code}`);
        citiesList.value = data;
        handleCityChange(props.data.city_municipality);
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
        const { data } = await axios.get<Location[]>(`/api/proxy/barangays/${code}`);
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

onMounted(() => {
    isRegionLoading.value = true;
    axios
        .get('/api/proxy/regions')
        .then(function (response) {
            regionsList.value = response.data;
            isRegionLoading.value = false;
            handleRegionChange(props.data.region);
        })
        .catch(function () {
            isRegionLoading.value = false;
        });
});
</script>

<template>
    <div class="flex">
        <form @submit.prevent="submit" class="flex w-full flex-col space-y-10">
            <h4 class="text-xl font-semibold text-zinc-50">Address Information</h4>

            <div class="grid grid-cols-1 gap-6 text-zinc-50 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <div class="grid gap-2">
                    <Label for="street">Region</Label>
                    <SelectRoot v-model="form.region">
                        <SelectTrigger
                            class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                            aria-label="Customise options"
                        >
                            <SelectValue placeholder="" />
                            <ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                :side-offset="5"
                            >
                                <SelectScrollUpButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowUp :size="16" />
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

                                <SelectScrollDownButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowDown :size="16" />
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
                            <ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                :side-offset="5"
                            >
                                <SelectScrollUpButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowUp :size="16" />
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

                                <SelectScrollDownButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowDown :size="16" />
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
                            <ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                :side-offset="5"
                            >
                                <SelectScrollUpButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowUp :size="16" />
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

                                <SelectScrollDownButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowDown :size="16" />
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
                            <ChevronDown icon="radix-icons:chevron-down" class="h-3.5 w-3.5" />
                        </SelectTrigger>

                        <SelectPortal>
                            <SelectContent
                                class="data-[side=top]:animate-slideDownAndFade data-[side=right]:animate-slideLeftAndFade data-[side=bottom]:animate-slideUpAndFade data-[side=left]:animate-slideRightAndFade z-[100] min-w-[160px] rounded-lg border bg-zinc-100 shadow-sm will-change-[opacity,transform] dark:bg-zinc-800"
                                :side-offset="5"
                            >
                                <SelectScrollUpButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowUp :size="16" />
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

                                <SelectScrollDownButton class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700">
                                    <ArrowDown :size="16" />
                                </SelectScrollDownButton>
                            </SelectContent>
                        </SelectPortal>
                    </SelectRoot>
                    <InputError :message="form.errors.barangay" />
                </div>
                <div class="grid gap-2">
                    <Label for="purok">Purok</Label>
                    <Input id="purok" type="text" class="capitalize" required autofocus autocomplete="purok" v-model="form.purok" />
                    <InputError :message="form.errors.purok" />
                </div>
                <div class="grid gap-2">
                    <Label for="street">Street</Label>
                    <Input id="street" type="text" class="capitalize" required autofocus autocomplete="street" v-model="form.street" />
                    <InputError :message="form.errors.street" />
                </div>
            </div>

            <Button class="h-10 text-white lg:w-52" :disabled="form.processing">
                <template v-if="form.processing">
                    <LoaderCircle class="size-5 animate-spin" />
                </template>
                <template v-else> Submit Changes </template>
            </Button>
        </form>
    </div>
    <ToastProvider>
        <ToastRoot
            v-model:open="successToastOpen"
            class="data-[state=open]:animate-slideIn data-[state=closed]:animate-hide data-[swipe=end]:animate-swipeOut grid grid-cols-[auto_max-content] items-center gap-x-[15px] rounded-lg border bg-green-600 p-[15px] shadow-sm [grid-template-areas:_'title_action'_'description_action'] data-[swipe=cancel]:translate-x-0 data-[swipe=cancel]:transition-[transform_200ms_ease-out] data-[swipe=move]:translate-x-[var(--reka-toast-swipe-move-x)]"
        >
            <ToastTitle class="mb-[5px] text-sm font-medium [grid-area:_title]"> Submit Success!</ToastTitle>
            <ToastDescription as-child>
                <p>Your membership profile has been successfully updated.</p>
            </ToastDescription>
            <ToastAction class="[grid-area:_action]" as-child alt-text="Goto schedule to undo">
                <button
                    class="inline-flex h-[25px] items-center justify-center rounded-md px-[10px] text-xs leading-[25px] font-medium shadow-[inset_0_0_0_1px] hover:shadow-[inset_0_0_0_1px] focus:shadow-[0_0_0_2px]"
                >
                    Close
                </button>
            </ToastAction>
        </ToastRoot>
        <ToastViewport
            class="fixed right-0 bottom-0 z-[2147483647] m-0 flex w-[390px] max-w-[100vw] list-none flex-col gap-[10px] p-[var(--viewport-padding)] outline-none [--viewport-padding:_25px]"
        />
    </ToastProvider>
</template>
