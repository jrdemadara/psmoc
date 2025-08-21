<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, ChevronDown, LoaderCircle, Plus, X } from 'lucide-vue-next';
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
import { PropType, reactive, ref } from 'vue';

interface Firearm {
    id: number;
    type: string;
    make: string;
    model: string;
    caliber: string;
    serial_no: string;
}

interface Data {
    firearms: Firearm[];
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
    firearms: props.data.firearms.map((firearm) => ({
        id: firearm.id,
        type: firearm.type,
        make: firearm.make,
        model: firearm.model,
        caliber: firearm.caliber,
        serial_no: firearm.serial_no,
    })),
});

const formErrorMessage = ref<string>('');

const submit = () => {
    form.patch(route('update-firearms'), {
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

type FirearmField = string | null;

const newFirearm = reactive<Record<'id' | 'type' | 'make' | 'model' | 'caliber' | 'serial_no', FirearmField>>({
    id: null,
    type: null,
    make: null,
    model: null,
    caliber: null,
    serial_no: null,
});

const addFirearm = () => {
    const { id, type, make, model, caliber, serial_no } = newFirearm;

    if (type && make && model && caliber && serial_no) {
        form.firearms.push({
            id: Number(id) || 0,
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
</script>

<template>
    <div class="flex">
        <form @submit.prevent="submit" class="flex w-full flex-col space-y-10">
            <h4 class="text-xl font-semibold text-zinc-50">Personal Details</h4>

            <div class="flex flex-col space-y-4 text-zinc-50">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3">
                    <div class="grid gap-2">
                        <Label for="Firearm Type">Firearm Type</Label>
                        <SelectRoot v-model="newFirearm.type">
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
                                    <SelectScrollUpButton
                                        class="flex h-[25px] cursor-default items-center justify-center bg-zinc-200 dark:bg-zinc-700"
                                    >
                                        <ArrowUp :size="16" />
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
                                        <ArrowDown :size="16" />
                                    </SelectScrollDownButton>
                                </SelectContent>
                            </SelectPortal>
                        </SelectRoot>
                    </div>
                    <div class="grid gap-2">
                        <Label for="make">Make</Label>
                        <Input id="make" type="text" class="uppercase" autofocus v-model="newFirearm.make" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="model">Model</Label>
                        <Input id="make" type="text" class="uppercase" autofocus v-model="newFirearm.model" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="caliber">Caliber</Label>
                        <Input id="caliber" type="text" class="uppercase" autofocus v-model="newFirearm.caliber" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="serial_no">Serial No.</Label>
                        <Input id="serial_no" type="text" class="uppercase" autofocus v-model="newFirearm.serial_no" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="serial_no" class="text-transparent">Action</Label>
                        <Button type="button" @click="addFirearm" class="h-10 bg-zinc-700 text-white sm:w-52" :tabindex="4">
                            Add
                            <Plus class="h-4 w-4" />
                        </Button>
                    </div>
                </div>

                <div class="mt-3 max-h-64 overflow-y-auto border border-zinc-600">
                    <table v-if="form.firearms.length > 0" class="min-w-full table-auto text-xs text-white">
                        <thead class="sticky top-0 bg-zinc-800">
                            <tr>
                                <th class="px-3 py-2 text-left">#</th>
                                <th class="px-3 py-2 text-left">Type</th>
                                <th class="px-3 py-2 text-left">Make</th>
                                <th class="px-3 py-2 text-left">Model</th>
                                <th class="px-3 py-2 text-left">Caliber</th>
                                <th class="px-3 py-2 text-left">Serial</th>
                                <th class="px-3 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(firearm, index) in form.firearms" :key="index" class="border-t border-zinc-600 text-zinc-50 uppercase">
                                <td class="px-3 py-2">{{ firearm.id }}</td>
                                <td class="px-3 py-2">{{ firearm.type }}</td>
                                <td class="px-3 py-2">{{ firearm.make }}</td>
                                <td class="px-3 py-2">{{ firearm.model }}</td>
                                <td class="px-3 py-2">{{ firearm.caliber }}</td>
                                <td class="px-3 py-2">{{ firearm.serial_no }}</td>
                                <td class="px-3 py-2">
                                    <Button type="button" @click="form.firearms.splice(index, 1)" class="bg-primary text-white hover:bg-primary/80">
                                        <X />
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
