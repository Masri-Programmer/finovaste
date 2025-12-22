<script setup lang="ts">
import AddressController from '@/actions/App/Http/Controllers/Settings/AddressController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Layout from '@/components/layout/Layout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { setPrimary as setPrimaryAddress } from '@/routes/addresses';
import { type Address } from '@/types';
import { Form, Head, router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { CheckCircle2, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    addresses: Address[];
    status?: string;
}

defineProps<Props>();

const isDialogOpen = ref(false);
const editingAddress = ref<Address | null>(null);

const openCreateDialog = () => {
    editingAddress.value = null;
    isDialogOpen.value = true;
};

const openEditDialog = (address: Address) => {
    editingAddress.value = address;
    isDialogOpen.value = true;
};

const deleteAddress = (address: Address) => {
    if (confirm(trans('address.confirm_delete'))) {
        router.delete(AddressController.destroy.url({ address: address.id }));
    }
};

const setPrimary = (address: Address) => {
    router.patch(setPrimaryAddress({ address: address.id }));
};
</script>

<template>
    <Layout>
        <Head :title="$t('address.title')" />
        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <div class="flex items-center justify-between gap-4">
                    <HeadingSmall
                        :title="$t('address.title')"
                        :description="$t('address.description')"
                    />
                    <Button @click="openCreateDialog" class="shrink-0">
                        <Plus class="mr-2 h-4 w-4" />
                        {{ $t('address.add_new') }}
                    </Button>
                </div>

                <div
                    v-if="addresses.length === 0"
                    class="rounded-lg border-2 border-dashed bg-muted/50 py-12 text-center"
                >
                    <p class="text-muted-foreground">
                        {{ $t('address.no_addresses') }}
                    </p>
                </div>

                <div v-else class="grid gap-4 sm:grid-cols-2">
                    <Card
                        v-for="address in addresses"
                        :key="address.id"
                        :class="{
                            'border-primary ring-1 ring-primary/20':
                                address.is_primary,
                        }"
                    >
                        <CardHeader
                            class="flex flex-row items-start justify-between space-y-0 pb-2"
                        >
                            <div class="flex flex-col gap-1">
                                <CardTitle class="text-sm font-medium">
                                    {{ address.street }}
                                </CardTitle>
                                <Badge
                                    v-if="address.is_primary"
                                    variant="secondary"
                                    class="h-4 w-fit px-1.5 py-0 text-[10px]"
                                >
                                    {{ $t('address.primary') }}
                                </Badge>
                            </div>
                            <div class="flex space-x-1">
                                <Button
                                    v-if="!address.is_primary"
                                    variant="ghost"
                                    size="icon"
                                    @click="setPrimary(address)"
                                    :title="$t('address.set_primary')"
                                >
                                    <CheckCircle2
                                        class="h-4 w-4 text-muted-foreground"
                                    />
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    @click="openEditDialog(address)"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="text-destructive"
                                    @click="deleteAddress(address)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-xs text-muted-foreground">
                                {{ address.city
                                }}{{
                                    address.state ? ', ' + address.state : ''
                                }}
                                {{ address.zip }}<br />
                                {{ address.country }}
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <Dialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>{{
                            editingAddress
                                ? $t('address.edit')
                                : $t('address.add_new')
                        }}</DialogTitle>
                        <DialogDescription>
                            {{ $t('address.description') }}
                        </DialogDescription>
                    </DialogHeader>

                    <Form
                        v-bind="
                            editingAddress
                                ? AddressController.update.form({
                                      address: editingAddress.id,
                                  })
                                : AddressController.store.form()
                        "
                        @success="isDialogOpen = false"
                        class="space-y-4"
                        v-slot="{ errors, processing }"
                    >
                        <div class="grid gap-2">
                            <Label for="street">{{
                                $t('address.street')
                            }}</Label>
                            <Input
                                id="street"
                                name="street"
                                :default-value="editingAddress?.street"
                                required
                            />
                            <InputError :message="errors.street" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="city">{{
                                    $t('address.city')
                                }}</Label>
                                <Input
                                    id="city"
                                    name="city"
                                    :default-value="editingAddress?.city"
                                    required
                                />
                                <InputError :message="errors.city" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="state">{{
                                    $t('address.state')
                                }}</Label>
                                <Input
                                    id="state"
                                    name="state"
                                    :default-value="editingAddress?.state"
                                    required
                                />
                                <InputError :message="errors.state" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="zip">{{ $t('address.zip') }}</Label>
                                <Input
                                    id="zip"
                                    name="zip"
                                    :default-value="editingAddress?.zip"
                                    required
                                />
                                <InputError :message="errors.zip" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="country">{{
                                    $t('address.country')
                                }}</Label>
                                <Input
                                    id="country"
                                    name="country"
                                    :default-value="editingAddress?.country"
                                    required
                                />
                                <InputError :message="errors.country" />
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 py-2">
                            <Checkbox
                                id="is_primary"
                                name="is_primary"
                                :default-checked="editingAddress?.is_primary"
                            />
                            <Label
                                for="is_primary"
                                class="cursor-pointer text-sm leading-none font-medium"
                            >
                                {{ $t('address.is_primary') }}
                            </Label>
                        </div>

                        <div class="flex justify-end space-x-2 pt-4">
                            <Button
                                type="button"
                                variant="outline"
                                @click="isDialogOpen = false"
                            >
                                {{ $t('address.cancel') }}
                            </Button>
                            <Button type="submit" :disabled="processing">
                                {{ $t('address.save') }}
                            </Button>
                        </div>
                    </Form>
                </DialogContent>
            </Dialog>
        </SettingsLayout>
    </Layout>
</template>
