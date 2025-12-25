<script setup lang="ts">
import FaqContent from '@/components/FaqContent.vue';
import Layout from '@/components/layout/Layout.vue';
import { home } from '@/routes';
import Home from './homepage/Home.vue';
</script>

<template>
    <Layout :link="home.url()">
        <Home />
        <FaqContent />
    </Layout>
</template>
