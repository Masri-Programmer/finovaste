<script setup lang="ts">
import FaqContent from '@/components/FaqContent.vue';
import Layout from '@/components/layout/Layout.vue';
</script>

<template>
    <Layout :title="$t('faq.meta_title')">
        <FaqContent />
    </Layout>
</template>
