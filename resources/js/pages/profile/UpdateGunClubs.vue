<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle, Plus, X } from 'lucide-vue-next';
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
import { computed, PropType, reactive, ref, watch } from 'vue';

interface Gunclub {
    id: number;
    name: string;
}

interface MemberGunclub {
    id: number;
    gunclub: Gunclub;
    years_no: number;
    is_main: boolean;
}

interface Data {
    gunclub: Gunclub[];
    member_gunclubs: MemberGunclub[];
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
    gunclubs: props.data.member_gunclubs.map((member) => ({
        id: member.id, //take a look at this. The expected type comes from property 'id' which is declared here on type '{ id: number; gunclub_id: number; years_no: number; is_main: boolean; }'
        gunclub_id: member.gunclub.id,
        years_no: member.years_no,
        is_main: !!member.is_main,
    })),
});

const formErrorMessage = ref<string>('');

const submit = () => {
    form.patch(route('update-gunclubs'), {
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

const newGunClub = reactive({
    id: undefined as number | undefined,
    gunclub_id: undefined as number | undefined,
    years_no: undefined as number | undefined,
    is_main: false,
});

const addGunClub = () => {
    const { id, gunclub_id, years_no, is_main } = newGunClub;

    // Ensure all fields are valid
    if (gunclub_id !== undefined && years_no !== undefined) {
        if (id === undefined) {
            newGunClub.id = 0;
        }

        form.gunclubs.push({
            id: id ?? 0,
            gunclub_id,
            years_no,
            is_main,
        });

        // Reset the newGunClub fields
        Object.assign(newGunClub, {
            id: null,
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
    return props.data.gunclub.filter((club) => {
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
</script>

<template>
    <div class="flex">
        <form @submit.prevent="submit" class="flex w-full flex-col space-y-10">
            <h4 class="text-xl font-semibold text-zinc-50">Gunclubs</h4>

            <div class="flex flex-col space-y-4 text-zinc-50">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4">
                    <div class="grid gap-2">
                        <Label for="gunclub">Gun Club</Label>
                        <SelectRoot v-model="newGunClub.gunclub_id">
                            <SelectTrigger
                                class="data-[placeholder]:text-green9 inline-flex h-10 min-w-[160px] items-center justify-between gap-[5px] rounded-lg border px-[15px] text-xs leading-none uppercase shadow-sm outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black dark:bg-input/30"
                                aria-label="Customise options"
                            >
                                <SelectValue placeholder="" />
                                <ChevronDown class="h-3.5 w-3.5" />
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
                                                    <Check :size="14" />
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
                                        <ArrowDown :size="16" />
                                    </SelectScrollDownButton>
                                </SelectContent>
                            </SelectPortal>
                        </SelectRoot>
                        <InputError :message="form.errors.gunclubs" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="years_no">Years of Membership</Label>
                        <Input id="years_no" type="number" autofocus1 v-model="newGunClub.years_no" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="model" class="hidden">Main Gun Club</Label>
                        <div class="flex items-center gap-2">
                            <input id="is_main" type="checkbox" v-model="newGunClub.is_main" class="form-checkbox h-5 w-5 text-red-600" />
                            <Label for="is_main">Main Gun Club</Label>
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label for="button" class="text-transparent">Action</Label>
                        <Button type="button" @click="addGunClub" class="h-10 bg-zinc-700 text-white sm:w-52">
                            Add
                            <Plus class="h-4 w-4" />
                        </Button>
                    </div>
                </div>

                <table v-if="form.gunclubs.length > 0" class="mt-5 min-w-full table-auto border border-zinc-600 text-sm text-white">
                    <thead class="bg-zinc-800">
                        <tr>
                            <th class="px-3 py-2 text-left">#</th>
                            <th class="px-3 py-2 text-left">Club</th>
                            <th class="px-3 py-2 text-left">Years</th>
                            <th class="px-3 py-2 text-left">Main</th>
                            <th class="px-3 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(gunclub, index) in form.gunclubs" :key="index" class="border-t border-zinc-600 text-zinc-50 uppercase">
                            <td class="px-3 py-2">{{ gunclub.id }}</td>
                            <td class="px-3 py-2">
                                {{ props.data.gunclub.find((club) => club.id === gunclub.gunclub_id)?.name || 'Unknown' }}
                            </td>
                            <td class="px-3 py-2">{{ gunclub.years_no }}</td>
                            <td class="px-3 py-2">{{ gunclub.is_main ? 'Yes' : 'No' }}</td>
                            <td class="px-3 py-2">
                                <Button type="button" @click="form.gunclubs.splice(index, 1)" class="bg-primary text-white hover:bg-primary/80">
                                    <X />
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
