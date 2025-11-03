<script setup lang="ts">
import { home } from '@/routes';
import Layout from '../components/layout/Layout.vue';
import Hero from './homepage/Hero.vue';
import Marketplace from './homepage/Marketplace.vue';
</script>

<template>
    <Layout :link="home.url()">
        <Hero />
        <Marketplace />
    </Layout>
</template>
