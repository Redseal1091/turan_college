<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import TopHeader from '@/Components/layout/TopHeader.vue';
import Footer from '@/Components/layout/Footer.vue';
import ApplyModal from '@/Components/shared/ApplyModal.vue';

defineProps<{
    title?: string;
}>();

const page = usePage();
</script>

<template>
    <div class="min-h-screen flex flex-col bg-white text-slate-800 antialiased selection:bg-[#0082C9]/20 selection:text-slate-900">
        <Head :title="title" />
        
        <!-- 1. Top Transparent Header Bar -->
        <TopHeader />

        <!-- 2. Main Page Content (Begins with Intro Block containing the Main Header) -->
        <main class="flex-1">
            <Transition name="page" mode="out-in" appear>
                <div :key="page.component" class="w-full">
                    <slot />
                </div>
            </Transition>
        </main>

        <Footer />

        <!-- Global Unified Online Application Modal (up to 10 files) -->
        <ApplyModal />
    </div>
</template>
