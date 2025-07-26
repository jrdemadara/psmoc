<script setup lang="ts">
import AppLogoSecondary from '@/components/AppLogoSecondary.vue';
import { Link } from '@inertiajs/vue3';
import { Crosshair, Facebook, Instagram, Mail, MoveRight, Phone, Smartphone, Youtube } from 'lucide-vue-next';
import welcome from '../../assets/images/welcome.jpg';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] text-[#1b1b18] dark:bg-black">
        <header class="flex w-full flex-col">
            <div class="flex space-x-8 border border-b px-8 py-2 text-zinc-400">
                <div class="flex items-center justify-center space-x-2">
                    <Smartphone :size="22" class="text-zinc-400" />
                    <strong>(Globe) +63 975 3577 935</strong>
                </div>
                <div class="flex items-center justify-center space-x-2">
                    <Phone :size="22" class="text-zinc-400" />
                    <strong>(02) 8995 4941</strong>
                </div>
                <div class="flex items-center justify-center space-x-2">
                    <Mail :size="22" class="text-zinc-400" />
                    <strong>psmoc.main@gmail.com</strong>
                </div>
            </div>
            <div class="flex py-5">
                <div class="flex w-fit items-center justify-center space-x-2 px-16">
                    <img src="../../assets/images/logo.png" alt="Logo" class="h-14 w-auto" />
                    <AppLogoSecondary class="w-72 text-zinc-950 dark:text-zinc-50" />
                </div>
                <nav class="flex w-full items-center justify-center gap-2 text-sm font-semibold text-[#1b1b18] uppercase dark:text-[#EDEDEC]">
                    <div class="group relative">
                        <button class="inline-block px-5 py-1.5 uppercase hover:text-red-500">Register</button>
                        <div
                            class="absolute z-10 hidden min-w-[150px] rounded bg-white text-[#1b1b18] shadow-lg group-hover:block dark:bg-zinc-900 dark:text-[#EDEDEC]"
                        >
                            <Link :href="route('register-member')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-primary">Member</Link>
                            <Link :href="route('register-member')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-primary"
                                >Match Officer</Link
                            >
                            <Link :href="route('register-gunclub')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-primary">Gun Club</Link>
                        </div>
                    </div>
                    <Link :href="route('login')" class="inline-block px-5 py-1.5 hover:text-red-500">Sanctioned Matches</Link>
                    <div class="group relative">
                        <button class="inline-block px-5 py-1.5 uppercase hover:text-red-500">Rulebook</button>
                        <div
                            class="absolute z-10 hidden min-w-[150px] rounded bg-white text-[#1b1b18] shadow-lg group-hover:block dark:bg-zinc-900 dark:text-[#EDEDEC]"
                        >
                            <Link :href="route('register-member')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-primary"
                                >PSMOC Rulebook</Link
                            >
                            <Link :href="route('register-member')" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-primary"
                                >ISMOC Airsoft Rulebook</Link
                            >
                        </div>
                    </div>
                    <Link :href="route('login')" class="inline-block px-5 py-1.5 hover:text-red-500">Gun Clubs</Link>
                    <Link :href="route('login')" class="inline-block px-5 py-1.5 hover:text-red-500">Match Officers</Link>
                    <Link :href="route('login')" class="inline-block px-5 py-1.5 hover:text-red-500">About</Link>
                </nav>

                <div class="flex w-fit items-center justify-end space-x-3 px-16">
                    <div class="rounded-full bg-primary/40 p-2 hover:bg-primary/30">
                        <Facebook color="white" :size="22" />
                    </div>
                    <div class="rounded-full bg-primary/40 p-2 hover:bg-primary/30">
                        <Instagram color="white" :size="22" />
                    </div>
                    <div class="rounded-full bg-primary/40 p-2 hover:bg-primary/30">
                        <Youtube color="white" :size="22" />
                    </div>
                    <Link
                        :href="route('register-member')"
                        class="flex h-10 w-32 items-center justify-center rounded-full bg-primary font-bold text-white hover:bg-primary/80"
                    >
                        <strong>Join Us</strong>
                    </Link>
                </div>
            </div>
        </header>
        <div
            class="relative flex h-screen w-full items-center justify-center bg-cover bg-center text-center text-white"
            :style="`background-image: url(${welcome})`"
        >
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/80 to-black/20"></div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col items-center justify-center space-y-5 rounded-lg p-8">
                <img src="../../assets/images/header.svg" alt="" class="w-1/3" />
                <p class="text-xl text-zinc-400">
                    No gimmicks. Just pure firepower. At PSMOC, we take shooting to the extreme — <br />
                    live matches, advanced setups, and raw intensity that hits you like recoil.
                </p>

                <Link
                    :href="route('register-member')"
                    class="flex h-14 w-48 items-center justify-between space-x-2 rounded-full bg-primary px-5 hover:bg-primary/80"
                >
                    <MoveRight color="white" :size="24" />
                    <span class="text-lg font-bold text-white">Join Us</span>
                    <Crosshair color="white" :size="24" class="animate-spin" />
                </Link>
            </div>
        </div>
    </div>
</template>
